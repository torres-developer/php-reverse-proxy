# Reverse Proxy

A reverse proxy with PHP.

# Why?

My school gives to the students access to a server.

We can serve static files and PHP it's also supported. That is why this is in
PHP.

Let's say I created an HTTP server at localhost port 3000 (with PHP, node.js,
...). I can't make people able to access it over the internet because I don't
have privileges to change the Apache server config or whatever to create a
reverse proxy.

I'm trying to use the tools that were given to me :)

# Code Example

``` php
<?php

use Psr\Http\Message\UploadedFileInterface;
use TorresDeveloper\HTTPMessage\Headers;
use TorresDeveloper\HTTPMessage\HTTPVerb;
use TorresDeveloper\HTTPMessage\Request;
use TorresDeveloper\HTTPMessage\ServerRequest;
use TorresDeveloper\HTTPMessage\Stream;
use TorresDeveloper\HTTPMessage\UploadedFile;
use TorresDeveloper\HTTPMessage\URI;

use function TorresDeveloper\ReverseProxy\reverse_proxy;

$uri = (($_SERVER["HTTP_HOST"] ?? "") . ($_SERVER["REQUEST_URI"] ?? ""));

$uri = new URI($uri ?: $_GET[$this->cfg->get(
    "path_search_param"
)] ?? null, false);

$method = HTTPVerb::from($_SERVER["REQUEST_METHOD"]);

$body = new Stream(new \SplFileObject("php://input"));

$headers = new Headers();

$keys = array_keys($_SERVER);
foreach ($keys as $k) {
    if (str_starts_with($k, "HTTP_")) {
        $header = strtr(mb_substr($k, 5), "_", "-");

        $headers->$header = $_SERVER[$k];
    }
}

$req = new ServerRequest($uri, $method, $body, $headers);

$req = $req->withCookieParams(array_merge($_COOKIE, $_SESSION))
    ->withUploadedFiles(array_filter(array_map(UploadedFile::from_FILES(...), $_FILES), fn ($i) => $i instanceof UploadedFileInterface));

$contents = null;

try {
    $contents = $body->getContents();
} catch (\Throwable) {
    $contents = "";
}

reverse_proxy("http://localhost:3000/", $req);
```
