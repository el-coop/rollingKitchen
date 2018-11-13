<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DatatableController extends Controller {
	
	public function list(Request $request) {
		
		$queryConfig = config($request->input('table'));
		
		if ($queryConfig['table'] ?? false) {
			$tableName = $queryConfig['table'];
			$query = DB::table($queryConfig['table']);
		} else {
			$query = $queryConfig['model']::query();
			$tableName = (new $queryConfig['model'])->getTable();
			
		}
		
		
		$this->addWhere($query, $queryConfig);
		$this->addJoins($query, $queryConfig);
		$this->addSelects($query, $queryConfig);
		
		$query->groupBy("{$tableName}.id");
		
		if ($request->filled('sort')) {
			$sort = explode('|', $request->input('sort'));
			$query->orderBy($sort[0], $sort[1]);
		} else {
			$query->orderBy("{$tableName}.created_at", 'desc');
		}
		
		if ($request->filled('filter')) {
			$this->addFilter($query, $request, $queryConfig['fields']);
		}
		
		return $query->paginate($request->input('per_page'));
	}
	
	protected function addSelects($query, $queryConfig) {
		$selects = [];
		foreach ($queryConfig['fields'] as $field) {
			$fieldName = $field['name'];
			if (strpos($fieldName, 'count') !== 0) {
				$tableName = $field['table'] ?? '';
				$selects[] = "{$tableName}.{$fieldName}";
			} else {
				$selects[] = DB::raw("$fieldName");
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
	
	
	protected function addWhere($query, $queryConfig) {
		if ($where = $queryConfig['where'] ?? false) {
			$query->where(...$where);
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
					$query->having($field, $filterConfig[0], $filterConfig[1]);
				} else {
					$filterVal = "%{$filter}%";
					$query->where($field, 'like', $filterVal);
				}
			}
		}
		
	}
	
}
