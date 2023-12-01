<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PayoutOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Use the API service to send a payout of the correct amount.
     * Note: The order status must be paid if the payout is successful, or remain unpaid in the event of an exception.
     *
     * @return void
     */
    public function handle(ApiService $apiService)
    {
        // TODO: Complete this method

        try {
            // TODO: Implement the logic to calculate the correct payout amount
            $payoutAmount = $this->calculatePayoutAmount($this->order->subtotal);

            // TODO: Use the ApiService to send the payout
            $apiService->sendPayout($this->order->affiliate, $payoutAmount);

            // If the payout is successful, update the order status to paid
            $this->order->update(['payout_status' => Order::STATUS_PAID]);

        } catch (\Exception $e) {
            // Handle any exceptions here
            // If an exception occurs during the payout, the order status remains unpaid
            // Log the exception, notify admin, etc.
            \Log::error("Payout failed for order ID {$this->order->id}: {$e->getMessage()}");
        }
    }

    private function calculatePayoutAmount(float $subtotal): float
    {
        // TODO: Implement your payout calculation logic here
        // This can be a percentage of the subtotal, fixed amount, or any other business logic
        // For example, let's assume a 10% commission rate
        $commissionRate = 0.10;
        $payoutAmount = $subtotal * $commissionRate;

        return $payoutAmount;
    }
}
