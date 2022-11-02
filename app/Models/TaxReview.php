<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxReview extends Model {
    use HasFactory;

    protected $appends = [
		'url'
	];

	protected static function boot() {
		parent::boot();
		static::deleted(function ($taxReview) {
			\Storage::delete("public/taxReviews/{$taxReview->file}");
		});
	}

	public function worker() {
		return $this->belongsTo(Worker::class);
	}

	public function getUrlAttribute() {
		return action('PhotoController@taxReview', $this);
	}
}
