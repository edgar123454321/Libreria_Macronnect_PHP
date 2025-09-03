
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

    getTraceback() {
        console.log(this.dataResponse);
        let html = "";
        html += "<div class='row' style='margin: 0px; text-align: center; justify-content: center;'>";
        html += "   " + this.messageError;
        html += "</div>";
        html += "<div class='row' style='margin: 0px; padding-top: 30px;'>";
        html += "   <strong style='width: auto;'>File:</strong> " + this.dataResponse["Detail Error"]["file"];
        html += "</div>";
        html += "<div class='row' style='margin: 0px; padding-top: 5px;'>";
        html += "   <strong style='width: auto;'>Line:</strong> " + this.dataResponse["Detail Error"]["line"];
        html += "</div>";
        html += "<div class='row' style='margin: 0px; padding-top: 5px;'>";
        html += "   <strong style='width: auto;'>Traceback:</strong> " + this.dataResponse["Detail Error"]["trace"];
        html += "</div>";
        return html;
    }

}
