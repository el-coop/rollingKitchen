<?php

namespace App\Exceptions;

use App\Models\Error;
use App\Models\PhpError;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler {
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];
	
	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];
	
	/**
	 * Report or log an exception.
	 *
	 * @param  \Exception $exception
	 * @return void
	 */
	public function report(Exception $exception) {
		if ($this->shouldReport($exception)) {
		//	$this->logException($exception);
		}
		parent::report($exception);
	}
	
	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Exception $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception) {
		return parent::render($request, $exception);
	}
	
	protected function logException(Exception $exception) {
		$request = request();
		$error = new Error;
		$phpError = new PhpError;
		if ($request->user()) {
			$error->user_id = $request->user()->id;
		}
		$error->page = $request->fullUrl();
		$phpError->message = $exception->getMessage();
		$phpError->exception = [
			'class' => get_class($exception),
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $exception->getTrace(),
		];
		
		$phpError->request = [
			'method' => $request->method(),
			'input' => $request->all(),
			'server' => $request->server(),
			'headers' => $request->header(),
			'cookies' => $request->cookie(),
			'session' => $request->hasSession() ? $request->session()->all() : '',
			'locale' => $request->getLocale(),
		];
		$phpError->save();
		$phpError->error()->save($error);
	}
}
