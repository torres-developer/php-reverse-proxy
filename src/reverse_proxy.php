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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

function reverse_proxy(string $target, RequestInterface $req, bool $serve = true): ResponseInterface
{
    $proxy = new ReverseProxy(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), $target);
    $res = $proxy->sendRequest($req);

    if ($serve) {
        http_response_code($res->getStatusCode());

        $headers = array_keys($res->getHeaders());

        foreach ($headers as $h) {
            header($res->getHeaderLine($h));
        }

        echo $res->getBody()->getContents() ?: null;
    }

    return $res;
}
