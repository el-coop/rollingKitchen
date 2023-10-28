<?php
/**
 * Created by PhpStorm.
 * User: lcd34
 * Date: 11/12/2018
 * Time: 16:52
 */

namespace App\Services;

use Carbon\Carbon;
use ConsoleTVs\Invoices\Classes\Invoice as InvoiceFile;
use Illuminate\Support\Collection;


class IndividualTaxInvoice extends InvoiceFile {

    public $language;
    private $decimalPoint = '.';
    private $thousandSeparator = ',';
    public $tax_type;
    public $split;
    public $tax;
    public $extraAmount;
    public $extraName;

    public static function make($name = 'Invoice') {
        return new self($name);
    }

    public function language($language) {
        $this->decimalPoint = $language == 'nl' ? ',' : '.';
        $this->thousandSeparator = $language == 'nl' ? '.' : ',';
        $this->language = $language;
        if ($language == 'nl') {
            $this->date->locale('nl_NL');
        }
        return $this;
    }

    public function addItem($name, $price, $amount = 1, $tax = 0, $id = '-') {

        $totalPrice = $price * $amount * (1 + $tax / 100);

        $this->items->push(Collection::make([
            'name' => $name,
            'formattedPrice' => $this->formatNumber($price),
            'price' => $price,
            'amount' => $amount,
            'tax' => $tax,
            'totalPrice' => $this->formatNumber($totalPrice),
            'id' => $id,
        ]));

        return $this;
    }

    private function formatNumber($number) {
        return number_format($number, 2, $this->decimalPoint, $this->thousandSeparator);
    }

    private function subTotalPrice() {
        return $this->items->sum(function($item) {
            return $item['price'] * $item['amount'];
        });
    }

    public function subTotalPriceFormatted() {
        $subtotal = $this->items->sum(function($item) {
            return $item['price'] * $item['amount'];
        });
        return $this->formatNumber($subtotal);
    }


    public function totalPriceFormatted($partial = 1, $roundDown = false) {
        return $this->formatNumber(($this->taxPrice() + $this->subTotalPrice() + $this->extraAmount) * $partial - ($roundDown ? 0.005 : 0));
    }


    public function taxPriceFormatted($tax_rate = null) {
        return $this->formatNumber($this->taxPrice());
    }

    private function taxPrice() {
        if ($this->tax_type == 'percentage') {
            return $this->subTotalPrice() * $this->tax / 100;
        }

        if ($this->tax_type == 'individual') {
            return $this->items->sum(function($item) {
                return $item['price'] * $item['amount'] * $item['tax'] / 100;
            });
        }

        return $this->tax;
    }

    public function taxType($type) {
        $this->tax_type = $type;
        return $this;
    }

    public function tax($tax) {
        $this->tax = $tax;
        return $this;
    }

    public function split($split) {
        $this->split = $split;
        return $this;
    }

    public function extra($extraAmount, $extraName){
        $this->extraAmount = $extraAmount;
        $this->extraName = $extraName;
        return $this;
    }

    public function extraAmountFormatted() {
        return $this->formatNumber($this->extraAmount);
    }
}
