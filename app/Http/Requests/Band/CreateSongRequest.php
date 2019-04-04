<?php

namespace App\Http\Requests\Band;

use App\Models\BandSong;
use Illuminate\Foundation\Http\FormRequest;

class CreateSongRequest extends FormRequest {
	private $band;
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		$this->band = $this->route('band');
		return $this->user()->can('update', $this->band);
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'title' => 'required|string',
			'composer' => 'required|string',
			'owned' => 'required|boolean',
			'protected' => 'required|boolean',
		];
	}
	
	public function commit() {
		$song = new BandSong;
		
		$song->title = $this->input('title');
		$song->composer = $this->input('composer');
		$song->owned = $this->input('owned');
		$song->protected = $this->input('protected');
		$this->band->bandSongs()->save($song);
		
		return $song;
	}
}
