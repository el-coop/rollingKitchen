<?php

namespace App\Http\Requests\Admin\BlastMessaage;

use App\Models\Band;
use App\Models\BandMember;
use App\Models\User;
use App\Notifications\User\MessageSentNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SendBlastMessageRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return Gate::allows('send-blast-message');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'destination' => 'required|array',
			'subject_en' => 'required|string',
			'subject_nl' => 'required|string',
			'text_en' => 'required|string',
			'text_nl' => 'required|string',
			'channels' => 'required|array'
		];
	}
	public function commit(){
		$channels = array_keys($this->input('channels'));
		foreach ($this->input('destination') as $key => $value){
			$users = User::where('user_type', $key)->get();
			if ($key == Band::class){
				$users = $users->merge(User::where('user_type', BandMember::class)->get());
			};
			$users->each(function ($user) use ($channels) {
				$user->notify( new MessageSentNotification([
						'channels' => $channels,
						'body' => $this->input('text_' . $user->language),
						'subject' => $this->input('subject_' . $user->language)
					])
				);
			});
		}
	}
}
