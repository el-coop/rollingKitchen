<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 04/11/2018
 * Time: 14:40
 */

namespace App\Http\ViewComposers;


use Illuminate\View\View;

class DashboardComposer {

    public $dashboardItems;

    public function __construct() {
        $this->dashboardItems = config('admin.navbar');
    }

    public function compose(View $view){
        $view->with('dashboardItems', $this->dashboardItems);
    }
}