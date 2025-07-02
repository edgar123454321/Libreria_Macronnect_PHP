<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "C:/php/prueba.log");


include_once("./Macronnect/API/ApiMacronnect.php");

?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta name="application-name" content="Pruebas Consumir API Macronnect"/>
        <meta name="keywords" content="Pruebas Consumir API Macronnect" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1" />
        <meta http-equiv="Content-Type" content="text/html"/>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>API Macronnect</title>
        <link rel="shortcut icon" href="/Content/img/icons/Grupo-Amaro.png"/>
        <link rel="stylesheet" type="text/css" href="/Estilos/CSS/Bootstrap"/>
        <link rel="stylesheet" type="text/css" href="/Estilos/CSS/app-style"/> 
        <script type="text/javascript" src="/Estilos/js/jquery"></script>
        <script type="text/javascript" src="/Estilos/js/Bootstrap" ></script>
        <link rel="stylesheet" href="/Content/js/Datapicker/css/ui-lightness/jquery-ui-1.8.20.custom.css" type="text/css" media="screen" charset="utf-8"/>
        <script src="/Content/js/Datapicker/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>        
        <script src="/Content/js/Datapicker/js/jquery-ui-1.8.20.custom.min.js" type="text/javascript" charset="utf-8"></script>
        
        <!-- JSON Editor -->
        <link href="https://cdn.jsdelivr.net/npm/jsoneditor@9.10.0/dist/jsoneditor.min.css" rel="stylesheet" type="text/css">
        <script src="https://cdn.jsdelivr.net/npm/jsoneditor@9.10.0/dist/jsoneditor.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.14/ace.js"></script>


        <style>
            .textArea-diabled {
                resize: vertical;          
                overflow-x: hidden;
                background-color: #f5f5f5;  
                border: 1px solid #ccc;     
                color: #777;                       
            }
        </style>

        <!-- Javascript Classes -->
        <script type="module">
            import { GeneralError } from "/Macronnect/ClassesClient/GeneralError.js";
            import { JSONCommunication } from "/Macronnect/ClassesClient/Communication/JSONCommunication.js";
            window.GeneralError = GeneralError;
            window.JSONCommunication = JSONCommunication;
        </script>

        <script>
            
            function changeSelectEndpoint() {
                let select = document.getElementById("selectEndpoint");
                let array_endpoint = select.value.split("|");
                let endpoint = array_endpoint[0].trim();
                let method = array_endpoint[1].trim();

                let url = "/ConstruyeApiMacronnect.php";
                
                let formData = new FormData();
                formData.append("QueryEndpoint", "yes");
                formData.append("Endpoint", endpoint);
                formData.append("Method", method);
                let optionsFetch = JSONCommunication.prepareRequest(formData);
                fetch(url, optionsFetch)
                    .then(response => {
                        if (! response.ok) throw new GeneralError("Error Status Server", "Error en la Solicitud: " + response.status);
                        return response.text();
                    })
                    .then(response_ => {
                        console.log(response_);    
                        let response = JSONCommunication.validResponse(response_);
                        let dataResponse = response["DataResponse"];
                        
                        applyDesign_pathEndpoint(dataResponse);
                    })
                    .catch(error => {
                        if (error instanceof GeneralError) {
                            Swal.fire({
                                title: error.typeError,
                                html: error.messageError,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        } else {
                            Swal.fire({
                                title: "Unexpected Error",
                                text: error,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        }
                    });   
            }
            
            function comsumeApiMacronnect() {
                let select = document.getElementById("selectEndpoint");
                let array_endpoint = select.value.split("|");
                let endpointSelected = array_endpoint[0].trim();
                let method = array_endpoint[1].trim();


                // Get Parameters Required in GET Request
                let container_variablesRequired_URL = document.getElementById("container_detailParametersRequired_URL");
                let arrayHTML_ElementsRequired_URL = container_variablesRequired_URL.getElementsByClassName("inputs_parametersRequired_URL");
                let arrayElementsRequired_URL = [];
                if (arrayHTML_ElementsRequired_URL.length > 0) {
                    for(let i=0; i < arrayHTML_ElementsRequired_URL.length; i++) {
                        let elementDiv = arrayHTML_ElementsRequired_URL[i];
                        arrayInputs = elementDiv.getElementsByTagName("input");
                        let input_keyReplace = arrayInputs[0].value;
                        let input_valueReplace = arrayInputs[1].value;
                        if (input_valueReplace == "") {
                            Swal.fire({
                                title: "Error Parametro Requerido",
                                text: "Se requiere ingresar un valor para el Parametro '" + input_keyReplace + "'",
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                            return;
                        }
                        arrayElementsRequired_URL.push({
                            Key_Replace: input_keyReplace,
                            Value_Replace: input_valueReplace
                        });
                    }
                }

                // Get Parameters Query Endpoint
                let elementsKeyParameters = document.getElementsByClassName("keyParameter");
                let keyParameters = [];
                Array.from(elementsKeyParameters).forEach(element => {
                    let inputs = element.getElementsByTagName("input");
                    if (! inputs[0].checked) return;
                    let typeOperator = Array.from(element.getElementsByTagName("select"))[0].value.trim();

                    if (inputs[1].value.trim() == "") {
                        return;
                    }
                    keyParameters.push({ 
                        "Key": inputs[1].value,
                        "Operator": typeOperator,
                        "Value": inputs[2].value
                    });
                });

                // Valid if there is JSON to send in POST
                let container_arrayParametersSend = document.getElementById("container_arrayParametersSend_POST");
                let arrayParametersSend = container_arrayParametersSend.getElementsByClassName("slide-parameters-send");
                let jsonSend = "";
                let nameTemplate = "";
                for(let i=0; i < arrayParametersSend.length; i++) {
                    let containerParametersSend = arrayParametersSend[i];
                    if (containerParametersSend.style.display == "block") {
                        let editor = containerParametersSend.editorInstance;
                        nameTemplate = containerParametersSend.dataset.nameTemplate;
                        jsonSend = editor.getValue().trim();
                        try {
                            let jsonSend_POST = JSON.parse(jsonSend);
                        } catch (error) {
                            Swal.fire({
                                title: "Error JSON",
                                text: "El JSON a Enviar no esta bien Formado",
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                            return;
                        }
                        break;
                    }
                }


                let url = "/ConstruyeApiMacronnect.php";
                
                let formData = new FormData();
                formData.append("ConsumeApiMacronnect", "yes");
                formData.append("Endpoint", endpointSelected);
                formData.append("Method", method);
                formData.append("parameters_URL", JSON.stringify(keyParameters));
                if (jsonSend != "") {
                    formData.append("JSON_Send", jsonSend);
                    formData.append("Name_Template", nameTemplate);
                }
                if (arrayElementsRequired_URL.length > 0) {
                    formData.append("Parameters_Required_URL", JSON.stringify(arrayElementsRequired_URL));
                }
                let optionsFetch = JSONCommunication.prepareRequest(formData);
                fetch(url, optionsFetch)
                    .then(response => {
                        if (! response.ok) throw new GeneralError("Error Status Server", "Error en la Solicitud: " + response.status);
                        return response.text();
                    })
                    .then(response_ => {
                        console.log(response_);
                
                        let response = JSONCommunication.validResponse(response_);
                        let dataResponse = response["DataResponse"];
                        
                        applyDesign_responseConsumeApiMacronnect(dataResponse);
                    })
                    .catch(error => {
                        if (error instanceof GeneralError) {
                            Swal.fire({
                                title: error.typeError,
                                html: error.messageError,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        }
                        else {
                            Swal.fire({
                                title: "Unexpected Error",
                                text: error,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        }
                    });
            }

            function request_getImagesDocumentationEndpoint(endpointSelected, method, folderDocumentationName) {

                let url = "/ConstruyeApiMacronnect.php";

                let formData = new FormData();
                formData.append("GetImagesDocumentation", "yes");
                formData.append("Endpoint", endpointSelected);
                formData.append("Method", method);
                formData.append("Name_Folder_Documentation", folderDocumentationName);
                let optionsFetch = JSONCommunication.prepareRequest(formData);
                fetch(url, optionsFetch)
                    .then(response => {
                        if (! response.ok) throw new GeneralError("Error Status Server", "Error en la Solicitud: " + response.status);
                        return response.text();
                    })
                    .then(response_ => {
                        console.log(response_);
                        let response = JSONCommunication.validResponse(response_);
                        let dataResponse = response["DataResponse"];

                        showDesign_imagesDocumentation(dataResponse);

                    })
                    .catch(error => {
                        if (error instanceof GeneralError) {
                            Swal.fire({
                                title: error.typeError,
                                html: error.messageError,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        } else {
                            Swal.fire({
                                title: "Unexpected Error",
                                text: error,
                                icon: 'error',
                                showConfirmButton: true,
                                confirmButtonText: "Aceptar"
                            });
                        }
                    });

                

            }
            
        </script>
        
        
        
        <script>
            
            function applyDesign_pathEndpoint(dataResponse) {
                
                // Method Endpoint
                let selectMethodEndpoint = document.getElementById("typeRequestHTTP");
                selectMethodEndpoint.innerHTML = "";
                let option = document.createElement("option");
                option.value = dataResponse["Method"];
                option.text = dataResponse["Method"];
                selectMethodEndpoint.appendChild(option);

                // Path Endpoint
                document.getElementById("valueEndpoint").value = dataResponse["Endpoint"];

                // Description Endpoint
                let container = document.getElementById("container_descriptionEndpoint");
                container.value = dataResponse["Description"];

                // Notas Endpoint
                let html = "";
                container = document.getElementById("container_notasEndpoint");
                dataResponse["Notes"].forEach((note) => {
                    html += "●  " + note + "\n\n";
                });
                container.value = html;

                // Show Documentation
                container = document.getElementById("container_documentationFolders");
                html = "";
                if (dataResponse["Documentation"]["Folders Documentation"].length > 0) {
                    let foldersDocumentation = dataResponse["Documentation"]["Folders Documentation"];
                    for(let i=0; i < foldersDocumentation.length; i++) {
                        if (i % 3 === 0) {
                            html += "<div class='row'>";
                        }
                        html += "<div class='col-lg-4 col-sm-4'>";
                        html += "   <div class='card'>";
                        html += "       <div class='card-header text-center' data-name-folder-documentation='" + foldersDocumentation[i] + "' onclick='showModalDocumentation(this)' style='padding: 15px; cursor: pointer;'>";
                        html += "           " + foldersDocumentation[i];
                        html += "       </div>";
                        html += "   </div>";    
                        html += "</div>";
                        if ((i + 1) % 3 === 0 || i === foldersDocumentation.length - 1) {
                            html += "</div>";
                        }
                    }
                    document.getElementById("foldersDocumentation").innerHTML = html;
                    container.style.display = "block";
                }
                else {
                    container.style.display = "none";
                }

                // Show JSON send in POST to modify
                let container_arrayParametersSend = document.getElementById("container_arrayParametersSend_POST");
                if ("Parameters POST" in dataResponse) {
                    let html = "";
                    
                    Object.entries(dataResponse["Parameters POST"]).forEach(([clave, valor], index) => { 
                        html += "<div class='slide-parameters-send' data-name-template='" + clave + "' style='height: 400px; width: 100%; font-size: 16px; display: none;'></div>";
                    });
                    container_arrayParametersSend.innerHTML = html;
                    
                    let arrayParametersSend = container_arrayParametersSend.getElementsByClassName("slide-parameters-send");
                    Array.from(arrayParametersSend).forEach(div_parameterSend => {
                        const editor = ace.edit(div_parameterSend);
                        editor.session.setMode("ace/mode/json");
                        div_parameterSend.editorInstance = editor;
                    });
                    Object.entries(dataResponse["Parameters POST"]).forEach(([clave, valor], index) => { 
                        let editor = arrayParametersSend[index].editorInstance;
                        editor.setValue(JSON.stringify(valor, null, 4), -1);
                    });

                    showSlide_parametersSend(0);

                    document.getElementById("container_jsoneditor_parametersSend_POST").style.display = "block";
                }
                else {
                    container_arrayParametersSend.innerHTML = "";
                    document.getElementById("container_jsoneditor_parametersSend_POST").style.display = "none";
                }

                // Input Endpoint
                document.getElementById("pathEndpoint").value = dataResponse["Real Endpoint"];

                // Required Parameters URL ??
                let variablesRequired_URL = dataResponse["Endpoint"].match(/__[^\/]+?__/g);
                let container_variablesRequired_URL = document.getElementById("container_detailParametersRequired_URL");
                if (variablesRequired_URL != null) {
                    let html = "";
                    variablesRequired_URL.forEach(variable => {
                        html += "<div class='row' style='margin-bottom: 20px;'>";
                        html += "   <div class='inputs_parametersRequired_URL'>";
                        html += "       <div class='col-lg-12' style='padding: 0px;'>";
                        html += "           <div class='col-lg-3'>";
                        html += "               <input class='form-control' value='" + variable + "' type='text' disabled style='text-align: center;'>";
                        html += "           </div>";
                        html += "           <div class='col-lg-9'>";
                        html += "               <input class='form-control' value='' type='text' style='text-align: center;'>";
                        html += "           </div>";
                        html += "       </div>";
                        html += "   </div>";
                        html += "</div>";
                    });

                    container_variablesRequired_URL.innerHTML = html;
                    document.getElementById("container_parametersRequired_URL").style.display = "block";
                }
                else {
                    container_variablesRequired_URL.innerHTML = "";
                    document.getElementById("container_parametersRequired_URL").style.display = "none";
                }


                // Response Endpoint
                container = document.getElementById("responseQueryEndpoint");
                container.innerHTML = "";

            }
            
            function applyDesign_addParameterQuery() {
                let container = document.getElementById("containerParametersQuery");
                let html = "";
                
                let hash = Math.random().toString(36).substring(2,10);                
                let newDiv = document.createElement("div");
                newDiv.classList.add("row");
                newDiv.style.marginTop = "15px";                
                html += '   <div id="id_' + hash + '" class="col-lg-12 keyParameter" style="display: flex;">';
                html += '       <div class="col-lg-1">';
                html += '           <span class="input-group-addon">';
                html += '               <input type="checkbox" checked/>';
                html += '           </span>';
                html += '       </div>';
                html += '       <div class="col-lg-3">';
                html += '           <input class="form-control" value="" type="text" style="text-align: center;"/>';
                html += '       </div>';
                
                html += '       <div class="col-lg-1" style="padding: 0px;">';
                html += '           <select class="form-control" style="text-align: center;">';
                <?php 
                    foreach(ConstantsMacronnect::$TYPE_FILTERS as $key => $value) {
                        echo "html += '<option value=\"$key\">$key</option>';";
                    }
                ?>
                html += '           </select>';
                html += '       </div>';
                
                html += '       <div class="col-lg-6">';
                html += '           <input class="form-control" value="" type="text" style="text-align: center;" />';
                html += '       </div>';
                html += '       <div class="col-lg-1" style="align-content: center;">';
                html += '           <img src="/Content/img/icons/eliminar2.png" onclick="delateRowKeyQuery(\'id_' + hash + '\')" width="32" style="cursor: pointer;"/>';
                html += '       </div>';
                html += '   </div>';        
                newDiv.innerHTML = html;
                container.append(newDiv);
            }
            
            function delateRowKeyQuery(idHash) {
                document.getElementById(idHash).parentElement.remove();
            }
            
            function applyDesign_responseConsumeApiMacronnect(response) {
                let container = document.getElementById("responseQueryEndpoint");
                let html = "";
                html += "<pre>";
                html += "   " + JSON.stringify(response, null, 4);
                html += "</pre>";
                container.innerHTML = html;
            }


            function showModalDocumentation(tag_div) {

                let folderDocumentationName = tag_div.getAttribute("data-name-folder-documentation");
                let select = document.getElementById("selectEndpoint");
                let array_endpoint = select.value.split("|");
                let endpoint = array_endpoint[0].trim();
                let method = array_endpoint[1].trim();

                // Update Title Modal
                document.getElementById("modalDocumentationImages_title").innerText = "Documentacion '" + folderDocumentationName + "'";

                // Update Body to upload Gif
                let container = document.getElementById("modalDocumentationImages").getElementsByClassName("modal-body")[0];
                let html = "";
                html += "<div class='claseImagenCarga text-center' id='imagenCarga' style='display: block;'>";
                html += "   <img class='' src='/Content/img/icons/waiting.gif' height='380px'>";
                html += "</div>";
                container.innerHTML = html;

                request_getImagesDocumentationEndpoint(endpoint, method, folderDocumentationName);

                document.getElementById("button_modalDocumentationImages").click();
            }

            function showDesign_imagesDocumentation(dataResponse) {
                let arrayImages = dataResponse["Path Images"];

                let containerBody = document.getElementById("modalDocumentationImages").getElementsByClassName("modal-body")[0];
                let html = "";

                html += '<div id="slider" style="overflow: hidden; position: relative; height: 90%; text-align: center;">';
                arrayImages.forEach(function(image, index) {
                    let valueDisplay = "none";
                    if (index == 0) {
                        valueDisplay = "block";
                    }
                    html += '<img class="slide" src="' + image + '" style="height: 100%; display: ' + valueDisplay + '; text-align: center; margin: 0 auto;">';
                });
                html += '</div>';

                html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">';
                html += '   <img src="Content/img/icons/hacia-atras.png" onclick="prevSlide()" style="width: 30px; cursor: pointer;">';
                html += '   <div id="slider-title" style="text-align: center; margin-top: 10px; font-weight: bold;">';
                if (arrayImages.length > 0) {
                    html += '   ' + getFileName(arrayImages[0]);
                }
                html += '   </div>';
                html += '   <img src="Content/img/icons/hacia-adelante.png" onclick="nextSlide()" style="width: 30px; cursor: pointer;">';
                html += '</div>';

                containerBody.innerHTML = html;

                // Update Data
                currentSlide = 0;
                slides = document.querySelectorAll(".slide");



            }
            
        </script>


 
        
        
        
    </head>
    
    <body>
        <div class="container">
            
            <div class="row text-center" style="margin-bottom: 20px;">
                <h3>Consumir API Macronnect</h3>
            </div>
            
            <div class="row" style="margin-bottom: 30px;">
                <h4>Selecciona el Endpoint</h4>
                
                <select id="selectEndpoint" class="form-control" onchange="changeSelectEndpoint()" style="text-align: left;">
                    <?php 
                        foreach(ApiMacronnect::$endpoints as $value) {
                            echo "<option value='" . $value->getEndpoint() . "|"  . $value->getMethod() . "'>[" . $value->getMethod() . "] " . $value->getTitle() . "</option>";
                        }
                    ?>
                </select>
            </div>

            <div style="margin-bottom: 35px;">
                <div class="row" style="margin-bottom: 5px;">
                    <h4>Ruta del Endpoint</h4>
                </div>
                <div class="row">
                    <input id="valueEndpoint" class="form-control" disabled="" value="" type="text" style="text-align: center;">
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <div class="row" style="margin-bottom: 5px;">
                    <h4>Descripcion del Endpoint</h4>
                </div>
                <div class="row">
                    <div>
                        <textarea class="textArea-diabled" id="container_descriptionEndpoint" rows="4" cols="40" readonly style='width: 100%;'>

                        </textarea>
                    </div>
                </div>
            </div>


            <div style="margin-bottom: 50px;">
                <div class="row" style="margin-bottom: 5px;">
                    <h4>Notas del Endpoint</h4>
                </div>
                <div class="row">
                    <div>
                        <textarea class="textArea-diabled" id="container_notasEndpoint" rows="4" cols="40" readonly style='width: 100%;'>

                        </textarea>
                    </div>
                </div>
            </div>


            <div id="container_documentationFolders" style="margin-bottom: 50px;">
                <div class="row" style="margin-bottom: 15px;">
                    <h4>Documentacion</h4>
                </div>
                <div id="foldersDocumentation">

                </div>
            </div>
            


            <div style="margin-bottom: 50px;">
                <div class="row" style="margin-bottom: 20px;">
                    <h4>Ruta Real del Endpoint</h4>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-lg-1" style="padding: 0px;">
                            <select id="typeRequestHTTP" class="form-control" style="text-align: center;">
                                <option value="GET">GET</option>
                            </select>
                        </div>
                        <div class="col-lg-10">
                            <input id="pathEndpoint" disabled class="form-control" value="" type="text"/>
                        </div>
                        <div class="col-lg-1">
                            <button class="btn btn-primary" onclick="comsumeApiMacronnect()">Ejecutar</button>
                        </div>
                    </div>
                </div>
            </div>


            <div id="container_parametersRequired_URL" style="display: none;">
                <div style="margin-bottom: 50px;">
                    <div class="row" style="margin-bottom: 20px;">
                        <h4>Parametros Requeridos URL</h4>
                    </div>
                    <div id="container_detailParametersRequired_URL">
                    </div>
                </div>
            </div>
            

            <div style="margin-bottom: 50px;">
                <div class="row" style="margin-bottom: 20px;">
                    <h4>Parametros de Consulta URL</h4>
                </div>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-lg-1">
                            
                        </div>
                        <div class="col-lg-3">
                            <input class="form-control" value="Key" type="text" disabled style="text-align: center;"/>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-6">
                            <input class="form-control" value="Value" type="text" disabled style="text-align: center;" />
                        </div>
                    </div>
                </div>
                
                <div id="containerParametersQuery">
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-lg-12 keyParameter">
                            <div class="col-lg-1">
                                <span class="input-group-addon">
                                    <input type="checkbox" checked/>
                                </span>
                            </div>
                            <div class="col-lg-3">
                                <!-- <input class="form-control" value="clave" type="text" style="text-align: center;"/> -->
                                <input class="form-control" value="" type="text" style="text-align: center;"/>
                            </div>
                            <div class="col-lg-1" style="padding: 0px;">
                                <select class="form-control" style="text-align: center;">
                                    <?php 
                                        foreach(ConstantsMacronnect::$TYPE_FILTERS as $key => $value) {
                                            echo "<option value='$key'>$key</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <!-- <input class="form-control" value="0 241 225 593" type="text" style="text-align: center;" /> -->
                                <input class="form-control" value="" type="text" style="text-align: center;" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-top: 45px;">
                    <div class="col-lg-11 text-center">
                        <img src="/Content/img/icons/add_icon.png" onclick="applyDesign_addParameterQuery()" width="50" style="cursor: pointer;">
                    </div>
                </div>
                
            </div>


            <div id="container_jsoneditor_parametersSend_POST" style="margin-bottom: 50px;">
                <div class="row" style="margin-bottom: 15px;">
                    <h4>Parametros a Enviar en POST</h4>
                </div>
                <div style="overflow: hidden; position: relative; text-align: center;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom:20px;">  
                        <img src="Content/img/icons/hacia-atras.png" onclick="prevSlide_parametersSend()" style="width: 30px; cursor: pointer;">   
                        <div id="slider-title-parametersSend" style="font-size: 17px; text-align: center; margin-top: 10px; font-weight: bold;">   
                                "Nombre Plantilla"   
                        </div>   
                        <img src="Content/img/icons/hacia-adelante.png" onclick="nextSlide_parametersSend()" style="width: 30px; cursor: pointer;">
                    </div>
                </div>
                <div id="container_arrayParametersSend_POST" class="row">

                </div>
            </div>


            
            <div>
                <div class="row" style="margin-bottom: 20px;">
                    <h4>Resultado</h4>
                </div>
                
                <div class="row">
                    <div id="responseQueryEndpoint">
                    </div>
                </div>
            </div>

            <div class="row" style="padding: 60px;">
            </div>


            <!-- Modal Images Documentation -->
             <button id="button_modalDocumentationImages" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalDocumentationImages" style="display: none;">
                Launch modalDocumentationImages
            </button>
            <div class="modal fade" id="modalDocumentationImages" tabindex="-1" aria-labelledby="modalDocumentationImagesLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                <div class="row" style="display: flex;">
                                    <div class="col-lg-1 col-sm-1 col-xs-1">
                                    </div>
                                    <div class="col-lg-10 col-sm-10 col-xs-10 text-center">
                                        <h4 id="modalDocumentationImages_title" class="modal-title">Name Folder Documentation</h4>
                                    </div>
                                    <div class="col-lg-1 col-sm-1 col-xs-1">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="height: 100%;">
                                            <span aria-hidden="true" style="font-size: 30px;">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-body" style='height: 500px;'>
                            ...
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
        
        
        <script type="text/javascript" src='/Content/Otros/js/sweetalert2_version7.all.min.js'></script>
        <link rel="stylesheet" href="/Content/Otros/Estilos/sweetalert2_version7.min.css"/>    

        <style>
            .swal2-success, .swal2-error, .swal2-warning, .swal2-question{
                font-size: 2rem !important;
            }
            .swal2-title {
                font-size: 3rem;
            }
            .swal2-html-container {
                font-size: 2em !important;
            }
            .swal2-confirm {
                font-size: 1.5em !important;
            }
        </style>

        <script>
            
        </script>
        
    </body>
    
    <script>
        window.onload = function() {
            changeSelectEndpoint();
        }
    </script>

    <script>
        var currentSlide = 0;
        var slides = document.querySelectorAll(".slide");

        function getFileName(path) {
            return path.split('/').pop(); // extrae el nombre del archivo desde el path
        }

        function showSlide(index) {
            slides.forEach((img, i) => {
                img.style.display = i === index ? "block" : "none";
            });

            // Mostrar el nombre del archivo como título
            document.getElementById("slider-title").innerText = getFileName(slides[index].src);
        }

        function nextSlide() {
            slides = document.querySelectorAll(".slide");
            if (slides.length == 0 ) return;
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            slides = document.querySelectorAll(".slide");
            if (slides.length == 0 ) return;
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }
    </script>


    <script>
        var currentSlide_parametersSend= 0;
        function showSlide_parametersSend(index) {
            let slides = document.querySelectorAll(".slide-parameters-send");
            slides.forEach((div, i) => {
                if (i == index) {
                    div.style.display = "block";
                    let nameTemplate = "\"" + div.dataset.nameTemplate + "\"";
                    document.getElementById("slider-title-parametersSend").innerText = nameTemplate;
                }
                else {
                    div.style.display = "none";
                }
            });
        }

        function nextSlide_parametersSend() {
            let slides = document.querySelectorAll(".slide-parameters-send");
            if (slides.length == 0) return;
            currentSlide_parametersSend = (currentSlide_parametersSend + 1) % slides.length;
            showSlide_parametersSend(currentSlide_parametersSend);
        }

        function prevSlide_parametersSend() {
            let slides = document.querySelectorAll(".slide-parameters-send");
            if (slides.length == 0) return;
            currentSlide_parametersSend = (currentSlide_parametersSend - 1 + slides.length) % slides.length;
            showSlide_parametersSend(currentSlide_parametersSend);
        }

    </script>
    
</html>
