/**
 * General Error Class that save typeError and messageError  
 */
export class GeneralError extends Error {

    /**
     * GeneralError Constructor
     *
     * @param {string} typeError 
     * @param {string} messageError 
     * @param {any} [dataResponse={}] - Optional data response, defaults to an empty object
     */
    constructor(typeError, messageError, dataResponse = {}) {
        super(messageError);
        this.name = "GeneralError";
        this.typeError = typeError;
        this.messageError = messageError;
        this.dataResponse = dataResponse;
    }

    toString() {
        return "Type Error: " + this.typeError + " -> Message Error: " + this.messageError;
    }

}