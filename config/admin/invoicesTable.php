<?php

return [
    'model' => \App\Models\Invoice::class,
    'joins' => [
        ['invoice_payments', 'invoices.id', '=', 'invoice_payments.invoice_id']
    ],
    'joinsOn' => [
        ['applications', 'invoices.owner_id', '=', 'applications.id', 'invoices.owner_type', '=', \App\Models\Application::class],
        ['debtors', 'invoices.owner_id', '=', 'debtors.id', 'invoices.owner_type', '=', \App\Models\Debtor::class],
        ['users', 'applications.kitchen_id', '=', 'users.user_id', 'users.user_type', '=', \App\Models\Kitchen::class],
        ['deleted_invoice_owners', 'invoices.owner_id', '=', 'deleted_invoice_owners.id', 'invoices.owner_type', '=', \App\Models\DeletedInvoiceOwner::class]
    ],
    'cases' => [
        'WHEN debtors.name IS NULL AND users.name IS NULL THEN deleted_invoice_owners.name WHEN debtors.name IS NULL THEN users.name ELSE debtors.name END as name'
    ],

    'fields' => [[
        'name' => 'id',
        'table' => 'invoices',
        'visible' => false
    ], [
        'name' => 'owner_id',
        'visible' => false
    ], [
        'name' => 'owner_type',
        'visible' => false
    ], [
        'name' => 'number',
        'table' => 'invoices',
        'visible' => false
    ], [
        'name' => 'amount',
        'table' => 'invoices',
        'visible' => false
    ], [
        'name' => 'tax',
        'table' => 'invoices',
        'visible' => false
    ], [
        'name' => 'number_datatable',
        'table' => 'invoices',
        'title' => 'admin/invoices.number',
        'filterFields' => ['invoices.number_datatable'],
        'sortField' => 'number',

    ], [
        'name' => 'nameOnInVoice',
        'raw' => 'IFNULL(users.name,IFNULL(debtors.name,deleted_invoice_owners.name)) as nameOnInVoice',
        'title' => 'global.name',
        'sortField' => 'amount',
        'filterFields' => ['users.name', 'debtors.name', 'deleted_invoice_owners.name'],
    ], [
        'name' => 'prefix',
        'title' => 'global.year',
        'sortField' => 'prefix'
    ], [
        'name' => 'total',
        'raw' => '(invoices.amount + (invoices.amount * invoices.tax/100) + invoices.extra_amount) as total',
        'title' => 'admin/invoices.amount',
        'sortField' => 'amount',
        'callback' => 'localNumber',
        'filter' => false
    ], [
        'name' => 'totalPaid',
        'raw' => 'COALESCE(SUM(invoice_payments.amount), 0) as totalPaid',
        'title' => 'admin/invoices.totalPaid',
        'callback' => 'localNumber',
        'filter' => [
            'paid' => 'admin/invoices.paid',
            'partiallyPaid' => 'admin/invoices.partiallyPaid',
            'notPaid' => 'admin/invoices.notPaid'
        ],
        'filterDefinitions' => [
            'notPaid' => ['=', 0],
            'partiallyPaid' => ['<', function () {
                return \DB::raw('total and totalPaid > 0');
            }],
            'paid' => ['=', function () {
                return \DB::raw('total');
            }],
        ],
    ], [
        'name' => 'amountLeft',
        'raw' => '(invoices.amount + (invoices.amount * invoices.tax/100) + invoices.extra_amount) - COALESCE(SUM(invoice_payments.amount),0) as amountLeft',
        'title' => 'admin/invoices.amountLeft',
        'callback' => 'localNumber',
        'filter' => false
    ]]
];
