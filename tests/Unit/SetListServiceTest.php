<?php

namespace Tests\Unit;

use App\Models\Band;
use App\Models\BandSong;
use App\Models\User;
use App\Services\SetListService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetListServiceTest extends TestCase {

	use RefreshDatabase;

	public function setUp(): void {
		parent::setUp();
		$this->songs = collect();

		$this->service = new SetListService();
	}

	public function test_sets_heading() {
		$headings = $this->service->headings();
		$this->assertEquals([
			__('admin/fields.Band'),
			__('band/band.title'),
			__('band/band.composer'),
			__('band/band.owned'),
			__('band/band.protected'),
		], $headings);
	}

	public function test_collection() {
		Band::factory(4)->create()->each(function ($band) {
			$band->user()->save(User::factory()->make());
			BandSong::factory(4)->create([
				'band_id' => $band->id
			]);
		});

		$collection = $this->service->collection();

		$songs = BandSong::select('users.name', 'band_songs.title', 'band_songs.composer', 'band_songs.owned', 'band_songs.protected')
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

		$this->assertEquals($songs, $collection);
	}
}
