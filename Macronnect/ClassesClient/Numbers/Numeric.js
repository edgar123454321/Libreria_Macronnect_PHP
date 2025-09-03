
import { GeneralError } from "../GeneralError.js";

/**
 * Class Numeric 
 * 
 * Class to do Precise Operations with Numbers
 */
export class Numeric {

    /**
     * Class Numeric Constructor
     * @param {string} value 
     */
    constructor(value) {
        if (isNaN(Number(value))) throw new GeneralError("Error al Convertir Numero", "El valor de '" + value + "' no es un Numero");
        this.value = value;
    }

    /**
     * Function to Apply Design Format to Numbers.
     * Comma every 3 digits and max 2 decimals to show
     * Examples: 
     *      1234567.89 -> 1,234,567.89
     *      1000       -> 1,000.00
     *      1234.5     -> 1,234.50
     * 
     * @return {string} Value Formatted
    */
    format() {
        return Number(this.value).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Function to Apply Design Integer Format to Numbers.
     * 
     * @returns {string} Value Formatted
     */
    formatInteger() {
        return Number(this.value).toLocaleString('es-MX', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }


}