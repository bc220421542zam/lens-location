<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    private const COMMISSION_RATE = 0.10;

    public function index(Request $request): View
    {
        $request->validate([
            'search'     => 'nullable|string|max:255',
            'status'     => 'nullable|in:paid,pending,failed',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
        ]);

        $transactions = Transaction::where('customer_id', auth()->id())
            ->with('booking.location')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->string('search').'%';
                $q->where('jazzcash_txn_ref', 'like', $term)
                    ->orWhereHas('booking.location', fn ($q) => $q->where('title', 'like', $term));
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('min_amount'), fn ($q) => $q->where('amount', '>=', $request->float('min_amount')))
            ->when($request->filled('max_amount'), fn ($q) => $q->where('amount', '<=', $request->float('max_amount')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.payments', compact('transactions'));
    }

    public function pay(Booking $booking): View
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $orderRef = 'ORD' . now()->format('YmdHis') . $booking->id;
        $amount = $booking->total_price;

        Transaction::create([
            'booking_id'       => $booking->id,
            'customer_id'      => $booking->customer_id,
            'owner_id'         => $booking->location->user_id,
            'amount'           => $amount,
            'owner_earning'    => $amount * (1 - self::COMMISSION_RATE),
            'jazzcash_txn_ref' => $orderRef,
            'status'           => 'pending',
        ]);

        $data = $this->buildJazzCashPayload($booking, $orderRef);

        return view('customer.payments.redirect', [
            'data' => $data,
            'url'  => config('services.jazzcash.url'),
        ]);
    }

    private function buildJazzCashPayload(Booking $booking, string $orderRef): array
    {
        $txnDateTime = now()->format('YmdHis');
        $expiry = now()->addMinutes(30)->format('YmdHis');
        $amount = (string) (int) round($booking->total_price * 100);

        $data = [
            'pp_Version'           => '1.1',
            'pp_TxnType'           => '',
            'pp_Language'          => 'EN',
            'pp_MerchantID'        => config('services.jazzcash.merchant_id'),
            'pp_SubMerchantID'     => '',
            'pp_Password'          => config('services.jazzcash.password'),
            'pp_BankID'            => '',
            'pp_ProductID'         => '',
            'pp_TxnRefNo'          => $orderRef,
            'pp_Amount'            => $amount,
            'pp_TxnCurrency'       => 'PKR',
            'pp_TxnDateTime'       => $txnDateTime,
            'pp_BillReference'     => $orderRef,
            'pp_Description'       => 'Booking payment #' . $booking->id,
            'pp_TxnExpiryDateTime' => $expiry,
            'pp_ReturnURL'         => route('customer.payments.callback'),
            'ppmpf_1'              => '',
            'ppmpf_2'              => '',
            'ppmpf_3'              => '',
            'ppmpf_4'              => '',
            'ppmpf_5'              => '',
        ];

        ksort($data);
        $stringToHash = config('services.jazzcash.salt') . '&' . implode('&', $data);
        $data['pp_SecureHash'] = strtoupper(hash_hmac('sha256', $stringToHash, config('services.jazzcash.salt')));

        return $data;
    }

    public function callback(Request $request): RedirectResponse
    {
        $orderRef = $request->input('pp_TxnRefNo');
        $success = $request->input('pp_ResponseCode') === '000';

        $transaction = Transaction::where('jazzcash_txn_ref', $orderRef)->first();

        if ($transaction) {
            $transaction->update(['status' => $success ? 'paid' : 'failed']);
            if ($success) {
                $transaction->booking->update(['status' => 'confirmed']);
            }
        }

        return redirect()->route('customer.bookings')
            ->with($success ? 'success' : 'error', $success ? 'Payment successful!' : 'Payment failed.');
    }
}