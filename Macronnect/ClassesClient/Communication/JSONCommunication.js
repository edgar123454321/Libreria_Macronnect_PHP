
import { GeneralError } from "./../GeneralError.js"

/**
 * Class JSONCommunication used in Client 
 */
export class JSONCommunication {

    /**
     * Function that validates that Server Response is in Format JSON
     * 
     * @param {string} _response - Response sent by Server in string
     * @returns {Object} - Response sent by Server in JSON Object 
     */
    static validResponse(_response) {
        let response = null;
        try {
            response = JSON.parse(_response);
        } catch (e) {
            throw new Error("Error al Convertir la Siguiente Respuesta a JSON: " + _response);
        }
        if (response["Status"] == "Error") {
            if (response.hasOwnProperty('DataResponse')) {
                throw new GeneralError(response["TypeError"], response["MessageError"], response["DataResponse"]);
            }
            throw new Error(response["MessageError"]);
        }
        return response;
    }

    /**
     * Function that Prepares options for the fetch request
     * 
     * @param {FormData} formData - The form data to be sent with the request
     * @returns {Object} The options object for the fetch request
     */
    static prepareRequest(formData) {
        let options = {
            method: "POST",
            body: formData
        };
        return options;
    }

    static prepareRequestWithFile(formData) {
        let options = {
            method: "POST",
            body: formData
        };
        return options;
    }


}