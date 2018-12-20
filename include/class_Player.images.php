<?php
namespace raiz;
use  MongoDB;

error_reporting(E_ALL ^ E_NOTICE);

class PlayerImage {
    public $Image;

    function __construct( ){
        include "vendor/autoload.php";

        require_once("include/class_images.php");


        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->MongoDB = $this->Globais->MongoConf["Index"];
        $this->MongoTable = $this->Globais->MongoConf["Type"]["campeonato"];

        $this->Image = new Images($this->MongoTable);
    }

    function getPlayerImage (  $request, $response, $args, $jsonRAW ){
      /*
      IF (!$jsonRAW) {
          $data =  array(	"resultado" =>  "ERRO",
              "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

          return $response->withStatus(500)
              ->withHeader('Content-type', 'application/json;charset=utf-8')
              ->withJson($data);
      }
      */

        if ($jsonRAW["idimagem"]){
            $filtros["_id"]  =  $jsonRAW["idimagem"]   ;
        }
        if ($args["idusuario"]){
            $filtros["IDUSUARIO"]  =  $args["idusuario"]   ;
        }

        $result =  $this->Image->getImage($filtros );
//	$resultMongo =  iterator_to_array(  $resultMongo) ;

        $data["hits"] = $result;
        $data["resultado"] = "SUCESSO";

        return $response->withStatus(200)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
    }


    function setPlayerImage(  $request, $response, $args,   $jsonRAW){


            IF (!$jsonRAW  ) {
                $data =  array(	"resultado" =>  "ERRO",
                    "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

                return $response->withStatus(500)
                    ->withHeader('Content-type', 'application/json;charset=utf-8')
                    ->withJson($data);
            }
            $idMongo = $this->Image->SetImage( $jsonRAW , $args);

            $data["msg"] = "Inserted with Object ID '{$idMongo}'";

            if ( $idMongo ){
                $data["resultado"] = "SUCESSO" ;
                return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
            }
            else {
                $data =    array(	"resultado" =>  "ERRO",
                    "erro" => "Impossible to save that image - $mensagem_retorno");

                return $response->withStatus(200)
                    ->withHeader('Content-type', 'application/json;charset=utf-8')
                    ->withJson($data);
            }
        }


    function DeletePlayerImage(  $request, $response, $args,   $jsonRAW){
      IF (!$jsonRAW || !$jsonRAW["idimagem"] ) {
          $data =  array(	"resultado" =>  "ERRO",
          "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

          return $response->withStatus(500)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
      }

      $filtros["idimagem"] = $jsonRAW["idimagem"];

      $result  = $this->Image->DeleteImage   ($args["idusuario"],  $filtros  );
      if ($result)
        $data =   array(	"resultado" =>  "SUCESSO" );
      else
        $data =   array(	"resultado" =>  "ERRO" );

      return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
    }


}
