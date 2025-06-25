<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: admin.php
 * Description: admin functions routes
 */

use Slim\App;
use api\Controllers\AdminController;
use api\Middleware\RequireAuth;
use api\Middleware\AdminMiddleware;

return function (App $app) {

    $app->get('/admin', [AdminController::class, 'dashboard'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->get('/api/admin/events', [AdminController::class, 'viewEventPage'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->get('/api/admin/events/list', [AdminController::class, 'listEvents']) // ✅ renamed to avoid collision
    ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/invite/create', [AdminController::class, 'createInvite'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/event/create', [AdminController::class, 'createEvent'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/stage/create', [AdminController::class, 'createStage'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/event/update/{id}', [AdminController::class, 'updateEvent'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->get('/admin/media', [AdminController::class, 'viewMediaPage'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->get('/admin/media/pending', [AdminController::class, 'pendingMedia'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/media/approve/{id}', [AdminController::class, 'approveMedia'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->post('/admin/media/reject/{id}', [AdminController::class, 'rejectMedia'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());

    $app->get('/admin/invite', [AdminController::class, 'viewInvitePage'])
        ->add(new AdminMiddleware())
        ->add(new RequireAuth());
};
