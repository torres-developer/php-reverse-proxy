# Change Log

All notable changes to this publication will be documented in this file.

## 1.0.0 - 2023-21-4

First release.

Provides the `ReverseProxy` class where you can specify the path of the
endpoint to reverse proxy to the target that also will be defined.

Returns a PSR-7 `ResponseInterface`. It will be with status code `404` in case
it couldn't reverse proxy since the request to possibly reverse proxy didn't
start with the path of the endpoint defined on the earlier.

Also gives access to a helper function that just does the reverse proxy right
away.
