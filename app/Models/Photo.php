<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model {
    use HasFactory;

    public $appends = [
		'url',
	];

	protected static function boot() {
		parent::boot();
		static::deleted(function ($photo) {
			\Storage::delete("public/photos/{$photo->file}");
		});
	}

	public function kitchen() {
		return $this->belongsTo(Kitchen::class);
	}

	public function getUrlAttribute() {
		return action('PhotoController@show', $this);
	}
}
