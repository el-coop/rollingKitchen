<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Notifications\InvoiceSent;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Notification;
use Storage;

class SendDebtorInvoice implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	/**
	 * @var Invoice
	 */
	private $invoice;
	/**
	 * @var string
	 */
	private $recipient;
	/**
	 * @var string
	 */
	private $subject;
	/**
	 * @var string
	 */
	private $message;
	/**
	 * @var Collection
	 */
	private $bcc;

	/**
	 * Create a new job instance.
	 *
	 * @param Invoice $invoice
	 * @param string $recipient
	 * @param string $subject
	 * @param string $message
	 * @param Collection $bcc
	 */
	public function __construct(Invoice $invoice, string $recipient, string $subject, string $message, Collection $bcc) {
		//
		$this->invoice = $invoice;
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->message = $message;
		$this->bcc = $bcc;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		$owner = $this->invoice->owner;
		$language = $owner->language;


		$invoiceService = new InvoiceService($owner);
		$number = $this->invoice->formattedNumber;
        $extra = ['amount' => $this->invoice->extra_amount, 'name' => $this->invoice->extra_name];

        $invoiceService->generate($number, $this->invoice->items, $extra)
			->save("invoices/{$number}.pdf");


		Notification::route('mail', $this->recipient)
			->notify((new InvoiceSent($this->subject, $owner->name, $this->message, $language, [[
				'file' => storage_path("app/invoices/{$number}.pdf"),
				'name' => "{$number}.pdf"
			]], $this->bcc->toArray()))->locale($language));

		Storage::delete("invoices/{$number}.pdf");
	}
}
