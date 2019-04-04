<?php

namespace App\Services;

use App;
use App\Models\Band;
use App\Models\Field;
use App\Models\Shift;
use App\Models\WorkedHoursExportColumn;
use App\Models\Worker;
use App\Models\WorkFunction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SetListService implements FromCollection, WithHeadings {
	
	use  Exportable;
	
	public function headings(): array {
		return [
			__('admin/fields.Band'),
			__('band/band.title'),
			__('band/band.composer'),
			__('band/band.owned'),
			__('band/band.protected'),
		];
	}
	
	public function collection() {
		return App\Models\BandSong::select('users.name', 'band_songs.title', 'band_songs.composer', 'band_songs.owned', 'band_songs.protected')
			->join('bands', 'band_id', '=', 'bands.id')
			->join('users', 'bands.id', '=', 'users.user_id')
			->where('users.user_type', Band::class)
			->orderBy('users.name')->get()->map(function ($bandSong) {
				return [
					$bandSong->name,
					$bandSong->title,
					$bandSong->composer,
					$bandSong->owned ? __('global.yes') : __('global.no'),
					$bandSong->protected ? __('global.yes') : __('global.no'),
				];
			});
		
		
	}
}
