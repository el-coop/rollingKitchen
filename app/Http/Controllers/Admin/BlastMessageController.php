<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlastMessaage\SendBlastMessageRequest;
use App\Models\Band;
use App\Models\Kitchen;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlastMessageController extends Controller {

	public function show() {
		$fields = collect([
			[
				'name' => 'destination',
				'label' => __('admin/message.destination'),
				'type' => 'checkbox',
				'options' => [
					Worker::class => ['name' =>__('admin/workers.workers')], Band::class => ['name' => __('admin/artists.bands')], Kitchen::class =>['name' => __('admin/kitchens.kitchens')]
				]
			],
			[
				'name' => 'subject_en',
				'label' => __('admin/message.subjectEn'),
				'type' => 'text',
			],
			[
				'name' => 'subject_nl',
				'label' => __('admin/message.subjectNl'),
				'type' => 'text',
			],
			[
				'name' => 'text_en',
				'label' => __('admin/message.bodyEn'),
				'type' => 'textarea',
			],
			[
				'name' => 'text_nl',
				'label' => __('admin/message.bodyNl'),
				'type' => 'textarea',
			],
			[
				'name' => 'channels',
				'label' => __('admin/message.channels'),
				'type' => 'checkbox',
				'options' => ['mail' => ['name' => __('global.email')], 'nexmo' => ['name' => __('admin/message.sms')]]
			]
		]);
		
		return view('admin.blastMessage.show', compact('fields'));
	}

	public function send(SendBlastMessageRequest $request) {
		$request->commit();

	}
}
