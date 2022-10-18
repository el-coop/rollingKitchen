<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPhoto extends Model {
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

	public function worker() {
		return $this->belongsTo(Worker::class);
	}

	public function getUrlAttribute() {
		return action('PhotoController@worker', $this);
	}

}
