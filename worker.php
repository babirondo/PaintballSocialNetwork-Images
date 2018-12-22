<?php
//namespace raiz;
include("vendor/autoload.php");
include("include/globais.php");

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$Globais = new raiz\Globais();


function process_message($message)
{
  include_once("vendor/autoload.php");
  include_once("include/globais.php");

  $API = new \babirondo\REST\RESTCall();
  $Globais = new raiz\Globais();

  $json_mensagem = json_decode($message->body, true);

  $trans=null;$trans = array(":idjogador" => $json_mensagem["IDUSUARIO"]  );
  $query_API = $API->CallAPI("POST", strtr( $Globais->SaveImage, $trans), json_encode($json_mensagem));

  //var_dump($query_API);

  if (is_array($query_API)){
    if ($query_API["babirondo/rest-api"]["http_code"] == 200){
      $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
      echo ".";
    }
    else
      echo "X";
  }
  else
    echo "x";


}




$exchange = $Globais->Rabbit_exchange;
$queue = $Globais->Rabbit_queue;

$connection = new AMQPStreamConnection($Globais->Rabbit_host, $Globais->Rabbit_port, $Globais->Rabbit_username, $Globais->Rabbit_password, $Globais->Rabbit_vhost);

$channel = $connection->channel();
$channel->queue_declare($queue, false, true, false, false);
$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function(  'shutdown', $channel, $connection);
while (count($channel->callbacks)) {
    sleep(0.8);
    $channel->wait();
}
