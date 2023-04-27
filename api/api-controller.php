<?php

require_once('api-view.php');

require_once('api-model-inmueble.php');
require_once('api-model-comercio.php');

class ApiController{

    private $modelInmueble;
    private $modelComercio;
    private $modelPersona;
    private $view;

    function __construct()
    {
        $this->modelInmueble = new ApiModelInmueble();
        $this->modelComercio = new ApiModelComercio();
       
        $this->view = new APIView();
    }

    function getHome(){
        $this->view->showHome();
    }

    function getDatosInmueble($params){
        $inmueble = $params[':ID'];
        $datos = $this->modelInmueble->getDatosInmueble($inmueble);
     
        if ($datos) {
            $this->view->response($datos, 200);
        } else {
            $this->view->response("No se han encontrado resultados para tu búsqueda", 404);
        }
    }

    function getDatosComercio($params){
        $comercio = $params[':ID'];     
      
        if(is_numeric($comercio) && (strlen($comercio) == 11)){
            $cuil = (substr($comercio,0,2) . '-' . substr($comercio,2,8) . '-' . substr($comercio,10,11));
        }else if (strlen($comercio) == 13){
            $cuil = $comercio;
        }
      

        $datos = $this->modelComercio->getDatosComercio($cuil);

        if ($datos) {
            $this->view->response($datos, 200);
        } else {
            $this->view->response("No se han encontrado resultados para tu búsqueda", 404);
        }
    }

   

    //Funcion para convertir de string(la variable de entrada) a json
    function getData(){ 
        return json_decode($this->data); 
    }  

    function showError(){
        $this->view->response("No se han encontrado resultados", 404);
    }
    
}



?>