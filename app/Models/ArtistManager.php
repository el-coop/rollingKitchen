<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistManager extends Model {
    use HasFactory;

	protected static function boot() {
		parent::boot(); // TODO: Change the autogenerated stub

		static::deleted(function ($artistManager) {
			$artistManager->user->delete();
		});
	}

	public function homePage(){
		return action('ArtistManager\ArtistManagerController@index');
	}

	static function indexPage() {
		return action('ArtistManager\ArtistManagerController@index', [], false);
	}

	public function user() {
		return $this->morphOne(User::class, 'user');
	}

	public function getFullDataAttribute(){
		return collect([
				[
					'name' => 'name',
					'label' => __('global.name'),
					'type' => 'text',
					'value' => $this->user->name ?? '',
				], [
					'name' => 'email',
					'label' => __('global.email'),
					'type' => 'text',
					'value' => $this->user->email ?? '',
				], [
					'name' => 'language',
					'label' => __('global.language'),
					'type' => 'select',
					'options' => [
						'nl' => __('global.nl'),
						'en' => __('global.en'),
					],
					'value' => $this->user->language ?? 'nl',
				],
		]);
	}
}
