<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedInvoiceOwner extends Model {
    use HasFactory;

    protected $casts = [
		'data' => 'array'
	];

	public function invoices() {
		return $this->morphMany(Invoice::class, 'owner');
	}
}
