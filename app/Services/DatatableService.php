<?php
/**
 * Created by PhpStorm.
 * User: lcd34
 * Date: 17/11/2018
 * Time: 19:16
 */

namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DatatableService implements FromCollection, WithHeadings {
	
	use Exportable;
	
	private $queryConfig;
	private $request;
	
	public function __construct(Request $request, ?array $queryConfig = null) {
		$this->request = $request;
		if (!$queryConfig) {
			$this->queryConfig = config($this->request->input('table'));
		} else {
			$this->queryConfig = $queryConfig;
		}
	}
	
	public function query() {
		if ($this->queryConfig['table'] ?? false) {
			$tableName = $this->queryConfig['table'];
			$query = DB::table($this->queryConfig['table']);
		} else {
			$query = $this->queryConfig['model']::query();
			$tableName = (new $this->queryConfig['model'])->getTable();
		}
		
		
		$this->addWhere($query, $this->queryConfig);
		$this->addJoins($query, $this->queryConfig);
		$this->addJoinOn($query, $this->queryConfig);
		$this->addSelects($query, $this->queryConfig);
		$query->groupBy("{$tableName}.id");
		
		if ($this->request->filled('sort')) {
			$sort = explode('|', $this->request->input('sort'));
			$query->orderBy($sort[0], $sort[1]);
		} else {
			if (isset($this->queryConfig['orderBy'])) {
				$query->orderBy("{$tableName}.{$this->queryConfig['orderBy']}");
				
			} else {
				$query->orderBy("{$tableName}.created_at", 'desc');
				
			}
		}
		
		if ($this->request->filled('filter')) {
			$this->addFilter($query, $this->request, $this->queryConfig['fields']);
		}
		return $query;
	}
	
	
	protected function addSelects($query, $queryConfig) {
		$selects = [];
		if ($cases = $queryConfig['cases'] ?? false) {
			foreach ($cases as $case) {
				$selects[] = DB::raw("CASE {$case}");
			}
		}
		
		$selectFields = collect($queryConfig['fields'])->reject(function ($item) {
			return $item['noTable'] ?? false;
		});
		foreach ($selectFields as $field) {
			$fieldName = $field['name'];
			if (strpos($fieldName, 'count') !== 0 && !($field['raw'] ?? false)) {
				$tableName = isset($field['table']) ? "{$field['table']}." : '';
				$selects[] = "{$tableName}{$fieldName}";
			} else {
				if (($field['raw'] ?? false)) {
					// JSON functions is not supported by sqlite
					if (env('APP_ENV') !== 'testing' || strpos($field['raw'], 'JSON_') !== 0) {
						$selects[] = DB::raw($field['raw']);
					}
				} else {
					$selects[] = DB::raw("$fieldName");
				}
			}
		}
		
		
		$query->select(...$selects);
		return $query;
	}
	
	protected function addJoins($query, $queryConfig) {
		if ($joins = $queryConfig['joins'] ?? false) {
			foreach ($joins as $join) {
				$query->leftJoin(...$join);
			}
			
		}
		return $query;
	}
	
	protected function addJoinOn($query, $queryConfig) {
		if ($joinsOn = $queryConfig['joinsOn'] ?? false) {
			foreach ($joinsOn as $joinOn) {
				$query->leftJoin($joinOn[0], function ($join) use ($joinOn) {
					$join->on($joinOn[1], $joinOn[2], $joinOn[3])
						->where($joinOn[4], $joinOn[5], $joinOn[6]);
				});
			}
		}
		return $query;
	}
	
	
	protected function addWhere($query, $queryConfig) {
		if ($where = $queryConfig['where'] ?? false) {
			$query->where($where);
		}
		
		return $query;
	}
	
	protected function addFilter($query, $request, $queryConfig) {
		$filters = json_decode($request->input('filter'));
		foreach ($filters as $field => $filter) {
			if ($filter !== '') {
				$filterConfig = collect($queryConfig)->first(function ($item) use ($field) {
					return $item['name'] == $field;
				});
				if ($filterConfig['filterDefinitions'] ?? false) {
					$filterConfig = $filterConfig['filterDefinitions'][$filter];
					$query->having($field, $filterConfig[0], is_callable($filterConfig[1]) ? $filterConfig[1]() : $filterConfig[1]);
				} else if ($filterConfig['filterFields'] ?? false) {
					$fields = $filterConfig['filterFields'];
					$query->where(function ($query) use ($fields, $filter) {
						$filterVal = "%{$filter}%";
						foreach ($fields as $field) {
							$query->orWhere($field, 'like', $filterVal);
						}
					});
				} else {
					if ($filterConfig['table'] ?? false) {
						$field = "{$filterConfig['table']}.{$field}";
					}
					$filterVal = "%{$filter}%";
					$query->where($field, 'like', $filterVal);
				}
			}
		}
	}
	
	public function headings(): array {
		return collect($this->queryConfig['fields'])->filter(function ($item) {
			return $item['visible'] ?? true;
		})->map(function ($item) {
			return __($item['title'] ?? $item['name']);
		})->toArray();
	}
	
	/**
	 * @return Collection
	 */
	public function collection() {
		return $this->query()->get()->map(function ($item) {
			return $this->formatField($item);
		});
	}
	
	protected function formatField($field) {
		$formatted = collect();
		$config = collect($this->queryConfig['fields']);
		foreach ($config as $columnConfig) {
			$column = $columnConfig['name'];
			if ($columnConfig['visible'] ?? true) {
				if (($columnConfig['callback'] ?? false) == 'translate') {
					$formatted->put($column, __("vue.{$field->$column}"));
				} else {
					$formatted->put($column, $field->$column);
				}
			}
		}
		return $formatted;
	}
}
