<?php
/**
 * Author: Taniya Tucker
 * Date: 6/23/25
 * File: AdminMiddleware.php
 * Description: limit admin features to correct users
 */

namespace api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write("Unauthorized - Admin Only");
            return $response->withStatus(403);
        }

        return $handler->handle($request);
    }
}
