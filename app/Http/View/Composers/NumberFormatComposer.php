<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 04/11/2018
 * Time: 14:40
 */

namespace App\Http\View\Composers;


use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class NumberFormatComposer {
	
	private $decimalPoint;
	private $thousandSeparator;
	
	public function __construct() {
		$this->decimalPoint = App::getLocale() == 'nl' ? ',' : '.';
		$this->thousandSeparator = App::getLocale() == 'nl' ? '.' : ',';
	}
	
	public function compose(View $view) {
		$view->with('decimalPoint', $this->decimalPoint);
		$view->with('thousandSeparator', $this->thousandSeparator);
	}
}