<?php

Route::get('accountant/pdf/{worker}', 'Admin\WorkerController@pdf')->middleware(['auth.basic', 'can:pdf,worker']);
Route::get('accountant/pdf/bandMember/{bandMember}', 'Admin\BandMemberController@pdf')->middleware(['auth.basic', 'can:pdf,bandMember']);
Route::get('accountant/pdf/bandAdmin/{bandAdmin}', 'Admin\BandController@adminPdf')->middleware(['auth.basic', 'can:adminBandPdf,' . \App\Models\Band::class]);
