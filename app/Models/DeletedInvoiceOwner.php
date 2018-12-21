<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedInvoiceOwner extends Model {
	
	protected $casts = [
		'data' => 'array'
	];
	
	public function invoices() {
		return $this->morphMany(Invoice::class, 'owner');
	}
}
