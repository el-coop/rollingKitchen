<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Pdf;
use App\Notifications\InvoiceSent;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Notification;

class SendApplicationInvoice implements ShouldQueue {
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
	 * @var array
	 */
	private $attachments;
	
	/**
	 * Create a new job instance.
	 *
	 * @param Invoice $invoice
	 * @param string $recipient
	 * @param string $subject
	 * @param string $message
	 * @param Collection $bcc
	 */
	public function __construct(Invoice $invoice, string $recipient, string $subject, string $message, array $attachments, Collection $bcc) {
		//
		$this->invoice = $invoice;
		$this->recipient = $recipient;
		$this->message = $message;
		$this->bcc = $bcc;
		$this->subject = $subject;
		$this->attachments = $attachments;
	}
	
	/**
	 * Execute the job.
	 *
	 * @param InvoiceService $service
	 * @return void
	 */
	public function handle() {
		$application = $this->invoice->owner;
		$language = $application->kitchen->user->language;
		
		$invoiceService = new InvoiceService($application);
		$number = $this->invoice->formattedNumber;
		$invoiceService->generate($number, $this->invoice->items, $this->invoice->tax)
			->save("invoices/{$number}.pdf");
		
		$files = collect($this->attachments)->map(function ($file) {
			$pdf = Pdf::find($file);
			return [
				'file' => storage_path("app/public/pdf/{$pdf->file}"),
				'name' => $pdf->name
			];
		});
		
		$files->push([
			'file' => storage_path("app/invoices/{$number}.pdf"),
			'name' => "{$number}.pdf"
		]);
		Notification::route('mail', $this->recipient)
			->notify((new InvoiceSent($this->subject, $application->kitchen->user->name, $this->message, $language, $files->toArray(), $this->bcc->toArray()))->locale($language));
		
		Storage::delete("invoices/{$number}.pdf");
	}
}
