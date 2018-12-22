<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model {
	protected $filename;

	public function bla($filename) {
		$this->filename = $filename;
	}

	public function allValues(){
		return json_decode(file_get_contents($this->filename),true);
	}

	public function put($name, $value){
		$all = $this->allValues();
		$all[$name] = $value;
		$this->setContent($all);
	}

	public function allStartingWith(string $string){
		if ($string === ''){
			return $this->allValues();
		}
		$all = $this->allValues();
		return array_filter($all, function ($key) use ($string){
			return substr($key, 0, strlen($string)) === $string;
		}, ARRAY_FILTER_USE_KEY);
	}

	public function get(string  $name){
		$all = $this->allValues();
		return $all[$name];
	}

	protected function setContent($values){
		file_put_contents($this->filename, json_encode($values));
	}
}
