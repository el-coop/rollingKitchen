<?php

namespace Tests\Unit;

use App\Services\DatatableService;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatatableServiceTest extends TestCase {
	
	private $datable;
	private $request;
	
	public function setUp(): void {
		parent::setUp();
		Config::set('test.config', [
			'table' => 'test',
			'fields' => [[
				'name' => 'id',
				'visible' => false
			], [
				'name' => 'name',
				'title' => 'title',
				'callback' => 'translate',
			], [
				'name' => 'year',
			]]
		]);
		$this->request = new Request([
			'table' => 'test.config',
		
		]);
		$this->datable = new DatatableService($this->request);
	}
	
	public function test_returns_query() {
		$query = $this->datable->query();
		
		$this->assertInstanceOf(Builder::class, $query);
		$this->assertEquals('test', $query->from);
		$this->assertEquals(['id', 'name', 'year'], $query->columns);
	}
	
	public function test_sets_heading() {
		$headings = $this->datable->headings();
		
		$this->assertEquals(['title', 'year'], array_values($headings));
	}
	
	public function test_formats_fields_for_excel() {
		$object = (object)[
			'id' => 1,
			'name' => 'new',
			'year' => 2018
		];
		$datatableService = \Mockery::mock(DatatableService::class, [$this->request])->makePartial();
		$datatableService->shouldReceive('query')->andReturnSelf()->shouldReceive('get')->andReturn(collect([
			$object
		]));
		
		$this->assertEquals(collect([
			'name' => 'New',
			'year' => 2018,
		]), $datatableService->collection()->first());
	}
	
}
