<?php

namespace ElCoop\valuestore;


class Valuestore {

	private $fileName;

	public function __construct(string $fileName) {
		$info = pathinfo($fileName);
		if ($info['extension'] !== 'json') {
			throw new \Exception('File is not json');

		}
		if (!file_exists($fileName)){
			file_put_contents($fileName, "");
		}
		$this->fileName = $fileName;
	}

	public function all() {
		return json_decode(file_get_contents($this->fileName), true);
	}

	public function put($name, $value) {
		if (!is_string($name)) {
			throw new \Exception('Name has to be string');
		}
		$all = $this->all();
		$all[$name] = $value;
		$this->setContent($all);
	}

	public function allStartingWith(string $string) {
		if ($string === '') {
			return $this->all();
		}
		$all = $this->all();
		return array_filter($all, function ($key) use ($string) {
			return substr($key, 0, strlen($string)) === $string;
		}, ARRAY_FILTER_USE_KEY);
	}

	protected function setContent($values) {
		file_put_contents($this->fileName, json_encode($values));
	}

	public function get(string $name) {
		$all = $this->all();
		if (isset($all[$name])) {
			return $all[$name];
		}
		throw new \Exception('Setting ' . $name . ' is missing');
	}
}
