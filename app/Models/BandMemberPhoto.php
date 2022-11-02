<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Storage;

class BandMemberPhoto extends Model {
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

	public function bandMember() {
		return $this->belongsTo(BandMember::class);
	}

	public function getUrlAttribute() {
		return action('PhotoController@bandMember', $this);
	}
}
