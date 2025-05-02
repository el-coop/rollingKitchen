<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChangeExtraNameFromJsonToDedicatedColumns extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $workers = Worker::all();
        $first_name = Field::where('name_en', 'First name(s)')->where('form', Worker::class)->get()->first()->id;
        $surname = Field::where('name_en', 'Surname')->where('form', Worker::class)->get()->first()->id;
        foreach ($workers as $worker){
            $name = '';
            if(isset($worker->data["$first_name"])){
                $name = $worker->data["$first_name"];
            }
            $worker->first_name = $name;
            $lastname = '';
            if(isset($worker->data["$surname"])){
                $lastname = $worker->data["$surname"];
            }
            $worker->surname = $lastname;
            $worker->save();
        }
    }
}
