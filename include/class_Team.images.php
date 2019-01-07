<?php
namespace raiz;
use  MongoDB;

error_reporting(E_ALL ^ E_NOTICE);

class TeamImage {
    public $Image;

    function __construct( ){
        include "vendor/autoload.php";

        require_once("include/class_images.php");


        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->MongoDB = $this->Globais->MongoConf["Index"];
        $this->MongoTable = $this->Globais->MongoConf["Type"]["campeonato"];

        $this->Image = new Images($this->MongoTable);

        $this->API = new \babirondo\REST\RESTCall();

    }


    function getTeamImage (  $request, $response, $args, $jsonRAW ){
      /*
      IF (!$jsonRAW) {
          $data =  array(	"resultado" =>  "ERRO",
              "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

          return $response->withStatus(500)
              ->withHeader('Content-type', 'application/json;charset=utf-8')
              ->withJson($data);
      }
      */
      /*
$jsonRAW["IDTIMES"]=null;
$jsonRAW["TipoImagem"]=null;
$args["idtime"]="10";
*/

        if ($jsonRAW["idimagem"]){
            $filtros["_id"]  =  $jsonRAW["idimagem"]   ;
        }
        if ($args["idtime"]){
            $filtros["IDTIME"]  =  "".$args["idtime"].""    ;
        }
        if ($jsonRAW["IDTIMES"]){
            $consultar=null;
            foreach (explode(",",$jsonRAW["IDTIMES"]   ) as $t){
              $consultar = array("IDTIME" => "$t");
            }
            $filtros['$or'] =  $consultar;//explode(",",$jsonRAW["IDTIMES"]   );
        }
        if ($jsonRAW["IDTIMES_ARRAY"]){
            $consultar=null;
            $debug[] = $jsonRAW["IDTIMES_ARRAY"];
            foreach ( $jsonRAW["IDTIMES_ARRAY"]  as $t){
              $consultar = array("IDTIME" => "$t");
              $debug[] = $filtros['$or'][] =  $consultar;//explode(",",$jsonRAW["IDTIMES"]   );

            }

        }
        if ($jsonRAW["TipoImagem"]){
            $filtros["TipoImagem"]  =  $jsonRAW["TipoImagem"]   ;
        }
        if (count($filtros ) > 1){
          $filtros_bkp = $filtros;
          $filtros = null;
          $filtros['$and'] = $filtros_bkp;
        }
        $debug[] = $filtros;
      //  VAR_DUMP($filtros);
        $result =  $this->Image->getImage($filtros );
    //    VAR_DUMP($result);
	      //$resultMongo =  iterator_to_array(  $resultMongo) ;

        $data["hits"] = $result;
        $data["resultado"] = "SUCESSO";
        $data["debug"] = $debug;//"SUCESSO";

        return $response->withStatus(200)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
    }


    function setTeamImage(  $request, $response, $args,   $jsonRAW){


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
              $filtrosDelete["IDTIME"] = $args["idtime"];

            //  echo "\n\n\n ".$filtrosDelete;

              $this->DeleteTeamImage   ($args, $jsonRAW, $filtrosDelete);
              $data["debug"] = $filtrosDelete;
            }
            $jsonRAW["IDTIME"] = $args["idtime"];

            $ResultMongo = $this->Image->SetImage( $jsonRAW , $args, "Team");

            $idMongo = $ResultMongo["idImage"];

          //  $data["msg"] = "Inserted with Object ID '$idMongo'";

            if ( $idMongo ){
                $data["resultado"] = "SUCESSO" ;
                $data=  $ResultMongo;

                if ($jsonRAW["TipoImagem"] == "Profile"){
                  //callback para informar imagem processada
                  $trans=null;$trans = array(":idtime" => $jsonRAW["IDTIME"]  );
                  $salvar_imagem_payload=null;
                  $salvar_imagem_payload["idtime"] = $jsonRAW["IDTIME"];
                  $salvar_imagem_payload["statusProfileImage"] = $ResultMongo["Image"];
                  $query_API = $this->API->CallAPI("POST", strtr( $this->Globais->CallbackTeamImageProcess, $trans), json_encode($salvar_imagem_payload,1) );
                }

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


    function DeleteTeamImageAPI(  $request, $response, $args,   $jsonRAW){
      IF (!$jsonRAW || !$jsonRAW["idimagem"] ) {
          $data =  array(	"resultado" =>  "ERRO",
          "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

          return $response->withStatus(500)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
      }

      $filtros["idimagem"] = $jsonRAW["idimagem"];
      $filtros["TipoImagem"] = $jsonRAW["TipoImagem"];
      $filtros["IDTIME"] = $args["idtime"];

      if ($this->DeleteTeamImage   ($args,  $jsonRAW, $filtros  ))
        $data =   array(	"resultado" =>  "SUCESSO" );
      else
        $data =   array(	"resultado" =>  "ERRO" );

      return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
    }

    function DeleteTeamImage(  $args, $jsonRAW, $filtros ){
      IF (!$jsonRAW  ) {
          return false;
      }

    //  var_dump($filtros);

      $result  = $this->Image->DeleteImage   ($args["idtime"],  $filtros  );

    //  echo "\n resultado do delete rows ".$result;
      if ($result)
        return true;
      else
        return false;
    }


}
