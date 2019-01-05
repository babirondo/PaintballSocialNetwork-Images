<?php
namespace raiz;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

error_reporting(E_ALL ^ E_NOTICE);

class Fila {
    public $Image;

    function __construct( ){
        include "vendor/autoload.php";

        require_once("include/globais.php");
        $this->Globais = new \raiz\Globais();
    }



        function PushTeamImage (  $request, $response, $args, $jsonRAW ){
            IF (!$jsonRAW) {
                $data =  array(	"resultado" =>  "ERRO",
                    "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

                return $response->withStatus(500)
                    ->withHeader('Content-type', 'application/json;charset=utf-8')
                    ->withJson($data);
            }
            // POSTAR NA FILA A IMAGEM
            $exchange = $this->Globais->Rabbit_exchange;
            $queue = $this->Globais->Rabbit_queue;

            $connection = new AMQPStreamConnection($this->Globais->Rabbit_host, $this->Globais->Rabbit_port, $this->Globais->Rabbit_username, $this->Globais->Rabbit_password, $this->Globais->Rabbit_vhost);
            $channel = $connection->channel();

            $channel->queue_declare($queue, false, true, false, false);
            $channel->exchange_declare($exchange, 'direct', false, true, false);
            $channel->queue_bind($queue, $exchange);


            $jsonRAW["IDTIME"] = $args["idtime"];
            $messageBody = json_encode($jsonRAW);

            //var_dump($messageBody);            exit;

            $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

            //for ($i=0;$i<1000;$i++)
              $channel->basic_publish($message, $exchange);

            $channel->close();
            $connection->close();

            $data["resultado"] = "SUCESSO";

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }



    function push (  $request, $response, $args, $jsonRAW ){
        IF (!$jsonRAW) {
            $data =  array(	"resultado" =>  "ERRO",
                "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        // POSTAR NA FILA A IMAGEM
        $exchange = $this->Globais->Rabbit_exchange;
        $queue = $this->Globais->Rabbit_queue;

        $connection = new AMQPStreamConnection($this->Globais->Rabbit_host, $this->Globais->Rabbit_port, $this->Globais->Rabbit_username, $this->Globais->Rabbit_password, $this->Globais->Rabbit_vhost);
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, 'direct', false, true, false);
        $channel->queue_bind($queue, $exchange);


        $jsonRAW["IDUSUARIO"] = $args["idusuario"];
        $messageBody = json_encode($jsonRAW);

        //var_dump($messageBody);
        

        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        //for ($i=0;$i<1000;$i++)
          $channel->basic_publish($message, $exchange);

        $channel->close();
        $connection->close();

        $data["resultado"] = "SUCESSO";
        $data["debug"] = $messageBody;

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
