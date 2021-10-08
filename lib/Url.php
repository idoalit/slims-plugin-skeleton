<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 30/08/2021 12:06
 * @File name           : Url.php
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


class Url
{
    static function section($section_path = null, $page = null) {
        $query = [
            'p' => $page ?? $_GET['p'],
            'sec' => $section_path ?? $_GET['p'] ?? '/'
        ];
        return SWB . 'index.php?' . http_build_query($query);
    }

    static function adminSection($section_path = null) {
        $query = [
            'mod' => $_GET['mod'],
            'id' => $_GET['id'],
            'sec' => $section_path ?? $_GET['p'] ?? '/'
        ];
        return $_SERVER['PHP_SELF'] . '?' . http_build_query($query);
    }

    static function goto($url) {
        echo <<<HTML
        <script>parent.$('#mainContent').simbioAJAX('{$url}')</script>
HTML;
        exit();
    }

    static function gotoAndClosePopUp($url) {
        echo <<<HTML
        <script>
            top.$.colorbox.close()
            parent.$('#mainContent').simbioAJAX('{$url}')
        </script>
HTML;
        exit();
    }
}
