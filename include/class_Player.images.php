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
        if ($jsonRAW["IDUSUARIOS"]){

          //TODO: NAO FUNCIONANDO O FILTRO POR N USUARIOS
            $filtros['$or'] =  $jsonRAW["IDUSUARIOS"][0]   ;
        }
        if ($jsonRAW["TipoImagem"]){
            $filtros["TipoImagem"]  =  $jsonRAW["TipoImagem"]   ;
        }
        $debug = $filtros;
      //  $debug = $jsonRAW;///var_export($jsonRAW,1);
        $result =  $this->Image->getImage($filtros );
//	$resultMongo =  iterator_to_array(  $resultMongo) ;

        $data["hits"] = $result;
        $data["resultado"] = "SUCESSO";
      //  $data["debug"] = $debug;//"SUCESSO";

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

            if ($jsonRAW["TipoImagem"] == "Profile"){
              //se for do tipo imagemdeperfil deleta a anterior, antes de incluir uma nova.

              $filtrosDelete["TipoImagem"] = $jsonRAW["TipoImagem"];
              $filtrosDelete["IDUSUARIO"] = $args["idusuario"];

            //  echo "\n\n\n ".$filtrosDelete;

              $this->DeletePlayerImage   ($args, $jsonRAW, $filtrosDelete);
              $data["debug"] = $filtrosDelete;
            }
            $jsonRAW["IDUSUARIO"] = $args["idusuario"];

            $idMongo = $this->Image->SetImage( $jsonRAW , $args, "Player");

            $data["msg"] = "Inserted with Object ID '{$idMongo}'";

            if ( $idMongo ){
                $data["resultado"] = "SUCESSO" ;
                $data["idimagem"] = "{$idMongo}";
                return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
            }
            else {
                $data =    array(	"resultado" =>  "ERRO",
                    "erro" => "Impossible to save that image - $mensagem_retorno");

                return $response->withStatus(201)
                    ->withHeader('Content-type', 'application/json;charset=utf-8')
                    ->withJson($data);
            }
        }


    function DeletePlayerImageAPI(  $request, $response, $args,   $jsonRAW){
      IF (!$jsonRAW || !$jsonRAW["idimagem"] ) {
          $data =  array(	"resultado" =>  "ERRO",
          "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

          return $response->withStatus(500)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
      }

      $filtros["idimagem"] = $jsonRAW["idimagem"];
      $filtros["TipoImagem"] = $jsonRAW["TipoImagem"];
      $filtros["IDUSUARIO"] = $args["idusuario"];

      if ($this->DeletePlayerImage   ($args,  $jsonRAW, $filtros  ))
        $data =   array(	"resultado" =>  "SUCESSO" );
      else
        $data =   array(	"resultado" =>  "ERRO" );

      return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
    }

    function DeletePlayerImage(  $args, $jsonRAW, $filtros ){
      IF (!$jsonRAW  ) {
          return false;
      }

    //  var_dump($filtros);
 

      $result  = $this->Image->DeleteImage   ($args["idusuario"],  $filtros  );

    //  echo "\n resultado do delete rows ".$result;
      if ($result)
        return true;
      else
        return false;
    }


}
