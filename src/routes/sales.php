<?php
/**
 * Sales Module Routes
 * 
 * These routes handle all sales-related pages and actions.
 * All routes are protected by authentication and permissions.
 */

// Main sales dashboard
Router::get('/sales', 'POS\Sales\SalesHomeController@index', [
    'auth' => true,
    'perm' => 'sales.view'
]);

// Start new sale flow
Router::get('/sales/start', 'POS\Sales\SaleStartController@index', [
    'auth' => true,
    'perm' => 'sales.create'
]);
Router::post('/sales/start', 'POS\Sales\SaleStartController@create', [
    'auth' => true,
    'perm' => 'sales.create'
]);

// View draft sale
Router::get('/sales/draft', 'POS\Sales\SaleDraftController@index', [
    'auth' => true,
    'perm' => 'sales.view'
]);

// Register and OR series selection
Router::post('/sales/register/update', 'POS\Sales\SaleRegisterController@update', [
    'auth' => true,
    'perm' => 'sales.create'
]);
Router::post('/sales/or-series/update', 'POS\Sales\SaleOrSeriesController@update', [
    'auth' => true,
    'perm' => 'sales.create'
]);

// Line item management
Router::post('/sales/line/add', 'POS\Sales\SaleLineController@add', [
    'auth' => true,
    'perm' => 'sales.create'
]);
Router::post('/sales/line/remove', 'POS\Sales\SaleLineController@remove', [
    'auth' => true,
    'perm' => 'sales.create'
]);

// Post sale
Router::post('/sales/draft/post', 'POS\Sales\SalePostController@post', [
    'auth' => true,
    'perm' => 'sales.create'
]);

// OR issuance
Router::post('/sales/or/issue', 'POS\Sales\OrIssueController@issue', [
    'auth' => true,
    'perm' => 'sales.create'
]);

// Payment routes
Router::post('/sales/payments/record', 'POS\Controllers\Sales\PaymentsController@record', [
    'auth' => true,
    'perm' => 'sales.payments.manage'
]);

// Sales history and status pages
Router::get('/sales/history', 'POS\Sales\SalesHistoryController@index', [
    'auth' => true,
    'perm' => 'sales.view'
]);
Router::get('/sales/register-status', 'POS\Sales\RegisterStatusController@index', [
    'auth' => true,
    'perm' => 'sales.view'
]);
Router::get('/sales/bir-readiness', 'POS\Sales\BirReadinessController@index', [
    'auth' => true,
    'perm' => 'sales.view'
]);