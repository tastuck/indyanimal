<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: AdminController.php
 * Description:
 */

namespace api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use api\Authentication\SessionManager;
use api\Models\AdminModel;
use api\Models\Media;

class AdminController
{
    // Admin dashboard view
    public function dashboard(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/admindashboard.php';
        $response->getBody()->write(ob_get_clean());
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function viewInvitePage(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/admin_invites.php';
        $response->getBody()->write(ob_get_clean());
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function viewMediaPage(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/admin_media.php';
        $response->getBody()->write(ob_get_clean());
        return $response->withHeader('Content-Type', 'text/html');
    }

    public function viewEventPage(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../app/admin_events.php';
        $response->getBody()->write(ob_get_clean());
        return $response->withHeader('Content-Type', 'text/html');
    }

    // Media approval endpoints
    public function pendingMedia(Request $request, Response $response): Response
    {
        $media = Media::getPendingMediaWithDetails();
        $response->getBody()->write(json_encode($media));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function approveMedia(Request $request, Response $response, array $args): Response
    {
        Media::approveMedia((int)$args['id']);
        $response->getBody()->write("Media {$args['id']} approved.");
        return $response;
    }

    public function rejectMedia(Request $request, Response $response, array $args): Response
    {
        Media::rejectMedia((int)$args['id']);
        $response->getBody()->write("Media {$args['id']} rejected.");
        return $response;
    }

    // Invite management
    public function createInvite(Request $request, Response $response): Response
    {
        $userId = SessionManager::getUserId();
        $result = AdminModel::createInvite($userId);

        if (!$result) {
            return $response->withStatus(403)->write("Monthly invite limit reached.");
        }

        $response->getBody()->write(json_encode(['invite_code' => $result]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Event management
    public function createEvent(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $adminId = SessionManager::getUserId();
        AdminModel::createEvent($data['title'], $data['event_date'], $adminId);

        $response->getBody()->write("Event created.");
        return $response;
    }

    public function listEvents(Request $request, Response $response): Response
    {
        $events = AdminModel::getAllEvents();
        $response->getBody()->write(json_encode($events));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function updateEvent(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        AdminModel::updateEvent((int)$args['id'], $data['title'], $data['event_date']);

        $response->getBody()->write("Event updated.");
        return $response;
    }

    public function createStage(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        AdminModel::createStage($data['event_id'], $data['name'], $data['description'] ?? null);

        $response->getBody()->write("Stage created.");
        return $response;
    }
}


