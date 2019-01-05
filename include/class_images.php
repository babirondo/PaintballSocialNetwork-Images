<?php
namespace raiz;
use  MongoDB;

error_reporting(E_ALL ^ E_NOTICE);

class Images{

    function __construct( $MongoTable ){
        include "vendor/autoload.php";

        require_once("include/globais.php");
        require_once("include/SimpleImage.php");
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

        $this->folder  = __DIR__."/../generated_images/";
    }

    function base64_to_jpeg($base64_string, $output_file) {

      //echo "\n\n\n\n\n PASTA ".$pasta_salvar_imagens.$output_file;
        // open the output file for writing
        $ifp = fopen( $this->folder.$output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }


        function resize_image($imagem, $tipo, $idreferencia="", $tiporeferencia=""){
          switch($tipo){
            case("Profile"):
              $altura = 320 ;
              $largura = 320;
            break;
          }
          $nome_arquivo_foto = "$tiporeferencia-$idreferencia-original-".uniqid().".jpg";
          $this->base64_to_jpeg($imagem,$nome_arquivo_foto );
          $nome_arquivo_foto_resized = "$tiporeferencia-$idreferencia-$tipo-".uniqid().".jpg";

          $image = new SimpleImage();
          if (! $image->load( $this->folder.$nome_arquivo_foto ) ){
            echo "Nao foi possivel ler os atributos da imagem";

            return false;
          }
          else{

            if ($image->getHeight > $image->getWidth){
              $image->resizeToHeight( $altura);
            }
            else {
              $image->resizeToWidth( $largura);
            }
            $image->save($this->folder.$nome_arquivo_foto_resized);

            return $nome_arquivo_foto_resized;
          }
        }

        function setImage(  $jsonRAW , $args , $tipoImagem){

            if (!$this->con->conectado || !$jsonRAW["imagem"]){
              return false;
            }

            IF (!$jsonRAW  ) {
              return false;
            }

            //echo "<BR> tipoo $tipoImagem"; var_dump($jsonRAW);

            switch ($tipoImagem){
              case("Team"):
                  $idreferencia =$jsonRAW["IDTIME"];// = $args["idreferencia"];
              break;

              case("Player"):
                  $idreferencia = $jsonRAW["IDUSUARIO"] ;//= $args["idreferencia"];
              break;
            }


        //    $jsonRAW["IDUSUARIO"] = $args["idusuario"];
            $imagem_redimensionada = $this->resize_image($jsonRAW["imagem"], $jsonRAW["TipoImagem"], $idreferencia, $tipoImagem);

            if ($imagem_redimensionada != false){
              //echo "IMAGEM $imagem_redimensionada";
              $jsonRAW["imagem"] =   $imagem_redimensionada;


              //var_dump($args);

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
            else
              return false;
        }

    function getImage (  $args ){

        if (!$this->con->conectado){
            return false;
        }
        $filtros =  array();
/*
        if ($args["_id"]){
            $filtros["_id"]  =   $this->con->MongoToObject ( $args["_id"] )  ;
        }
        if ($args["IDUSUARIO"]){
            $filtros["IDUSUARIO"]  =    $args["IDUSUARIO"]   ;
        }
        if ($args["IDTIME"]){
            $filtros["IDTIME"]  =    $args["IDTIME"]   ;
        }
        if ($args["IDTIMES"]){
            $filtros["IDTIMES"]  =    $args["IDTIMES"]   ;
        }
        */
        //var_dump($args);
        $resultMongo =  $this->con->MongoFind($args  );//$filtros
  //	$resultMongo =  iterator_to_array(  $resultMongo) ;

        return $resultMongo;
    }

    function DeleteImage( $idreferencia,  $args){

        if (!$this->con->conectado  && !$idreferencia){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }


        if ($args["IDUSUARIO"]){
            $filtros["IDUSUARIO"]  =  $idreferencia;
        }
        if ($args["IDTIME"]){
            $filtros["IDTIME"]  =  $idreferencia;
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
