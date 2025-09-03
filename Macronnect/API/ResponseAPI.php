<?php 


class ResponseAPI {

    // Type Return
    const FETCH_ORIGINAL = "ORIGINAL";
    const FETCH_TABLE = "TABLE";

    // GET, POST, ....
    private ?string $typeRequest = null;

    // Variables GENERAL
    private ?string $actualToken = null;
    private ?string $request = null;
    private ?array $responseEndpoint = null;

    // POST
    private ?array $parametersSend_POST = null;


    // Builder
    public function __construct(string $typeRequest) {
        $this->typeRequest = $typeRequest;
    }

    public function fetchAll(string $typeFetch): array {
        if ($typeFetch == self::FETCH_ORIGINAL) {
            return $this->responseEndpoint;
        }
        elseif ($typeFetch == self::FETCH_TABLE) {
            $headers = $this->responseEndpoint["encabezados"];
            $values = $this->responseEndpoint["valores"];
            $records = [];
            foreach($values as $record) {
                // Iterate in Each Name Field
                $record_tmp = [];   
                for($i=0; $i < count($headers); $i++) {
                    $header = $headers[$i];
                    $value = $record[$i];
                    $record_tmp[$header] = $value;
                }
                $records[] = $record_tmp;
            }
            return $records;
        }
        else {
            throw new GeneralError("Error Tipo Fetch '$typeFetch'", "El Tipo de Fetch '$typeFetch' no ha sido definido en ResponseAPI");
        }
    }


    public function fetch(string $typeFetch) {
        if ($typeFetch == self::FETCH_ORIGINAL) {
            if (count($this->responseEndpoint) == 0) {
                return [];
            }
            return ($this->responseEndpoint)[0];
        }

        throw new GeneralError("Error Tipo Fetch '$typeFetch'", "El Tipo de Fetch '$typeFetch' no ha sido definido en ResponseAPI");
    }


    public function getAsArray(): array {
        if ($this->typeRequest == "GET") {
            return [
                "Actual Token" => $this->actualToken,
                "Tipo Solicitud" => $this->typeRequest,
                "Request" => $this->request,
                "Response Endpoint" => $this->responseEndpoint
            ];
        }
        elseif ($this->typeRequest == "POST") {
            return [
                "Actual Token" => $this->actualToken,
                "Tipo Solicitud" => $this->typeRequest,
                "Request" => $this->request,
                "Parameters Sent POST" => $this->parametersSend_POST,
                "Response Endpoint" => $this->responseEndpoint
            ];
        }
        else {
            throw new GeneralError("Metodo '" . $this->typeRequest . "' No Definido", "No se ha definido el Metodo '" . $this->typeRequest . "' en ResponseAPI");
        }
    }



    // GETTER AND SETTER
    public function getTypeRequest(): ?string {
        return $this->typeRequest;
    }
    public function setTypeRequest(string $typeRequest): void {
        $this->typeRequest = $typeRequest;
    }

    public function getActualToken(): ?string {
        return $this->actualToken;
    }
    public function setActualToken(string $actualToken): void {
        $this->actualToken = $actualToken;
    }

    public function getRequest(): ?string {
        return $this->request;
    }
    public function setRequest(string $request): void {
        $this->request = $request;
    }

    public function getResponseEndpoint(): ?array {
        return $this->responseEndpoint;
    }
    public function setResponseEndpoint(array $responseEndpoint): void {
        $this->responseEndpoint = $responseEndpoint;
    }

    public function getParameterSend_POST(): ?array {
        return $this->parametersSend_POST;
    }
    public function setParametersSend_POST(array $parametersSend_POST): void {
        $this->parametersSend_POST = $parametersSend_POST;
    }


}



?>