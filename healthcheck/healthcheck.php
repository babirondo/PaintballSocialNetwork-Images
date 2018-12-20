<?php
namespace raiz;

class HealthCheck{
    function __construct( ){

    }
    function check(  $response, $jsonRAW)
    {

        $data = array("resultado" => "SUCESSO",
            "status" => "alive" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');

    }
}
