<?php
//incluyo el router de la libreria
include_once 'libs/Route.php';
include_once 'api/api-controller.php';
// include_once("libs/adodb5/adodb-exceptions.inc.php");
// include_once("libs/adodb5/adodb.inc.php");

//creo el nuevo router
$router = new Router();

// defino la base url para la construccion de links con urls semánticas
/* LOCAL anda asi */
 //define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']) . '/'); 
/** para cuando se sube al SERVER **/
//define('BASE_URL', '//' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['PHP_SELF']));

//defino la tabla de ruteo (case)
$router->addRoute('/', 'GET', 'ApiController', 'getHome');
$router->addRoute('inmueble/:ID', 'GET', 'ApiController', 'getDatosInmueble');
$router->addRoute('comercio/:ID', 'GET', 'ApiController', 'getDatosComercio');



$router->setDefaultRoute('ApiController','showError'); 

//rutear
$router->route($_REQUEST['resource'], $_SERVER['REQUEST_METHOD']);