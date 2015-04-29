<?php

namespace local_api;

class Api
{
    public $json = true;

    public static function error($string, $httpStatusCode = null)
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
