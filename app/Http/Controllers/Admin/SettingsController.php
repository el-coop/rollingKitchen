<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Settings\UpdateSettingsRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller {

	public function show() {
		$settings = app('settings');
		$generalSettings = collect([
			'registration_year' => $settings->get('registration_year')
		]);
		$tabs = [
			'admin/settings.title' => $generalSettings->merge($settings->allStartingWith('general_')),
			'admin/applications.applications' => $settings->allStartingWith('application_'),
			'admin/invoices.invoices' => $settings->allStartingWith('invoices_'),
			'admin/workers.workers' => $settings->allStartingWith('workers_'),
			'admin/artists.bands' => $settings->allStartingWith('bands_'),
			'admin/artists.artistManager' => $settings->allStartingWith('artist_managers_'),
			'admin/bands.bandMembers' => $settings->allStartingWith('band_members_'),
			'admin/bands.schedule' => $settings->allStartingWith('schedule_'),
            'admin/settings.accountant' => $settings->allStartingWith('accountant_'),
			'admin/settings.kitchen' => $settings->allStartingWith('kitchen_')
		];
		return view('admin.settings.show', compact('tabs'));
	}

	public function update(UpdateSettingsRequest $request) {
		$request->commit();

		return redirect()->back()->with('toast', [
			'type' => 'success',
			'title' => '',
			'message' => __('vue.updateSuccess')
		]);
	}
}
