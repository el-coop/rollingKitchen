<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Illuminate\Database\Eloquent\Model;

class Application extends Model {
    use HasFields;
    public function kitchen(){
        return $this->belongsTo(Kitchen::class);
    }

    public function getEditData(){
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
        $data = static::fields()->map(function ($item) {
            return [
                'name' => $item->name,
                'label' => $item->name,
                'type' => $item->type,
                'value' => $this->data[$item->name] ?? ''
            ];
        });

        return $editData->concat($data);

    }
}
