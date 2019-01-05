<?php
//set_time_limit(10);


require('vendor/autoload.php');


class testImages extends PHPUnit\Framework\TestCase
{
    protected $client;

    protected function setUp()
    {

        $conf['timeout'] = 5;
        $conf['connect_timeout'] = 5;
        $conf['read_timeout'] = 5;
        $this->client = new GuzzleHttp\Client(   $conf );


        $this->imagem_teste = "https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png";

        require_once("include/globais.php");

        $this->Globais = new raiz\Globais();
    }

    public function OpenConf(){

      $configuracoes_externas = file_get_contents('include/config.json');
      $config_parsed = json_decode($configuracoes_externas,true);
      return $config_parsed;
    }

      public function SaveConf($conf){

           $fp = fopen('include/config.json', "w");
           if (fwrite($fp, json_encode($conf,true)))
                $sucesso = 1;
           else
                $sucesso = 0;
           fclose($fp);

          return $sucesso;
      }


      public function testGet_HealthCheck()
      {
          //var_dump($this->Globais->healthcheck); var_dump($JSON);

          $response = $this->client->request('GET', $this->Globais->healthcheck

              , array(
                  'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                  'timeout' => 10, // Response timeout
                  'connect_timeout' => 10 // Connection timeout

              )
          );
          $jsonRetorno = json_decode($response->getBody()->getContents(), 1);

          //var_dump(  $jsonRetorno );
          $this->assertEquals('SUCESSO', $jsonRetorno["resultado"]);

      }
//team images

    public function testPOST_PushTeamProfileImagetoQueue()
    {

        set_time_limit(10);
        $idtime = 10;

        $img = "data:".  image_type_to_mime_type( exif_imagetype (  $this->imagem_teste))   .";base64,".base64_encode(file_get_contents( $this->imagem_teste  ))    ;

        $JSON = json_decode( " {
          \"TipoImagem\":\"Profile\",

          \"imagem\":\"$img\"
        } " , true);
        //var_dump($JSON);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtime" => $idtime );
        //var_dump(strtr($this->Globais->PushTeamImagetoQueue, $trans));exit;

        $response = $this->client->request('POST', strtr($this->Globais->PushTeamImagetoQueue, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

     public function testPOST_getTeamImage()
    {
        $idtime = 10;

         $trans = null;$trans = array(":idtime" => $idtime );
        //var_dump(strtr($this->Globais->getTeamImage, $trans));

        $response = $this->client->request('GET', strtr($this->Globais->getTeamImage, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

    public function testPOST_getTeamsImages()
   {


       $idjogador = 10;
       ini_set('xdebug.var_display_max_depth', '10');
       ini_set('xdebug.var_display_max_children', '256');
       ini_set('xdebug.var_display_max_data', '1024');
     //  $time = "testProfileImage".rand(500,80500);
      //TODO: LOGICA DE BUSCAR POR MULTI times NAO FUNCIONANDO
       $JSON = json_decode( " {

         \"IDTIMES\":\"20,10,11,12,13,14,15,16,17,18,19\",
         \"TipoImagem\":\"Profile\"
       } " , true);
       //var_dump($JSON);
       if ($JSON == NULL ) die(" JSON erro de formacao");

       $trans = null;$trans = array(":idtime" => $idtime );
       $endpoint = strtr($this->Globais->getTeamsImage, $trans);
       //var_dump( $endpoint );

       $response = $this->client->request('POST', $endpoint

           , array(
               'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
               'timeout' => 10, // Response timeout
               'form_params' => $JSON,
               'connect_timeout' => 10 // Connection timeout


           )
       );
       $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
       //var_dump($jsonRetorno);

       $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
   }

   public function testPOST_setTeamImage()
  {


      $idtime = 10;
    //  $img = "testPOST_setTeamImage".rand(500,80500);
      $img = "data:".  image_type_to_mime_type( exif_imagetype (  $this->imagem_teste))   .";base64,".base64_encode(file_get_contents( $this->imagem_teste  ))    ;
    //  $time = "testProfileImage".rand(500,80500);

      $JSON = json_decode( " {


        \"imagem\":\"$img\",
        \"TipoImagem\":\"Profile\"
      } " , true);
    //  var_dump($JSON);
      if ($JSON == NULL ) die(" JSON erro de formacao");

      $trans = null;$trans = array(":idtime" => $idtime );
      $endpoint = strtr($this->Globais->setTeamImage, $trans);
    //  var_dump( $endpoint );

      $response = $this->client->request('POST', $endpoint

          , array(
              'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
              'timeout' => 10, // Response timeout
              'form_params' => $JSON,
              'connect_timeout' => 10 // Connection timeout


          )
      );
      $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
    //  var_dump($jsonRetorno);

      $Conf = $this->OpenConf();
      $Conf["idimagem"] = $jsonRetorno["idimagem"];


      if ($this->SaveConf($Conf) == 0){
        echo " Nao foi possivel salvar o arqvuio de conf";
        exit;
      }

      $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
  }







//players image

    public function testPOST_PushProfileImagetoQueue()
    {

        set_time_limit(10);
        $idjogador = 10;

        $time = "data:".  image_type_to_mime_type( exif_imagetype (  $this->imagem_teste))   .";base64,".base64_encode(file_get_contents( $this->imagem_teste  ))    ;

        $JSON = json_decode( " {
          \"TipoImagem\":\"Profile\",

          \"imagem\":\"$time\"
        } " , true);
        //var_dump($JSON);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogador" => $idjogador );
        //var_dump(strtr($this->Globais->CriarMeuTimeSalvar, $trans));

        $response = $this->client->request('POST', strtr($this->Globais->PushUserImagetoQueue, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

     public function testPOST_getPlayerImage()
    {


        $idjogador = 10;

      //  $time = "testProfileImage".rand(500,80500);



        $trans = null;$trans = array(":idjogador" => $idjogador );
        //var_dump(strtr($this->Globais->getPlayerImage, $trans));

        $response = $this->client->request('GET', strtr($this->Globais->getPlayerImage, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

    public function testPOST_getPlayersImages()
   {


       $idjogador = 10;

     //  $time = "testProfileImage".rand(500,80500);
      //TODO: LOGICA DE BUSCAR POR MULTI USUARIOS NAO FUNCIONANDO
       $JSON = json_decode( " {
         \"idimagem\":\"\",
         \"IDUSUARIOS\":\"1,10,2\",
         \"TipoImagem\":\"Profile\"
       } " , true);
       //var_dump($JSON);
       if ($JSON == NULL ) die(" JSON erro de formacao");

       $trans = null;$trans = array(":idjogador" => $idjogador );
       $endpoint = strtr($this->Globais->getPlayersImage, $trans);
       //var_dump( $endpoint );

       $response = $this->client->request('POST', $endpoint

           , array(
               'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
               'timeout' => 10, // Response timeout
               'form_params' => $JSON,
               'connect_timeout' => 10 // Connection timeout


           )
       );
       $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
       //var_dump($jsonRetorno);

       $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
   }

   public function testPOST_setPlayerImage()
  {


      $idjogador = 10;
    //  $img = "testPOST_setPlayerImage".rand(500,80500);
      $img = "data:".  image_type_to_mime_type( exif_imagetype (  $this->imagem_teste))   .";base64,".base64_encode(file_get_contents( $this->imagem_teste  ))    ;
    //  $time = "testProfileImage".rand(500,80500);

      $JSON = json_decode( " {


        \"imagem\":\"$img\",
        \"TipoImagem\":\"Profile\"
      } " , true);
    //  var_dump($JSON);
      if ($JSON == NULL ) die(" JSON erro de formacao");

      $trans = null;$trans = array(":idjogador" => $idjogador );
      $endpoint = strtr($this->Globais->setPlayerImage, $trans);
      //var_dump( $endpoint );

      $response = $this->client->request('POST', $endpoint

          , array(
              'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
              'timeout' => 10, // Response timeout
              'form_params' => $JSON,
              'connect_timeout' => 10 // Connection timeout


          )
      );
      $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
      //var_dump($jsonRetorno);

      $Conf = $this->OpenConf();
      $Conf["idimagem"] = $jsonRetorno["idimagem"];


      if ($this->SaveConf($Conf) == 0){
        echo " Nao foi possivel salvar o arqvuio de conf";
        exit;
      }

      $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
  }



}
