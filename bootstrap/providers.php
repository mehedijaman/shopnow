<?php

use App\Providers\AppServiceProvider;
use Modules\Acl\AclServiceProvider;
use Modules\AdminAuth\AdminAuthServiceProvider;
use Modules\Blog\BlogServiceProvider;
use Modules\Cart\CartServiceProvider;
use Modules\Customer\CustomerServiceProvider;
use Modules\Dashboard\DashboardServiceProvider;
use Modules\Index\IndexServiceProvider;
use Modules\Order\OrderServiceProvider;
use Modules\Product\ProductServiceProvider;
use Modules\Support\SupportServiceProvider;
use Modules\User\UserServiceProvider;

return [
    AppServiceProvider::class,

    SupportServiceProvider::class,
    AdminAuthServiceProvider::class,
    UserServiceProvider::class,
    DashboardServiceProvider::class,
    AclServiceProvider::class,

    IndexServiceProvider::class,
    BlogServiceProvider::class,

    ProductServiceProvider::class,
    CustomerServiceProvider::class,
    OrderServiceProvider::class,
    CartServiceProvider::class,
];
