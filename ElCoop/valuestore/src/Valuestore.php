<?php

namespace valuestore;


class Valuestore {

	private $fileName;

	public function __construct(string $fileName) {
		if (file_exists($fileName)) {
			if ((substr($fileName, -5) === '.json')) {
				return $this->fileName = $fileName;
			}
			throw new \Exception('El-Coop Valuestore: File is not json');
		}
		throw new \Exception('El-Coop Valuestore: Settings file not found');
	}

	public function all() {
		return json_decode(file_get_contents($this->fileName), true);
	}

	public function put(string $name, $value) {
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
		throw new \Exception('El-Coop Valuestore: Setting ' . $name . ' is missing');
	}
}
