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

header('Content-type: application/json');

$response = array();

/**
 * Parameters
 */
if (!empty($_POST['email'])) {
    $email = $_POST['email'];
} else {
    die(json_encode(array('error' => get_string('auth_no_email', 'local_api'))));
}

if (!empty($_POST['password'])) {
    $password = $_POST['password'];
} else {
    die(json_encode(array('error' => get_string('auth_no_password', 'local_api'))));
}

/**
 * Load the user's info from the database
 */
$user = $DB->get_record('user', array('email' => $email));
if (!$user) {
    die(json_encode(array('error' => get_string('auth_user_not_found', 'local_api'))));
}

/**
 * Check the password is correct
 */
if (password_verify($password, $user->password)) {
    // TODO: Add settings to define what gets returned
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
    die(json_encode(array('error' => get_string('auth_incorrect_password', 'local_api'))));
}

echo json_encode($response);
