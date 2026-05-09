<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\ProfileController as BaseProfileController;
use App\Http\Requests\Owner\UpdatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends BaseProfileController
{
    public function show(): View
    {
        $payment = Payment::where('user_id', auth()->id())->first();

        return view('owner.profile', compact('payment'));
    }

    public function updatePayment(UpdatePaymentRequest $request): RedirectResponse
    {
        Payment::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return redirect()
            ->route('owner.profile')
            ->with('success', 'Payment info saved successfully.');
    }

    protected function profileRouteName(): string
    {
        return 'owner.profile';
    }
}
