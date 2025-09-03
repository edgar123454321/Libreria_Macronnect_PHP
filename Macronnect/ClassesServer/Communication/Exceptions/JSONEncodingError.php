<?php 
    require_once(__DIR__ . "/../../GeneralError.php");

    class JSONEncodingError extends GeneralError {

        public function __construct(string $message) {
            parent::__construct("JSONEncodingError", $message);
        }

    }


?>