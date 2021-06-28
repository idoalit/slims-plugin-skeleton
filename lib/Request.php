<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 27/06/2021 0:51
 * @File name           : Request.php
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

namespace Plugin\Lib;


use MPScholten\RequestParser\Psr7\ControllerHelperTrait;
use MPScholten\RequestParser\TypeParser;
use Psr\Http\Message\ServerRequestInterface;
use Volnix\CSRF\CSRF;

class Request
{
    use ControllerHelperTrait;

    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->initRequestParser($request);
    }

    function isValid() {
        return CSRF::validate($this->all());
    }

    function all() {
        return $this->request->getParsedBody();
    }

    function input($name): TypeParser
    {
        return $this->bodyParameter($name);
    }

    function query($name): TypeParser
    {
        return $this->queryParameter($name);
    }

    public function __get($name)
    {
        return $this->request->getParsedBody()[$name] ?? null;
    }
}