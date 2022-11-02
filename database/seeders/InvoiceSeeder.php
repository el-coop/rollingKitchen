<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder {
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run() {
        \App\Models\Application::all()->each(function ($application) {
            $invoices = rand(0, 4);
            for ($j = 0; $j < $invoices; $j++) {
                $invoice = Invoice::factory()->make();
                $application->invoices()->save($invoice);

                $invoiceItems = rand(1, 4);

                for ($j = 0; $j < $invoiceItems; $j++) {
                    $invoiceItem = InvoiceItem::factory()->make();
                    $invoice->items()->save($invoiceItem);
                    $total = $invoiceItem->unit_price * $invoiceItem->quantity;
                }

                $invoice->amount = $total;
                $invoice->save();

            }
        });
    }
}
