<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: index.php
 * Description:
 */

// start PHP session
ini_set('session.cookie_samesite', 'Lax'); // safe default
session_start();

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->load();

use Slim\Factory\AppFactory;

$app = AppFactory::create();

// load routes
(require __DIR__ . '/../routes/routes.php')($app);
(require __DIR__ . '/../routes/admin.php')($app); // admin routes are separate so files arent cluttered

$app->run();