<?php


use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use api\Middleware\RequireAuth;

return function (App $app) {

    // Public routes
    $app->get('/signin', function ($request, $response, $args) {
        return (new \api\Controllers\AuthController())->showSignin($request, $response);
    });

    $app->post('/signin', function ($request, $response, $args) {
        return (new \api\Controllers\AuthController())->handleSignin($request, $response);
    });

    $app->get('/signup', function ($request, $response, $args) {
        return (new \api\Controllers\AuthController())->showSignup($request, $response);
    });

    $app->post('/signup', function ($request, $response, $args) {
        return (new \api\Controllers\AuthController())->handleSignup($request, $response);
    });

    $app->get('/mission', function ($request, $response, $args) {
        ob_start();
        include __DIR__ . '/../app/mission.php';
        $response->getBody()->write(ob_get_clean());
        return $response->withHeader('Content-Type', 'text/html');
    });


    // Protected routes
    $app->group('', function (RouteCollectorProxy $group) {

        $group->get('/signout', function ($request, $response, $args) {
            return (new \api\Controllers\AuthController())->logout($request, $response);
        });

        $group->get('/dashboard', function ($request, $response) {
            ob_start();
            include __DIR__ . '/../app/dashboard.php';
            $response->getBody()->write(ob_get_clean());
            return $response;
        });

        $group->post('/media/upload', function ($request, $response, $args) {
            return (new \api\Controllers\MediaController())->uploadMedia($request, $response);
        });

        $group->get('/media', function ($request, $response, $args) {
            return (new \api\Controllers\MediaController())->listApprovedMedia($request, $response);
        });

        $group->get('/media/upload', function ($request, $response, $args) {
            return (new \api\Controllers\MediaController())->viewUploadPage($request, $response);
        });

        $group->get('/media/search', function ($request, $response, $args) {
            return (new \api\Controllers\MediaController())->search($request, $response);
        });

        $group->get('/api/event/{id}', function ($request, $response, $args) {
            return (new \api\Controllers\EventController())->getEventJSON($request, $response, $args);
        });

        $group->get('/event/{id}', function ($request, $response, $args) {
            include __DIR__ . '/../app/event.php';
            return $response;
        });

        $group->post('/payment/create-session/{id}', function ($request, $response, $args) {
            return (new \api\Controllers\PaymentController())->createSession($request, $response, $args);
        });

        $group->get('/api/tags', function ($request, $response) {
            $tags = \api\Models\Tag::getAllTags();
            $response->getBody()->write(json_encode($tags));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/events', function ($request, $response) {
            ob_start();
            include __DIR__ . '/../app/events.php';
            $response->getBody()->write(ob_get_clean());
            return $response->withHeader('Content-Type', 'text/html');
        });

        $group->get('/api/events', function ($request, $response) {
            return (new \api\Controllers\EventController())->searchEventsJSON($request, $response);
        });

        $group->get('/api/events/upcoming', function ($request, $response) {
            return (new \api\Controllers\EventController())->getUpcomingEventsJSON($request, $response);
        });



    })->add(new RequireAuth());
};
