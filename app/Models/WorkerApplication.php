<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerApplication extends Model {


    public function worker(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
        return $this->belongsTo(Worker::class);
    }
}
