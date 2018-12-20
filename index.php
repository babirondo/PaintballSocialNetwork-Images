<?php
namespace raiz;
use Slim\Views\PhpRenderer;

include "vendor/autoload.php";

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$container = $app->getContainer();

$container['renderer'] = new PhpRenderer("./templates");

$app->post('/Analyze/Image/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_fila.php");

    $Fila = new Fila();
    $retorno = $Fila->push($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->get('/Player/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Player.images.php");

    $cPlayerImage = new PlayerImage();
    $retorno = $cPlayerImage->getPlayerImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->post('/Player/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Player.images.php");

    $cPlayerImage = new PlayerImage();
    $retorno = $cPlayerImage->setPlayerImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->delete('/Player/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Player.images.php");

    $cPlayerImage = new PlayerImage();
    $retorno = $cPlayerImage->DeletePlayerImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );


$app->run();
