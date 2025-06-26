<?php
/**
 * Author: Taniya Tucker
 * Date: 6/24/25
 * File: PaymentController.php
 * Description:
 */

namespace api\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use api\Models\Order;
use api\Models\Event;

class PaymentController
{
    // handle Stripe webhook for successful checkouts
    public function handleWebhook(Request $request, Response $response): Response
    {
        $payload = (string) $request->getBody();
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            error_log('Stripe webhook error: ' . $e->getMessage());
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'text/plain')
                ->write('Invalid webhook');
        }

        // mark the order as complete when checkout finishes
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $providerOrderId = $session->id;
            Order::markAsComplete($providerOrderId);
        }

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(['status' => 'ok']));
    }

    // start a Stripe checkout session for an event
    public function createSession(Request $request, Response $response, array $args): Response
    {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? '');

        $eventId = (int)$args['id'];
        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId) {
            return $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Not logged in']));
        }

        $event = Event::getById($eventId);
        if (!$event) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Event not found']));
        }

        $adminUserId = $event['admin_user_id'];
        $amount = $event['price_cents'] ?? 500;
        $platformFee = 50;

        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => 'Event Ticket #' . $eventId],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => "http://localhost:8000/event/{$eventId}?success=true",
                'cancel_url' => "http://localhost:8000/event/{$eventId}?canceled=true",
            ]);

            // log the pending order in the database
            Order::create([
                'user_id' => $userId,
                'event_id' => $eventId,
                'admin_user_id' => $adminUserId,
                'amount' => $amount,
                'platform_fee' => $platformFee,
                'provider_order_id' => $session->id,
                'status' => 'pending'
            ]);

            $response->getBody()->write(json_encode(['url' => $session->url]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
