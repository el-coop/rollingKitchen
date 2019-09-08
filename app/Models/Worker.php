<?php

namespace App\Models;

use App\Models\Traits\HasFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JustBetter\PaginationWithHavings\PaginationWithHavings;


class Worker extends Model {
    use HasFields;
    use PaginationWithHavings;
    
    protected static function boot() {
        parent::boot();
        static::deleting(function ($worker) {
            $worker->photos->each->delete();
            $worker->taxReviews->each->delete();
        });
        static::deleted(function ($worker) {
            $worker->user->delete();
            $worker->shifts()->detach();
        });
    }
    
    protected $casts = [
        'data' => 'array',
    ];
    
    protected $appends = [
        'workplacesList',
        'photoList'
    ];
    
    static function indexPage() {
        return action('Admin\WorkerController@index', [], false);
    }
    
    public function homePage() {
        return action('Worker\WorkerController@index', $this);
    }
    
    public function user() {
        return $this->morphOne(User::class, 'user');
    }
    
    public function getFullDataAttribute() {
        $fullData = collect([
            [
                'name' => 'name',
                'label' => __('global.name'),
                'type' => 'text',
                'value' => $this->user->name ?? '',
            ], [
                'name' => 'email',
                'label' => __('global.email'),
                'type' => 'text',
                'value' => $this->user->email ?? '',
            ], [
                'name' => 'language',
                'label' => __('global.language'),
                'type' => 'select',
                'options' => [
                    'nl' => __('global.nl'),
                    'en' => __('global.en'),
                ],
                'value' => $this->user->language ?? 'nl',
            ],
            [
                'name' => 'type',
                'label' => __('admin/workers.type'),
                'type' => 'select',
                'options' => [
                    __('admin/workers.payroll'),
                    __('admin/workers.freelance'),
                    __('admin/workers.volunteer'),
                ],
                'value' => $this->type,
            ], [
                'name' => 'workplaces',
                'type' => 'multiselect',
                'label' => __('admin/workers.workplaces'),
                'options' => Workplace::select('name', 'id')->get(),
                'optionsLabel' => 'name',
                'value' => $this->workplaces()->select('name', 'workplaces.id')->get(),
            ], [
                'name' => 'supervisor',
                'type' => 'Checkbox',
                'value' => $this->supervisor,
                'options' => [[
                    'name' => __('admin/workers.makeSupervisor'),
                ]],
            ],
        ]);
        
        if ($this->exists) {
            $fullData = $fullData->push([
                'name' => 'approved',
                'type' => 'Checkbox',
                'value' => $this->approved,
                'options' => [[
                    'name' => __('admin/workers.approved'),
                ]],
            ]);
            $fullData = $fullData->concat($this->getFieldsData());
        }
        
        return $fullData;
    }
    
    public function workplaces() {
        return $this->belongsToMany(Workplace::class)->withTimestamps();
    }
    
    public function getWorkplacesListAttribute() {
        return $this->workplaces->implode('name', ', ');
        
    }
    
    public function photos() {
        return $this->hasMany(WorkerPhoto::class);
    }
    
    public function isSupervisor() {
        return $this->supervisor;
    }
    
    public function isMySupervisor(User $user) {
        $user = $user->user;
        if ($user->isSupervisor()) {
            foreach ($this->workplaces as $workplace) {
                if ($workplace->hasWorker($user)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function shifts() {
        return $this->belongsToMany(Shift::class)->using(ShiftWorker::class)->withPivot('start_time', 'end_time', 'work_function_id')->where('shifts.date', '>', Carbon::parse('first day of January'));
    }
    
    
    public function getWorkedHoursAttribute() {
        $shifts = $this->shifts()->where('closed', true)
            ->where('date', '>', request()->input('date', 0))
            ->get();
        $totalHours = new Carbon('today');
        $startOfDay = $totalHours->clone();
        $shifts->each(function ($shift) use ($totalHours) {
            $totalHours->add($shift->pivot->workedHours);
        });
        return $startOfDay->diffAsCarbonInterval($totalHours);
    }
    
    public function taxReviews() {
        return $this->hasMany(TaxReview::class);
    }
    
    public function getTotalPaymentAttribute() {
        return $this->shifts()->where('closed', true)
            ->where('date', '>', request()->input('date', 0))
            ->get()->sum(function ($shift) {
                return $shift->pivot->payment;
            });
    }
    
    public function getPhotoListAttribute() {
        return $this->photos;
    }
}

