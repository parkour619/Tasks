<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method
        $fromDate = Carbon::parse($request->input('from'));
        $toDate = Carbon::parse($request->input('to'));

        $stats = [
            'count' => 0,
            'commission_owed' => 0.00,
            'revenue' => 0.00,
        ];

        // Fetch orders within the date range
        $orders = Order::whereBetween('created_at', [$fromDate, $toDate])->get();

        foreach ($orders as $order) {
            $stats['count']++;
            $stats['revenue'] += $order->subtotal;

            // Accumulate unpaid commission for orders with an affiliate
            if ($order->affiliate_id && $order->payout_status !== Order::STATUS_PAID) {
                $stats['commission_owed'] += $order->commission_owed;
            }
        }

        return response()->json($stats);
        
    }
}
