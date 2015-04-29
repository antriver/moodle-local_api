<?php

namespace local_api;

class Api
{
    public $json = true;

    public function debug()
    {
        $debug = print_r($_GET, true) . print_r($_POST, true) . print_r($_SERVER, true);
        file_put_contents(dirname(__FILE__) . '/../debug.txt', $debug);
    }

    public function error($string, $httpStatusCode = null)
    {
        if ($httpStatusCode) {
            http_response_code($httpStatusCode);
        }

        if ($this->json) {
            die(json_encode(array('error' => get_string($string, 'local_api'))));
        } else {
            die(get_string($string, 'local_api'));
        }

    }

    public function setJson($json)
    {
        if ($json) {
            $this->json = true;
            header('Content-type: application/json');
        } else {
            $this->json = false;
        }
    }
}
