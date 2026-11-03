<?php

namespace Modules\Customer\Observers;

use Illuminate\Support\Facades\Hash;
use Modules\Customer\Models\Customer;
use Modules\CustomerAuth\Services\RegistrationOtp;

class CustomerObserver
{
    public function saving(Customer $customer)
    {
        if ($customer->exists && $customer->isDirty('phone')) {
            $customer->phone_verified_at = null;
            if (app(RegistrationOtp::class)->requiredForNewCustomer()) {
                $customer->phone_verification_required = true;
            }
        }
        if (request()->filled('password')) {
            $customer->password = Hash::make(request('password'));
        }
    }
}
