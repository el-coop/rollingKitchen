<?php

namespace Database\Seeders;

use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceNumberDatatableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Invoice::all()->each(function ($invoice){
            $number = $invoice->number;
            $prefix = $invoice->prefix;
            if (strlen($number) == 1){
                $invoice->number_datatable = "$prefix-00$number";
            } elseif (strlen($number) == 2){
                $invoice->number_datatable = "$prefix-0$number";
            } else {
                $invoice->number_datatable = "$prefix-$number";
            }
            $invoice->save();
        });
    }
}
