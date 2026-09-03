<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

/**
 * Turns "how many rows per day/month for the last N periods" into a single
 * grouped query, replacing the one-COUNT-per-period loops the dashboards used
 * to run (7 days x 4 metrics was 28 round trips per page load).
 *
 * Results are zero-filled, so a period with no rows still gets a 0 and each
 * returned series lines up index-for-index with the returned labels.
 */
final class TimeSeriesCounts
{
    /**
     * Daily counts for the last $days days, oldest first.
     *
     * @param  array<string, array{0: string, 1: string|array<int, string>}>  $buckets  series name => [column, value(s)]
     * @return array{labels: array<int, string>, series: array<string, array<int, int>>}
     */
    public static function daily(Builder $query, int $days, array $buckets = [], string $column = 'created_at'): array
    {
        $first = CarbonImmutable::today()->subDays(max($days, 1) - 1);

        $periods = [];
        for ($i = 0; $i < max($days, 1); $i++) {
            $day = $first->addDays($i);
            $periods[$day->format('Y-m-d')] = $day->format('D');
        }

        self::assertSafeIdentifier($column);

        return self::run(
            query: $query,
            periods: $periods,
            column: $column,
            buckets: $buckets,
            groupExpression: "DATE({$column})",   // same syntax on MySQL and SQLite
            start: $first->startOfDay(),
            end: CarbonImmutable::today()->endOfDay(),
        );
    }

    /**
     * Monthly counts for the last $months months, oldest first.
     *
     * @param  array<string, array{0: string, 1: string|array<int, string>}>  $buckets  series name => [column, value(s)]
     * @return array{labels: array<int, string>, series: array<string, array<int, int>>}
     */
    public static function monthly(Builder $query, int $months, array $buckets = [], string $column = 'created_at'): array
    {
        $first = CarbonImmutable::today()->startOfMonth()->subMonths(max($months, 1) - 1);

        $periods = [];
        for ($i = 0; $i < max($months, 1); $i++) {
            $month = $first->addMonths($i);
            $periods[$month->format('Y-m')] = $month->format('M');
        }

        self::assertSafeIdentifier($column);

        $groupExpression = $query->getConnection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";

        return self::run(
            query: $query,
            periods: $periods,
            column: $column,
            buckets: $buckets,
            groupExpression: $groupExpression,
            start: $first->startOfDay(),
            end: CarbonImmutable::today()->endOfMonth()->endOfDay(),
        );
    }

    /**
     * @param  array<string, string>  $periods  period key => chart label
     * @param  array<string, array{0: string, 1: string|array<int, string>}>  $buckets
     * @return array{labels: array<int, string>, series: array<string, array<int, int>>}
     */
    private static function run(
        Builder $query,
        array $periods,
        string $column,
        array $buckets,
        string $groupExpression,
        CarbonImmutable $start,
        CarbonImmutable $end,
    ): array {
        $selects  = [$groupExpression.' as period_key', 'COUNT(*) as total_count'];
        $bindings = [];

        foreach ($buckets as $name => [$bucketColumn, $value]) {
            self::assertSafeIdentifier($name);
            self::assertSafeIdentifier($bucketColumn);

            // A bucket may match several values (e.g. `completed` + `visited`
            // both count as a completed booking).
            $values       = array_values((array) $value);
            $placeholders = implode(', ', array_fill(0, count($values), '?'));

            // COUNT over a CASE counts only matching rows and, unlike SUM,
            // returns 0 instead of NULL when nothing matches.
            $selects[]  = "COUNT(CASE WHEN {$bucketColumn} IN ({$placeholders}) THEN 1 END) as bucket_{$name}";
            array_push($bindings, ...$values);
        }

        $rows = $query->clone()
            ->selectRaw(implode(', ', $selects), $bindings)
            ->whereBetween($column, [$start, $end])
            ->groupBy('period_key')
            ->get()
            ->keyBy(fn ($row) => (string) $row->period_key);

        $series = ['total' => []];
        foreach (array_keys($buckets) as $name) {
            $series[$name] = [];
        }

        foreach (array_keys($periods) as $key) {
            $row = $rows->get($key);

            $series['total'][] = (int) ($row->total_count ?? 0);
            foreach (array_keys($buckets) as $name) {
                $series[$name][] = (int) ($row->{'bucket_'.$name} ?? 0);
            }
        }

        return [
            'labels' => array_values($periods),
            'series' => $series,
        ];
    }

    /**
     * Series names and column names are interpolated into raw SQL, so they must
     * be developer-supplied identifiers rather than anything request-derived.
     */
    private static function assertSafeIdentifier(string $identifier): void
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException("Unsafe SQL identifier: {$identifier}");
        }
    }
}
