<?php
namespace raiz;
use Slim\Views\PhpRenderer;

include "vendor/autoload.php";

$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$container = $app->getContainer();

$container['renderer'] = new PhpRenderer("./templates");




//times
$app->post('/Teams/Analyze/Image/{idtime}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_fila.php");

    $Fila = new Fila();
    $retorno = $Fila->PushTeamImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );
$app->delete('/Team/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Team.images.php");

    $cTeamImage = new TeamImage();
    $retorno = $cTeamImage->DeleteTeamImageAPI($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->post('/Teams/', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Team.images.php");

    $cTeamImage = new TeamImage();
    $retorno = $cTeamImage->getTeamImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->get('/Team/{idtime}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Team.images.php");

    $cTeamImage = new TeamImage();
    $retorno = $cTeamImage->getTeamImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->post('/Team/{idtime}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Team.images.php");

    $cTeamImage = new TeamImage();
    $retorno = $cTeamImage->setTeamImage($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );





// players
$app->post('/Usuarios/Analyze/Image/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_fila.php");

    $Fila = new Fila();
    $retorno = $Fila->push($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->delete('/Player/{idusuario}', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Player.images.php");

    $cPlayerImage = new PlayerImage();
    $retorno = $cPlayerImage->DeletePlayerImageAPI($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;

}  );

$app->post('/Players/', function ($request, $response, $args)  use ($app )   {
    require_once("include/class_Player.images.php");

    $cPlayerImage = new PlayerImage();
    $retorno = $cPlayerImage->getPlayerImage($request, $response, $args ,  $request->getParsedBody() );

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




$app->get('/healthcheck/', function ($request, $response, $args)  use ($app )   {
    require_once("healthcheck/healthcheck.php");

    $HealthCheck = new HealthCheck();

    $retorno = $HealthCheck->check($response, $request->getParsedBody() );
    return $retorno;
}  );


$app->run();
