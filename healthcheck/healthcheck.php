<?php
namespace raiz;
include "vendor/autoload.php";

class HealthCheck{

    function __construct( ){

      require_once("include/globais.php");


        $this->Globais = new Globais();

    }
    function check(  $response, $jsonRAW)
    {

        $data = array(
            "resultado" => "SUCESSO",
            "status" => "UP",
            "BuildVersion" => $this->Globais->external_config["VERSION"],
            "BuildTime" => $this->Globais->external_config["BUILD_TIME"]
          ) ;
        $data = array("resultado" => "SUCESSO",
            "status" => "alive" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');

    }
}
