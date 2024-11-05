<?php

namespace Modules\Customer\Observers;

use Illuminate\Support\Facades\Hash;
use Modules\Customer\Models\Customer;

class CustomerObserver
{
    public function saving(Customer $customer)
    {
        if (request()->filled('password')) {
            $customer->password = Hash::make(request('password'));
        }
    }
}
