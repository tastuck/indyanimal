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
    // show a specific event page by ID
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

    // return a single event as JSON (used in checkout flow)
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

    // show the full list of events (JS fetches actual data)
    public function listAllEvents(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $title = $queryParams['title'] ?? null;
        $date = $queryParams['date'] ?? null;

        $events = Event::searchEvents($title, $date);

        ob_start();
        include __DIR__ . '/../../app/events.php';
        $response->getBody()->write(ob_get_clean());

        return $response->withHeader('Content-Type', 'text/html');
    }

    // return a filtered list of events as JSON
    public function searchEventsJSON(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $title = $queryParams['title'] ?? null;
        $date = $queryParams['date'] ?? null;

        $events = Event::searchEvents($title, $date);

        $response->getBody()->write(json_encode($events));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // return upcoming events only (used on dashboard or landing)
    public function getUpcomingEventsJSON(Request $request, Response $response): Response
    {
        $events = Event::getUpcomingEvents();
        $response->getBody()->write(json_encode($events));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
