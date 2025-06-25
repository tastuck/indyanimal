<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: EventController.php
 * Description:
 */


namespace api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use api\Models\Event;

class EventController
{
    // show a specific event by ID
    public function viewEvent(Request $request, Response $response, array $args): Response
    {
        $eventId = (int)$args['id'];
        $event = Event::getById($eventId);

        if (!$event) {
            $response->getBody()->write("Event not found.");
            return $response->withStatus(404);
        }

        ob_start();
        include __DIR__ . '/../../app/event_page.php';
        $response->getBody()->write(ob_get_clean());

        return $response->withHeader('Content-Type', 'text/html');
    }

    // return event JSON (for checkout logic)
    public function getEventJSON(Request $request, Response $response, array $args): Response
    {
        $eventId = (int)$args['id'];
        $event = Event::getById($eventId);

        if (!$event) {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($event));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // show a list of all events (searchable/browsable)
    public function listAllEvents(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $title = $queryParams['title'] ?? null;
        $date = $queryParams['date'] ?? null;

        $events = Event::searchEvents($title, $date);

        // Just show HTML page for now (will fetch events via JS)
        ob_start();
        include __DIR__ . '/../../app/events.php';
        $response->getBody()->write(ob_get_clean());

        return $response->withHeader('Content-Type', 'text/html');
    }

    // json might circle badk to this
    public function searchEventsJSON(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $title = $queryParams['title'] ?? null;
        $date = $queryParams['date'] ?? null;

        $events = Event::searchEvents($title, $date);

        $response->getBody()->write(json_encode($events));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUpcomingEventsJSON(Request $request, Response $response): Response
    {
        $events = Event::getUpcomingEvents();
        $response->getBody()->write(json_encode($events));
        return $response->withHeader('Content-Type', 'application/json');
    }

}
