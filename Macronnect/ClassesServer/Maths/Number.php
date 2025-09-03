<?php 

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

/**
 * Class Number
 * 
 * This Class is used to performs operations between numbers
 * Using this class we use functions like bcadd, bcsub, bcmul,
 * bcdiv to do basic operations between numbers and get 
 * accurate results in basic operations
 * 
 * Author Ing. Edgar Mora Santos 
 * 
 */
class Number {

    /** @var string */
    private $number;

    /**
     * Class Number Construct
     * 
     * @param string|null $number - string number to save. Null if is undefined 
     */
    public function __construct(?string $number) {
        // Limpiar comas de miles
        if ($number !== null) {
            $number = str_replace(",", "", $number);
        }
        $this->number = $number;
    }
    

    /**
     * Getter method for $number
     */
    public function getValue(): ?string {
        return $this->number;
    }

    /**
     * Function to add 2 Numbers
     * 
     * @param Number $otherNumber - Other instance of class Number to sum
     * @return Number $result - Result of sum 
     */
    public function add(Number $otherNumber): Number {
        $result = bcadd($this->number, $otherNumber->getValue(), 10); // 10 is Number Decimals accuracy
        return new Number($result);
    }

    /**
     * Function to subtract 2 Numbers
     * 
     * @param Number $otherNumber - Other instance of class Number to substract
     * @return Number $result - Result of subtract
     */
    public function sub(Number $otherNumber): Number {
        $result = bcsub($this->number, $otherNumber->getValue(), 10); // 10 is Number Decimals accuracy
        return new Number($result);
    }
    
    /*
     * Function to Multiply 2 Numbers
     * 
     * @param Number $otherNumber - Other instance of class Number to substract
     * @return Number $result - Result of Multiply
     */
    public function multiply(Number $otherNumber): Number {
        $result = bcmul($this->number, $otherNumber->getValue(), 10); // 10 is Number Decimals accuracy
        return new Number($result);
    }
    
    /*
     * Function to Divide 2 Numbers
     * 
     * @param Number $otherNumber - Other instance of class Number to substract
     * @return Number $result - Result of Division
     */
    public function divide(Number $otherNumber): Number {
        $result = bcdiv($this->number, $otherNumber->getValue(), 10); // 10 is Number Decimals accuracy
        return new Number($result);
    }

    /**
     * Function to compare 2 Numbers (Greater Than)
     * 
     * @param Number $otherNumber - Other instance of class Number to compare
     * @return bool - Response evaluation
     */
    public function greaterThan(Number $otherNumber): bool {
        if (bccomp($this->number, $otherNumber->getValue(), 10) === 1) return true;
        return false;
    }

    /**
     * Function to compare 2 Numbers (Lower Than)
     * 
     * @param Number $otherNumber - Other instance of class Number to compare
     * @return bool - Response evaluation
     */
    public function lowerThan(Number $otherNumber): bool {
        if (bccomp($this->number, $otherNumber->getValue(), 10) === -1) return true;
        return false;
    }

    /**
     * Function to compare 2 Numbers (Equal)
     * 
     * @param Number $otherNumber - Other instance of class Number to compare
     * @return bool - Response evaluation
     */
    public function equal(Number $otherNumber): bool {
        if (bccomp($this->number, $otherNumber->getValue(), 10) === 0) return true;
        return false;
    }

}

?>