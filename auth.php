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
 * Check if a user's password is correct, returns minimal information about the user if so.
 * Send a POST request to this page with the user's email ('email') and plaintext password ('password')
 * to use. Returns a JSON response.
 *
 * @package    local_api
 * @copyright  2015 Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/lib/password_compat/lib/password.php');

$api = new local_api\local\api();
// $api->debug();

/**
 * Parameters
 */

$successtext = null;
$mode = !empty($_POST['mode']) ? $_POST['mode'] : 'post';

$response = array();
$username = null;
$password = null;

switch ($mode) {

    case 'http':

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        } else if (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'basic') === 0) {
                list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            }
        }

        $api->set_json_output(true);

        break;

    case 'post':

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }

        $api->set_json_output(true);

        break;

    case 'pam':

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }

        $successtext = 'OK';
        $api->set_json_output(false);

        break;

    default:
        $api->error('invalid_mode', 400);

}

if (empty($username)) {
    $api->error('auth_no_username', 400);
}

if (empty($password)) {
    $api->error('auth_no_password', 400);
}

/**
 * Load the user's info from the database
 */
$user = $DB->get_record('user', array('username' => $username));
if (!$user) {
    $api->error('auth_user_not_found', 401);
}

/**
 * Check the password is correct
 */
if (password_verify($password, $user->password)) {
    // Specify what information to give in the response
    // (Don't want to give unnecessary stuff here)
    $response['user'] = array(
        'id' => $user->id,
        'idnumber' => $user->idnumber,
        'username' => $user->username,
        'email' => $user->email,
        'auth' => $user->auth,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
    );
} else {
    $api->error('auth_incorrect_password', 403);
}

/**
 * Success!
 */
if ($successtext) {
    echo $successtext;
} else {
    echo json_encode($response);
}
