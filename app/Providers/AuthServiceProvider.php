<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Application;
use App\Models\Debtor;
use App\Models\Developer;
use App\Models\Error;
use App\Models\Field;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Kitchen;
use App\Models\Pdf;
use App\Models\Service;
use App\Models\Worker;
use App\Models\WorkFunction;
use App\Models\Workplace;
use App\Policies\ApplicationPolicy;
use App\Policies\DebtorPolicy;
use App\Policies\ErrorPolicy;
use App\Policies\FieldPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\KitchenPolicy;
use App\Policies\InvoicePaymentPolicy;
use App\Policies\PDFPOlicy;
use App\Policies\ServicePolicy;
use App\Policies\WorkerPolicy;
use App\Policies\WorkFunctionPolicy;
use App\Policies\WorkplacePolicy;
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
		Invoice::class => InvoicePolicy::class,
		Error::class => ErrorPolicy::class,
		Service::class => ServicePolicy::class,
		Debtor::class => DebtorPolicy::class,
		InvoicePayment::class => InvoicePaymentPolicy::class,
		Workplace::class => WorkplacePolicy::class,
		WorkFunction::class => WorkFunctionPolicy::class,
		Worker::class => WorkerPolicy::class
	];
	
	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->registerPolicies();
		Gate::define('update-settings', function ($user) {
			return $user->user_type == Admin::class || $user->user_type == Developer::class;
		});
	}
}
