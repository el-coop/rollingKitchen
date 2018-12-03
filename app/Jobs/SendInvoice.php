<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class SendInvoice implements ShouldQueue {
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
	private $message;
	/**
	 * @var array
	 */
	private $bcc;
	/**
	 * @var string
	 */
	private $subject;
	
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
		$this->message = $message;
		$this->bcc = $bcc;
		$this->subject = $subject;
	}
	
	/**
	 * Execute the job.
	 *
	 * @param InvoiceService $service
	 * @return void
	 */
	public function handle(InvoiceService $invoiceService) {
		$number = $this->invoice->formattedNumber;
		$invoiceService->generate($number, $this->invoice->tax, $this->invoice->items)
			->save(storage_path("invoices/{$number}"));
	}
}
