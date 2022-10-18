<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class BandAdminPhoto extends Model {
    use HasFactory;

	public $appends = [
		'url',
	];

	protected static function boot() {
		parent::boot();
		static::deleted(function ($photo) {
			Storage::delete("public/photos/{$photo->file}");
		});
	}

	public function bandAdmin() {
		return $this->belongsTo(BandAdmin::class);
	}

	public function getUrlAttribute() {
		return action('PhotoController@bandAdmin', $this);
	}
}
