<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DatatableController extends Controller {
	
	public function list(Request $request) {
		
		$table = config($request->input('table'));
		
		$query = DB::table($table['table']);
		
		$this->addWhere($query, $table);
		$this->addJoins($query, $table);
		$this->addSelects($query, $table);
		
		if ($request->filled('sort')) {
			$sort = explode('|', $request->input('sort'));
			$query->orderBy($sort[0], $sort[1]);
		} else {
			$query->orderBy("{$table['table']}.created_at", 'desc');
		}
		
		if ($request->filled('filter')) {
			foreach (json_decode($request->input('filter')) as $field => $filter) {
				if ($filter !== '') {
					$filterVal = "%{$filter}%";
					$query->where($field, 'like', $filterVal);
				}
			}
		}
		
		return $query->paginate($request->input('per_page'));
	}
	
	protected function addSelects($query, $table) {
		$selects = [];
		foreach ($table['fields'] as $field) {
			$selects[] = $field['name'];
		}
		
		$query->select(...$selects);
		return $query;
	}
	
	protected function addJoins($query, $table) {
		if ($joins = $table['joins'] ?? false) {
			foreach ($joins as $join) {
				$query->join(...$join);
			}
			
		}
		return $query;
	}
	
	
	protected function addWhere($query, $table) {
		if ($where = $table['where'] ?? false) {
			$query->where(...$where);
		}
		return $query;
	}
	
}
