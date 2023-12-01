<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;


class OrderService
{
    
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        $merchant = Merchant::where('merchant_domain',$data['merchant_domain'])->first();
        $affiliate = $this->affiliateService->register(
            $merchant->id,
            $data['customer_email'],
            $data['customer_name'],
            $merchant->default_commission_rate,
        );

        $amount=$data['subtotal_price']* $merchant->default_commission_rate;

        $order = Order::updateOrCreate(
                [
                    'id' => $data['id']
                ]
            ,
            [
                'merchant_id'=>$merchant->id,
                'affiliate_id'=> $affiliate->id,
                'subtotal'=>$data['subtotal_price'],
                'commission_owed'=>$amount,
                'payout_status'=>Order::STATUS_UNPAID,
                'discount_code'=>$data['discount_code'],
            ]
        );

        $this->affiliateService->apiService->sendPayout('customer_email',$amount);
    }
}
