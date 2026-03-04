<?php
/**
 * Sales Module Routes
 * 
 * These routes handle all sales-related pages and actions.
 * @param Router $router The router instance
 */

// Require controller files
require_once __DIR__ . '/../Sales/SalesHomeController.php';
require_once __DIR__ . '/../Sales/SaleStartController.php';
require_once __DIR__ . '/../Sales/SaleDraftController.php';
require_once __DIR__ . '/../Sales/SalesHistoryController.php';
require_once __DIR__ . '/../Sales/RegisterStatusController.php';
require_once __DIR__ . '/../Sales/BirReadinessController.php';
require_once __DIR__ . '/../Sales/SaleRegisterController.php';
require_once __DIR__ . '/../Sales/SaleOrSeriesController.php';
require_once __DIR__ . '/../Sales/SaleLineController.php';
require_once __DIR__ . '/../Sales/SalePostController.php';
require_once __DIR__ . '/../Sales/OrIssueController.php';
require_once __DIR__ . '/../Controllers/Sales/PaymentsController.php';

// Main sales dashboard
$router->get('/sales', function() {
    $controller = new POS\Sales\SalesHomeController();
    $controller->index();
});

// Start new sale flow
$router->get('/sales/start', function() {
    $controller = new POS\Sales\SaleStartController();
    $controller->index();
});

$router->post('/sales/start', function() {
    $controller = new POS\Sales\SaleStartController();
    $controller->create();
});

// View draft sale
$router->get('/sales/draft', function() {
    $controller = new POS\Sales\SaleDraftController();
    $controller->index();
});

// Register and OR series selection
$router->post('/sales/register/update', function() {
    $controller = new POS\Sales\SaleRegisterController();
    $controller->update();
});

$router->post('/sales/or-series/update', function() {
    $controller = new POS\Sales\SaleOrSeriesController();
    $controller->update();
});

// Line item management
$router->post('/sales/line/add', function() {
    $controller = new POS\Sales\SaleLineController();
    $controller->add();
});

$router->post('/sales/line/remove', function() {
    $controller = new POS\Sales\SaleLineController();
    $controller->remove();
});

// Post sale
$router->post('/sales/draft/post', function() {
    $controller = new POS\Sales\SalePostController();
    $controller->post();
});

// OR issuance
$router->post('/sales/or/issue', function() {
    $controller = new POS\Sales\OrIssueController();
    $controller->issue();
});

// Payment routes
$router->post('/sales/payments/record', function() {
    $controller = new POS\Controllers\Sales\PaymentsController();
    $controller->record();
});

// Sales history
$router->get('/sales/history', function() {
    $controller = new POS\Sales\SalesHistoryController();
    $controller->index();
});

// Register status
$router->get('/sales/register-status', function() {
    $controller = new POS\Sales\RegisterStatusController();
    $controller->index();
});

// BIR readiness
$router->get('/sales/bir-readiness', function() {
    $controller = new POS\Sales\BirReadinessController();
    $controller->index();
});