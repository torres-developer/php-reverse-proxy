# Change Log

All notable changes to this publication will be documented in this file.

## 1.0.2 - 2023-21-4
Two new helper functions:
* `serverRequest` - creates a `ServerRequestInterface` that you can use for the
  `ReverseProxy::sendRequest` method. It uses PHP superglobals like `$_SERVER`,
  `$_COOKIES`, `$_SESSION`, `$_FILES`, to create the object;
* `respond` - Can respond the status code, headers, body of a
  `ResponseInterface` to the client;

## 1.0.1 - 2023-21-4
Exception classes.

## 1.0.0 - 2023-21-4

First release.

Provides the `ReverseProxy` class where you can specify the path of the
endpoint to reverse proxy to the target that also will be defined.

Returns a PSR-7 `ResponseInterface`. It will be with status code `404` in case
it couldn't reverse proxy since the request to possibly reverse proxy didn't
start with the path of the endpoint defined on the earlier.

Also gives access to a helper function that just does the reverse proxy right
away.
