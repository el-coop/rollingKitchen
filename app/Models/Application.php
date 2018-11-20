<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Application extends Model {
	use HasFields;
	
	static function indexPage(){
		return action('Admin\ApplicationController@index',[],false);
	}
	
	public function kitchen() {
		return $this->belongsTo(Kitchen::class);
	}
	
	public function getEditData() {
		$editData = collect([
			[
				'name' => 'year',
				'label' => __('misc.year'),
				'type' => 'text',
				'value' => $this->year
			],
			[
				'name' => 'status',
				'label' => __('misc.status'),
				'type' => 'select',
				'options' => [
					'pending' => __('datatable.pending'),
					'accepted' => __('datatable.accepted'),
					'rejected' => __('datatable.rejected')
				],
				'value' => $this->status
			]
		]);
		
		return $editData->concat($this->getFieldsData());
	}
}