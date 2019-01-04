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

    public function testPOST_PushProfileImagetoQueue()
    {

        set_time_limit(10);
        $idjogador = 10;

        $time = "testProfileImage".rand(500,80500);

        $JSON = json_decode( " {
          \"TipoImagem\":\"Profile\",

          \"imagem\":\"$time\"
        } " , true);
        //var_dump($JSON);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogador" => $idjogador );
        //var_dump(strtr($this->Globais->CriarMeuTimeSalvar, $trans));

        $response = $this->client->request('POST', strtr($this->Globais->PushImagetoQueue, $trans)

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

        $JSON = json_decode( " {
          \"idimagem\":\"\",


          \"TipoImagem\":\"Profile\"
        } " , true);
        //var_dump($JSON);
        if ($JSON == NULL ) die(" JSON erro de formacao");

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
      $img = "testPOST_setPlayerImage".rand(500,80500);

    //  $time = "testProfileImage".rand(500,80500);

      $JSON = json_decode( " {


        \"imagem\":\"$img\",
        \"TipoImagem\":\"Profile\"
      } " , true);
      //var_dump($JSON);
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
        echo " Nao foipossivel salvar o arqvuio de conf";
        exit;
      }

      $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
  }


  public function testDELETE_DeletePlayerImage()
 {
     $Conf = $this->OpenConf();
     $idimagem = $Conf["idimagem"];
     $idjogador = 10;


   //  $time = "testProfileImage".rand(500,80500);

     $JSON = json_decode( " {


       \"idimagem\":\"$idimagem\",
       \"TipoImagem\":\"Profile\"
     } " , true);
     //var_dump($JSON);
     if ($JSON == NULL ) die(" JSON erro de formacao");

     $trans = null;$trans = array(":idjogador" => $idjogador );
     $endpoint = strtr($this->Globais->DeletePlayerImageAPI, $trans);
     //var_dump( $endpoint );

     $response = $this->client->request('DELETE', $endpoint

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
/*
    public function testPUT_AlterandoMeuTime()
    {

        set_time_limit(10);
        $Conf = $this->OpenConf();
        $idjogador = 10;

        $time = "testAAA meu novo time ;) ALTERADO ".rand(500,8500);
        $idtime= $Conf["idtime"];

        $JSON = json_decode( " {\"time\":\"$time\",\"treino\":{\"Domingo\":\"Domingo\"},\"nivelcompeticao\":\"D2\",\"procurando\":{\"Doritos\":\"Doritos\"},\"localtreino\":\"Dublin\",\"foto\":{\"name\":\"\",\"type\":\"\",\"tmp_name\":\"\",\"error\":4,\"size\":0},\"idtime\":\"$idtime\"} " , true);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        //var_dump(strtr($this->Globais->MeusTimesRemoto, $trans));

        $response = $this->client->request('PUT', strtr($this->Globais->CriarMeuTimeSalvar, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 ,// Connection timeout,
           )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);

        $Conf = $this->OpenConf();
        $Conf["time"] = $time;
        if ($this->SaveConf($Conf) == 0){
          echo " Nao foipossivel salvar o arqvuio de conf";
          exit;
        }




        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

    public function testPOST_ProcurarTimes()
    {
        $Conf = $this->OpenConf();
        set_time_limit(10);
        $idjogador = 10;

        $JSON = json_decode( " {\"time\":\"".$Conf["time"]."\",\"treino\":null,\"localtreino\":\"\",\"nivelcompeticao\":\"\",\"procurando\":null,\"filtro\":1} " , true);

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        //var_dump(strtr($this->Globais->ProcurarTimes, $trans)); var_dump($JSON);

        $response = $this->client->request('POST', strtr($this->Globais->ProcurarTimes, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);

        //   var_dump($jsonRetorno);
        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }

    public function testGET_MeusTimes()
    {

        set_time_limit(10);
        $idjogador = 10;


        $JSON = json_decode( " {\"nome\":\"\",\"treino\":null,\"localtreino\":\"\",\"nivelcompeticao\":\"\",\"procurando\":null,\"filtro\":1} " , true);

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        //var_dump(strtr($this->Globais->MeusTimesRemoto, $trans));

        $response = $this->client->request('GET', strtr($this->Globais->MeusTimesRemoto, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);

        //   var_dump($jsonRetorno);
        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }
*/



}
