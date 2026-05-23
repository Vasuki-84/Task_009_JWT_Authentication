<?php

require_once "../config/config.php";

require_once "../app/core/Database.php";
require_once "../app/core/Router.php";

require_once "../app/helpers/JWT.php";
require_once "../app/helpers/Response.php";

require_once "../app/middleware/JsonMiddleware.php";
require_once "../app/middleware/AuthMiddleware.php";

require_once "../app/models/User.php";
require_once "../app/models/Patient.php";

require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/PatientController.php";

// Run JSON middleware
JsonMiddleware::handle();

// Database connection
$db = (new Database())->connect();

// Initialize router
$router = new Router();

// Controllers
$authController = new AuthController($db);

$patientController = new PatientController($db);

// Auth Routes
$router->add(
    'POST',
    '/api/register',
    [$authController, 'register']
);

$router->add(
    'POST',
    '/api/login',
    [$authController, 'login']
);

// Patient Routes
$router->add(
    'GET',
    '/api/patients',
    [$patientController, 'index']
);

$router->add(
    'POST',
    '/api/patients',
    [$patientController, 'store']
);

$router->add(
    'PUT',
    '/api/patients/{id}',
    [$patientController, 'update']
);

$router->add(
    'DELETE',
    '/api/patients/{id}',
    [$patientController, 'delete']
);

// Get URI
$requestUri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

$requestUri = str_replace(
    '/task_009/public',
    '',
    $requestUri
);

// Dispatch route
$router->dispatch(
    $requestUri,
    $_SERVER['REQUEST_METHOD']
);