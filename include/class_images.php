<?php
namespace raiz;
use  MongoDB;

error_reporting(E_ALL ^ E_NOTICE);

class Images{

    function __construct( $MongoTable ){
        include "vendor/autoload.php";

        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->con = new \babirondo\classbd\db();

        $this->con->conecta($this->Globais->Mongo_banco ,
                              $this->Globais->Mongo_localhost,
                              $this->Globais->Mongo_db,
                              $this->Globais->Mongo_username,
                              $this->Globais->Mongo_password,
                              $this->Globais->Mongo_port);

        $this->con->MongoDB = $this->Globais->MongoConf["Index"];
        $this->con->MongoTable = $MongoTable;//$this->Globais->Championship["Type"]["campeonato"];
    }


        function setImage(  $jsonRAW , $args ){

            if (!$this->con->conectado){
              return false;

            }

            IF (!$jsonRAW  ) {
              return false;
            }
            $jsonRAW["IDUSUARIO"] = $args["idusuario"];
            $resultMongo = $this->con->MongoInsertOne( $jsonRAW );
            $idMongo = $resultMongo->getInsertedId();

            $data["msg"] = "Inserted with Object ID '{$idMongo}'";

            if ( $idMongo ){
                return $idMongo;
            }
            else {
                // nao encontrado
                return false;
            }

        }

    function getImage (  $args ){

        if (!$this->con->conectado){
            return false;
        }
        $filtros =  array();

        if ($args["_id"]){
            $filtros["_id"]  =   $this->con->MongoToObject ( $args["_id"] )  ;
        }
        if ($args["IDUSUARIO"]){
            $filtros["IDUSUARIO"]  =    $args["IDUSUARIO"]   ;
        }
        $resultMongo =  $this->con->MongoFind($filtros );
  //	$resultMongo =  iterator_to_array(  $resultMongo) ;

        return $resultMongo;
    }

    function DeleteImage( $idusuario,  $args){

        if (!$this->con->conectado  && !$idusuario){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }


        if ($idusuario){
            $filtros["IDUSUARIO"]  =  $idusuario;
        }
        if ($args["idimagem"]){
            $filtros["_id"]   =     $this->con->MongoToObject ( $args["idimagem"] )  ;
        }

        $resultMongo = $this->con->MongoDeleteOne  ( $filtros );

        if ($resultMongo )
          return true;
        else
          return false;
    }
}
