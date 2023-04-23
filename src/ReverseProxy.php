<?php

/**
 *  ReverseProxy - A reverse proxy in PHP.
 *  Copyright (C) 2023  João Augusto Costa Branco Marado Torres
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace TorresDeveloper\ReverseProxy;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use TorresDeveloper\HTTPMessage\HTTPVerb;
use TorresDeveloper\HTTPMessage\Response;

use function TorresDeveloper\Pull\pull;

final class ReverseProxy implements ClientInterface
{
    private string $proxyPath;
    private string $target;

    public function __construct(string $proxyPath, UriInterface $target)
    {
        $this->proxyPath = rtrim((string) $proxyPath, "/");
        $this->target = rtrim($target, "/");
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $requestPath = $request->getUri()->getPath() ?: "/";

        if (str_starts_with($requestPath, $this->proxyPath)) {
            $target = $this->target . substr($requestPath, strlen($this->proxyPath));

            try {
                return pull($target, HTTPVerb::from($request->getMethod()), $request->getBody(), $request->getHeaders());
            } catch (\Throwable $th) {
                throw new ClientException(previous: $th);
            }
        }

        return new Response(404);
    }
}
