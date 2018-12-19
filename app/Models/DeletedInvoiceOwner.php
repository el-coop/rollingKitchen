<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedInvoiceOwner extends Model {
	protected $fillable = [
		'name',
		'email',
		'language'
	];
	public function invoices() {
		return $this->morphMany(Invoice::class, 'owner');
	}
}
