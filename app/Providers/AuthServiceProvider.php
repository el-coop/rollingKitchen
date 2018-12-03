<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Application;
use App\Models\Field;
use App\Models\Invoice;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Policies\ApplicationPolicy;
use App\Policies\FieldPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\KitchenPolicy;
use App\Policies\PDFPOlicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		Field::class => FieldPolicy::class,
		Kitchen::class => KitchenPolicy::class,
		Application::class => ApplicationPolicy::class,
		Pdf::class => PDFPOlicy::class,
		Invoice::class => InvoicePolicy::class
	];
	
	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();
		Gate::define('update-settings', function ($user) {
			return $user->user_type == Admin::class;
		});
	}
}
