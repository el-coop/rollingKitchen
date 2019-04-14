<?php

Route::get('accountant/pdf/{worker}', 'Admin\WorkerController@pdf')->middleware(['auth.basic', 'can:pdf,worker']);
Route::get('accountant/pdf/bandMember/{bandMember}', 'Admin\BandMemberController@pdf')->middleware(['auth.basic', 'can:pdf,bandMember']);
