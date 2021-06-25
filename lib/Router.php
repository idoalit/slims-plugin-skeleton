<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 25/06/2021 22:05
 * @File name           : Router.php
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

use AltoRouter;

class Router extends AltoRouter
{
    /**
     * @throws \Exception
     */
    public function get($route, $target, $name = null) {
        $this->map('GET', $route, $target, $name);
    }

    /**
     * @throws \Exception
     */
    public function post($route, $target, $name = null) {
        $this->map('POST', $route, $target, $name);
    }

    public function match($requestUrl = null, $requestMethod = null)
    {
        $params = array();
        $match = false;

        // set Request Url if it isn't passed as parameter
        if ($requestUrl === null) $requestUrl = $_GET['sec'] ?? '/';

        // strip base path from request url
        $requestUrl = substr($requestUrl, strlen($this->basePath));

        // Strip query string (?a=b) from Request Url
        if (($strpos = strpos($requestUrl, '?')) !== false) {
            $requestUrl = substr($requestUrl, 0, $strpos);
        }

        // set Request Method if it isn't passed as a parameter
        if ($requestMethod === null) {
            $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        }

        foreach ($this->routes as $handler) {
            list($methods, $route, $target, $name) = $handler;

            $method_match = (stripos($methods, $requestMethod) !== false);

            // Method did not match, continue to next route.
            if (!$method_match) continue;

            if ($route === '*') {
                // * wildcard (matches all)
                $match = true;
            } elseif (isset($route[0]) && $route[0] === '@') {
                // @ regex delimiter
                $pattern = '`' . substr($route, 1) . '`u';
                $match = preg_match($pattern, $requestUrl, $params) === 1;
            } elseif (($position = strpos($route, '[')) === false) {
                // No params in url, do string comparison
                $match = strcmp($requestUrl, $route) === 0;
            } else {
                // Compare longest non-param string with url
                if (strncmp($requestUrl, $route, $position) !== 0) {
                    continue;
                }
                $regex = $this->compileRoute($route);
                $match = preg_match($regex, $requestUrl, $params) === 1;
            }

            if ($match) {

                if ($params) {
                    foreach ($params as $key => $value) {
                        if (is_numeric($key)) unset($params[$key]);
                    }
                }

                return array(
                    'target' => $target,
                    'params' => $params,
                    'name' => $name
                );
            }
        }
        return false;
    }

    public function makeCallable($method)
    {
        if (is_string($method)) $method = explode('@', $method);
        if (isset($method[1]) && class_exists($method[0])) {
            $instance = new $method[0];
            if (method_exists($instance, $method[1])) {
                return array($instance, $method[1]);
            }
        }
        return false;
    }

    public function run()
    {
        // match current request url
        $match = $this->match();

        // call closure or throw 404 status
        if ($match && !is_array($match['target']) && is_callable($match['target'])) {
            call_user_func_array($match['target'], $match['params']);
        } else {
            if ($callable = $this->makeCallable($match['target'])) {
                call_user_func_array($callable, $match['params']);
            } else {
                // no route was matched
                header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
                include __DIR__ . '/../app/Views/errors/404.php';
            }
        }
    }
}