<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local_api
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_api\local;

class api
{
    public $oputputjson = true;

    /**
     * Dump all input to a log file.
     */
    public function debug() {

        $debug = print_r($_GET, true) . print_r($_POST, true) . print_r($_SERVER, true);
        file_put_contents(dirname(__FILE__) . '/../debug.txt', $debug);
    }

    /**
     * Output an error message and quit.
     *
     * @param  string $string         Name of a string in the language file to output.
     * @param  int $statuscode
     */
    public function error($string, $statuscode = null) {

        if ($statuscode) {
            http_response_code($statuscode);
        }

        if ($this->oputputjson) {
            die(json_encode(array('error' => get_string($string, 'local_api'))));
        } else {
            die(get_string($string, 'local_api'));
        }
    }

    /**
     * Make errors output as JSON instead of plain text
     *
     * @param boolean $oputputjson
     */
    public function set_json_output($oputputjson = true) {

        if ($oputputjson) {
            $this->oputputjson = true;
            header('Content-type: application/json');
        } else {
            $this->oputputjson = false;
        }
    }
}
