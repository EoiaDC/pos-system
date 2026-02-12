<?php

/**
 * TEMPORARY TEST SCRIPT - DEV B MODULE VALIDATION
 * This file will be deleted after integration testing
 * DO NOT COMMIT TO PRODUCTION
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\DbHealth;
use Dotenv\Dotenv;

// Load environment
$dotenv = 'Dotenv\Dotenv'::createImmutable(__DIR__);
$dotenv->load();

// Run health check
DbHealth::displayCliReport();
