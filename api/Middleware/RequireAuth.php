<?php
/**
 * Author: Taniya Tucker
 * Date: 6/5/25
 * File: RequireAuth.php
 * Description: only logged in users can access everything
 */

namespace api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequireAuth implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!isset($_SESSION['user'])) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write("Unauthorized - Not logged in");
            return $response->withStatus(401);
        }

        return $handler->handle($request);
    }
}


