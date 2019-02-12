<?php

Route::get('accountant/pdf/{worker}', 'Admin\WorkerController@pdf')->middleware(['auth.basic', 'can:pdf,worker']);
