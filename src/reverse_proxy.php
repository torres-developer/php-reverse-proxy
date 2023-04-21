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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use TorresDeveloper\HTTPMessage\Headers;
use TorresDeveloper\HTTPMessage\HTTPVerb;
use TorresDeveloper\HTTPMessage\ServerRequest;
use TorresDeveloper\HTTPMessage\Stream;
use TorresDeveloper\HTTPMessage\UploadedFile;
use TorresDeveloper\HTTPMessage\URI;

function reverseProxy(string|UriInterface $target, RequestInterface $req = serverRequest(), bool $serve = true): ResponseInterface
{
    $proxy = new ReverseProxy(
        parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
        $target instanceof UriInterface ? $target : new URI($target)
    );

    $res = $proxy->sendRequest($req);

    if ($serve) {
        respond($res);
    }

    return $res;
}

function serverRequest(): ServerRequestInterface
{
    static $req;

    if ($req instanceof ServerRequestInterface) {
        return $req;
    } else {
        session_start();
    }

    $uri = new URI("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

    $method = HTTPVerb::from($_SERVER["REQUEST_METHOD"]);

    $body = new Stream(new \SplFileObject("php://input"));

    $headers = new Headers();
    $keys = array_keys($_SERVER);
    foreach ($keys as $k) {
        if (str_starts_with($k, "HTTP_")) {
            $header = strtr(substr($k, 5), "_", "-");
            $headers->$header = $_SERVER[$k];
        }
    }

    $req = new ServerRequest($uri, $method, $body, $headers);

    $req = $req->withCookieParams(array_merge($_COOKIE, $_SESSION))
        ->withUploadedFiles(array_filter(
            array_map(
                UploadedFile::from_FILES(...),
                $_FILES
            ),
            static fn ($i) => $i instanceof UploadedFileInterface
        ));

    return $req;
}

function respond(ResponseInterface $res): void
{
    http_response_code($res->getStatusCode());

    $headers = array_keys($res->getHeaders());

    foreach ($headers as $h) {
        header($res->getHeaderLine($h));
    }

    echo $res->getBody()->getContents() ?: null;
}
