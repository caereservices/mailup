<?php

namespace Caereservices\Mailup;

class MailUpException extends \Exception {

    var $statusCode;

    function __construct($inStatusCode, $inMessage) {
        parent::__construct($inMessage);
        $this->statusCode = $inStatusCode;
    }

    function getStatusCode() {
        return $this->statusCode;
    }

    function setStatusCode($inStatusCode) {
        $this->statusCode = $inStatusCode;
    }
}

?>
