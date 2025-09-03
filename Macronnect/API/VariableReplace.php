<?php

class VariableReplace {

    private string $keyReplace;
    private string $valueReplace;

    // Builders
    public function __construct(string $keyReplace, string $valueReplace) {
        $this->keyReplace = $keyReplace;
        $this->valueReplace = $valueReplace;
    }


    // GETTER AND SETTER
    public function getKeyReplace(): string {
        return $this->keyReplace;
    }
    public function setKeyReplace(string $keyReplace): void {
        $this->keyReplace = $keyReplace;
    }

    public function getValueReplace(): string {
        return $this->valueReplace;
    }
    public function setValueReplace(string $valueReplace): void {
        $this->valueReplace = $valueReplace;
    }
}



?>