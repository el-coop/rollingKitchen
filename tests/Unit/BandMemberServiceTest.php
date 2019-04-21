<?php

namespace Tests\Unit;

use App\Models\Band;
use App\Models\BandAdmin;
use App\Models\BandMember;
use App\Models\BandMemberExportColumn;
use App\Models\BandSchedule;
use App\Models\Stage;
use App\Models\User;
use App\Services\BandMemberService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BandMemberServiceTest extends TestCase {
	protected $bands;
	protected $bandMemberColumns;
	protected $bandMemberService;
	
	protected function setUp(): void {
		parent::setUp(); // TODO: Change the autogenerated stub
		$stage = factory(Stage::class)->create();
		$this->bands = factory(Band::class, 4)->create([
			'payment_method' => 'band'
		])->each(function ($band) use ($stage) {
			$band->user()->save(factory(User::class)->make());
			factory(BandSchedule::class)->create([
				'stage_id' => $stage->id,
				'approved' => 'accepted',
				'payment' => 40,
				'band_id' => $band->id,
			]);
			factory(BandMember::class, 3)->create([
				'payment' => 10,
				'band_id' => $band->id
			]);
			factory(BandAdmin::class)->create([
				'payment' => 10,
				'band_id' => $band->id
			]);
			
		});
		$columns = ['user.name', 'user.email', 'band.name', 'bandMember.payment'];
		$i = -1;
		$this->bandMemberColumns = factory(BandMemberExportColumn::class, 2)->make()->each(function ($column) use ($columns, $i) {
			$i = $i + 1;
			$column->column = $columns[$i];
			$column->orded = $i;
		});
		$this->bandMemberService = new BandMemberService;
	}
	
	public function test_sets_heading() {
		$headings = $this->bandMemberService->headings();
		$expectedHeadings = BandMemberExportColumn::orderBy('order')->get()->pluck('name')->toArray();
		$this->assertEquals($expectedHeadings, $headings);
	}
	
	public function test_collection() {
		$collection = $this->bandMemberService->collection();
		$data = $this->collect();
		$this->assertEquals($data, $collection);
	}
	
	public function collect() {
		$bands = Band::where('payment_method', 'individual')->whereHas('schedules', function ($query) {
			$query->where('approved', 'accepted');
		})->get();
		$fields = BandMemberExportColumn::orderBy('order')->get()->pluck('column');
		$data = collect();
		foreach ($bands as $band) {
			$data->push($this->listAdminData($fields, $band->admin));
			foreach ($band->bandMembers as $bandMember){
				$data->push($this->listData($fields,$bandMember));
			}
		}
		return $data;
	}
	
	public function test_individual() {
		$bandMember = BandMember::first();
		$individual = $this->bandMemberService->individual($bandMember);
		$fields = BandMemberExportColumn::orderBy('order')->get();
		$data = $fields->pluck('name')->combine($this->listData($fields->pluck('column'), $bandMember));
		$this->assertEquals($data, $individual);
	}

	public function test_individual_admin() {
		$bandAdmin = BandAdmin::first();
		$individual = $this->bandMemberService->adminIndividual($bandAdmin);
		$fields = BandMemberExportColumn::orderBy('order')->get();
		$data = $fields->pluck('name')->combine($this->listAdminData($fields->pluck('column'), $bandAdmin));
		$this->assertEquals($data, $individual);
	}
	
	/**
	 * @param $fields
	 * @param $bandMember
	 * @return \Illuminate\Support\Collection
	 */
	protected function listData($fields, BandMember $bandMember): \Illuminate\Support\Collection {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'band':
					$result->push($bandMember->band->user->name);
					break;
				case 'bandMember':
					if ($column === 'payment') {
						$result->push($bandMember->payment);
					} else {
						$result->push($bandMember->data[$column] ?? '');
					}
					break;
				default:
					$result->push($bandMember->user->$column);
					break;
			}
		}
		return $result;
	}

	protected function listAdminData($fields, BandAdmin $bandAdmin) {
		$result = collect();
		foreach ($fields as $field) {
			$model = strtok($field, '.');
			$column = strtok('.');
			switch ($model) {
				case 'band':
					$result->push($bandAdmin->band->user->name);
					break;
				case 'bandMember':
					switch ($column) {
						case 'payment':
							$result->push($bandAdmin->payment);
							break;
						case 'pdf':
							$result->push(action('Admin\BandController@adminPdf', $bandAdmin));
							break;
						default:
							$result->push($bandAdmin->data[$column] ?? '');

					}
					break;
				default:
					if ($model == 'name') {
						$result->push($bandAdmin->name);
					} else {
						$result->push($bandAdmin->band->user->email);
					}
					break;
			}
		}
		return $result;
	}

	protected function adminIndividual(BandAdmin $bandAdmin) {
		$fields = BandMemberExportColumn::orderBy('order')->get();
		return $fields->pluck('name')->combine($this->listAdminData($fields->pluck('column'), $bandAdmin));
	}
}
