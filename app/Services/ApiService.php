<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use Illuminate\Support\Str;
use RuntimeException;
use App\Mail\PayoutNotification;
use Illuminate\Support\Facades\Mail;

/**
 * You don't need to do anything here. This is just to help
 */
class ApiService
{
    /**
     * Create a new discount code for an affiliate
     *
     * @param Merchant $merchant
     *
     * @return array{id: int, code: string}
     */
    public function createDiscountCode(Merchant $merchant): array
    {
        return [
            'id' => rand(0, 100000),
            'code' => Str::uuid()
        ];
    }

    /**
     * Send a payout to an email
     *
     * @param  string $email
     * @param  float $amount
     * @return void
     * @throws RuntimeException
     */
    public function sendPayout(string $email, float $amount)
    {
        //
        try {
            // TODO: Implement your payout logic here
            // For example, send an email notification about the payout
            Mail::to($email)->send(new PayoutNotification($amount));

            // You may also implement any other payout mechanisms like bank transfers, API calls, etc.

        } catch (\Exception $e) {
            // If an exception occurs during the payout, throw a RuntimeException
            throw new \RuntimeException("Payout failed: {$e->getMessage()}");
        }
    }
}
