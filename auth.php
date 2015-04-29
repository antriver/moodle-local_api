<?php

/**
 * Check if a user's password is correct, returns minimal information about the user if so.
 * Send a POST request to this page with the user's email ('email') and plaintext password ('password')
 * to use. Returns a JSON response.
 *
 * @package    local_api
 * @copyright  Anthony Kuske <www.anthonykuske.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once $CFG->dirroot . '/lib/password_compat/lib/password.php';

$api = new local_api\Api();
//$api->debug();

/**
 * Parameters
 */

$successText = null;
$mode = !empty($_POST['mode']) ? $_POST['mode'] : 'post';

$response = array();
$username = null;
$password = null;

switch ($mode) {

    case 'http':

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']), 'basic') === 0) {
                list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
            }
        }

        $api->setJson(true);

        break;

    case 'post':

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }

        $api->setJson(true);

        break;

    case 'pam':

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        }

        $successText = 'OK';
        $api->setJson(false);

        break;

    default:
        $api->error('invalid_mode', 400);

}

if (empty($username)) {
    $api->error('auth_no_username', 403);
}

if (empty($password)) {
    $api->error('auth_no_password', 403);
}

/**
 * Load the user's info from the database
 */
$user = $DB->get_record('user', array('username' => $username));
if (!$user) {
    $api->error('auth_user_not_found', 403);
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
        'auth' => $user->auth
    );
} else {
    $api->error('auth_incorrect_password', 403);
}

/**
 * Success!
 */
if ($successText) {
    echo $successText;
} else {
    echo json_encode($response);
}
