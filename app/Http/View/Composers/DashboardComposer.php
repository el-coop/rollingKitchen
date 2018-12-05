<?php
/**
 * Created by PhpStorm.
 * User: adam
 * Date: 04/11/2018
 * Time: 14:40
 */

namespace App\Http\View\Composers;


use App\Models\Admin;
use Illuminate\View\View;

class DashboardComposer {

    public $dashboardItems;

    public function __construct() {
    	$request = request();
    	if ($request->user()->user_type == Admin::class){
			$this->dashboardItems = config('admin.navbar');
		} else {
    		$this->dashboardItems = config('developer.navbar');
		}
    }

    public function compose(View $view){
        $view->with('dashboardItems', $this->dashboardItems);
    }
}
