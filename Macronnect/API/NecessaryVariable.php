<?php 

class NecessaryVariable {

    private string $nameVariable;
    private string $typeVariable;

    public function __construct(string $nameVariable, string $typeVariable) {

        // Validar Type Variable
        if (!self::validTypeVariable($typeVariable)) {
            throw new InvalidArgumentException("Tipo '$typeVariable' no es válido.");
        }

        $this->nameVariable = $nameVariable;
        $this->typeVariable = $typeVariable;
    }


    public static function validTypeVariable(string $typeVariable): bool {
        $validTypes = ["string", "int", "integer", "float", "double", "array", "bool", "boolean"];
        return in_array(strtolower($typeVariable), $validTypes, true);
    }


    // GETTER and SETTER
    public function getNameVariable(): string {
        return $this->nameVariable;
    }

    public function getTypeVariable(): string {
        return $this->typeVariable;
    }


}



?>