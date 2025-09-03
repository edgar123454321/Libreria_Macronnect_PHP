<?php 

    /**
     * Class General Error
     */
    class GeneralError extends Exception {

        /** @var string */
        private $typeError;

        /** @var string */
        private $messageError;

        /**
         * General Error Construct
         */
        public function __construct(string $typeError, string $messageError) {
            parent::__construct($messageError, 0); // Code 0
            $this->typeError = $typeError;
            $this->messageError = $messageError;
        }
        
        /**
         * GETTER 
         */
        public function getTypeError() {
            return $this->typeError;
        }

        public function getMessageError() {
            return $this->messageError;
        }
    }

?>