<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Field;
use App\Models\Kitchen;
use Illuminate\Console\Command;

class ImportFields extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'import:fields';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Imports fields from field.json file in storage';
	
	
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$this->info('Reading File...');
		$fieldsJson = json_decode(\Storage::disk('local')->get('fields.json'), true);

		foreach ($fieldsJson as $fieldType => $fields) {
			$order = 0;
			$this->info("Importing {$fieldType} fields");
			
			$type = $this->getFieldTypeName($fieldType);
			
			foreach ($fields as $field) {
				$field['form'] = $type;
				$field['order'] = $order++;
				Field::forceCreate($field);
			}
			
			
			$this->alert("Import {$fieldType} Complete");
		}

		Field::forceCreate([
			'name_en' => 'Instagram',
			'name_nl' => 'Instagram',
			'type' => 'text',
			'order' =>  Kitchen::getLastFieldOrder() + 1,
			'form' => Kitchen::class,
		]);


	}
	
	protected function getFieldTypeName($type) {
		switch ($type) {
			case 'kitchen':
				return Kitchen::class;
			case 'application':
				return Application::class;
		}
		return '';
	}
}
