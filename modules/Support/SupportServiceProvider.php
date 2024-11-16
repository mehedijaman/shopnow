<?php

namespace Modules\Support;

use Illuminate\Database\Eloquent\Relations\Relation;

class SupportServiceProvider extends BaseServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Modules\Support\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot()
    {
        Relation::morphMap([
            'user' => 'Modules\User\Models\User',
            'customer' => 'Modules\Customer\Models\Customer',
        ]);

        include __DIR__ . '/helpers.php';
        include __DIR__ . '/Validators/required_editor.php';
        include __DIR__ . '/Validators/recaptcha.php';
        parent::boot();
    }
}
