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
    public function pay(Booking $booking): View
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $orderRef = 'ORD' . now()->format('YmdHis') . $booking->id;

        Transaction::create([
            'booking_id' => $booking->id,
            'order_ref' => $orderRef,
            'amount' => $booking->total_price,
            'status' => 'pending',
        ]);

        $data = $this->buildJazzCashPayload($booking, $orderRef);

        \Log::info('JazzCash Payload', $data);

        return view('customer.payments.redirect', [
            'data' => $data,
            'url' => config('services.jazzcash.url'),
        ]);
    }

    private function buildJazzCashPayload(Booking $booking, string $orderRef): array
    {
        $txnDateTime = now()->format('YmdHis');
        $expiry = now()->addMinutes(30)->format('YmdHis');
        $amount = (int) round($booking->total_price * 100); // JazzCash expects amount in paisa

        $data = [
            'pp_Version' => '1.1',
            'pp_TxnType' => 'MPAY',
            'pp_Language' => 'EN',
            'pp_MerchantID' => config('services.jazzcash.merchant_id'),
            'pp_SubMerchantID' => '',
            'pp_Password' => config('services.jazzcash.password'),
            'pp_BankID' => '',
            'pp_ProductID' => '',
            'pp_TxnRefNo' => $orderRef,
            'pp_Amount' => $amount,
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $txnDateTime,
            'pp_BillReference' => 'billRef',
            'pp_Description' => 'Booking payment #' . $booking->id,
            'pp_TxnExpiryDateTime' => $expiry,
            'pp_ReturnURL' => route('customer.payments.callback'),
            'ppmpf_1' => '',
            'ppmpf_2' => '',
            'ppmpf_3' => '',
            'ppmpf_4' => '',
            'ppmpf_5' => '',
        ];

        ksort($data);
        $stringToHash = config('services.jazzcash.salt') . '&' . implode('&', $data);
        $data['pp_SecureHash'] = hash_hmac('sha256', $stringToHash, config('services.jazzcash.salt'));
        
        return $data;
    }

    public function callback(Request $request): RedirectResponse
    {
        $orderRef = $request->input('pp_TxnRefNo');
        $success = $request->input('pp_ResponseCode') === '000';

        $transaction = Transaction::where('order_ref', $orderRef)->first();

        if ($transaction) {
            $transaction->update(['status' => $success ? 'paid' : 'failed']);
        }

        return redirect()->route('customer.bookings')
            ->with($success ? 'success' : 'error', $success ? 'Payment successful!' : 'Payment failed.');
    }
}