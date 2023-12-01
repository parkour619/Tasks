<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // TODO: Complete this method

        $user = User::where('email',$data['email'])->first();
        if($user == null){
            $user=User::create([
                'name'=> $data['name'],
                'email'=> $data['email'],
                'password'=> Hash::make($data['api_key'])
    
            ]);    
        }
        $merchant = Merchant::create([
            'user_id' => $data['user_id'],
            'domain' => $data['domain'],
            'display_name' => $data['name'],
            // 'turn_customers_into_affiliates' => $data['turn_customers_into_affiliates'],
            // 'default_commission_rate' => $data['default_commission_rate'],
        ]);
        return $merchant;
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method
        $merchant=$this->findMerchantByEmail($user->email);
        $merchant->domain = $data['domain'];
        $merchant->display_name = $data['name'];
        // $merchant->turn_customers_into_affiliates =  $data['turn_customers_into_affiliates'];
        // $merchant->default_commission_rate =  $data['default_commission_rate'];
        $merchant->update();
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        // TODO: Complete this method
        $user = User::where('email',$email)->first();
        if($user == null){
           return 0 ;
        }
        $merchant = Merchant::where('user_id',$user->id)->first();
        return $merchant;
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        // TODO: Complete this method
        // Get all unpaid orders for the affiliate
        $unpaidOrders = $affiliate->orders()->where('payout_status', Order::STATUS_UNPAID)->get();

        // Dispatch PayoutOrderJob for each unpaid order
        foreach ($unpaidOrders as $order) {
            PayoutOrderJob::dispatch($order);
        }
    }
}
