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

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

final class ClientException extends \Exception implements NetworkExceptionInterface
{
    private RequestInterface $req;

    public static function fromException(\Exception $e, RequestInterface $req): static
    {
        $new = new static($e->getMessage(), $e->getCode(), $e);
        $new->setRequest($req);

        return $new;
    }

    public function getRequest(): RequestInterface
    {
        return $this->req;
    }

    private function setRequest(RequestInterface $req): void
    {
        $this->req = $req;
    }
}
