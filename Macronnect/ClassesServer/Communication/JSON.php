<?php 

    require_once 'Exceptions/JSONEncodingError.php';

    /**
     * Class JSON that handler communication between Server-Client
     */
    class JSON {

        /**
         * Function Handle Unexpected Errors in Server to return Error in JSON
        */
        public static function handleUnexpectedError($errno, $errstr, $errfile, $errline): void {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $backtraceMessage = [];
            foreach ($backtrace as $trace) {
                $backtraceMessage[] = [
                    'file' => isset($trace['file']) ? $trace['file'] : null,
                    'line' => isset($trace['line']) ? $trace['line'] : null,
                    'function' => isset($trace['function']) ? $trace['function'] : null,
                    'class' => isset($trace['class']) ? $trace['class'] : null,
                    'type' => isset($trace['type']) ? $trace['type'] : null,
                ];
            }
            
            if (is_array($backtraceMessage)) {
                $backtraceMessage = json_encode($backtraceMessage);
            }

            header('Content-Type: application/json');        
            exit(json_encode([
                "Status" => "Error",
                "TypeError" => "Unexpected Server Error",
                "MessageError" => "[$errno] $errstr - $errfile:$errline <br> $backtraceMessage"
            ]));
        }

        /**
         * Function to return Success string in Format JSON
         * 
         * @param array $dataRequest - Any data object return to be readed by client
         * @return string 
         */
        public static function returnSuccess(array $dataResponse = []): string {
            $response = [];
            $response["Status"] = "Success";
            $response["DataResponse"] = $dataResponse;
            
            $json = json_encode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JSONEncodingError("Error al convertir a JSON el mensaje: '" . strval($response) . "' -> " . json_last_error_msg());
            }
            return $json;
        }
        

        /**
         * Function to return Error string in Format JSON
         * 
         * @param string $typeError
         * @param string $messageError
         * @return string
         */
        public static function returnError(string $typeError, string $messageError, array $dataResponse = []): string {
            $response = [];
            $response["Status"] = "Error";
            $response["TypeError"] = $typeError;
            $response["MessageError"] = $messageError;
            $response["DataResponse"] = $dataResponse;
            
            $json = json_encode($response);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new JSONEncodingError("Error al convertir a JSON el mensaje: '" . strval($response) . "' -> " . json_last_error_msg());
            }
            return $json;
        }

    }

?>