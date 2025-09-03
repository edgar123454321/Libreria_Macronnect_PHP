<?php 

    class Parameter_GET {

        private string $key;
        private string $operator;
        private string $value;

        
        // Builders
        public function __construct(string $key, string $operator, string $value) {
            $this->key = $key;
            $this->operator = $operator;
            $this->value = $value;
        }


        // GETTER AND SETTER
        public function getKey(): string {
            return $this->key;
        }
        public function setKey(string $key): void {
            $this->key = $key;
        }

        public function getOperator(): string {
            return $this->operator;
        }
        public function setOperator(string $operator): void {
            $this->operator = $operator;
        }

        public function getValue(): string {
            return $this->value;
        }
        public function setValue(string $value): void {
            $this->value = $value;
        }


    }



?>