<?php
namespace raiz;

error_reporting(E_ALL ^ E_DEPRECATED ^E_NOTICE);
class Globais{

    public $env;
    public $banco;

    function __construct( ){
        $this->env = "local";

        $servidor["UI"] = $servidor["frontend"] = "http://192.168.0.150:81";
        $servidor["autenticacao"] = "http://192.168.0.150:82";
        $servidor["players"] = "http://192.168.0.150:83";
        $servidor["campeonato"] = "http://192.168.0.150:81";
        $servidor["images"] = "http://192.168.0.150:85";
        $servidor["times"] = "http://192.168.0.150:86";

        $servidor["bancodados_campeonato"] = "192.168.0.150";
        $servidor["bancodados_players"] = "192.168.0.150";
        $servidor["rabbitmq"] = "192.168.0.150";
        $servidor["bancodados_images"] = "localhost";
    //    $servidor["bancodados_images"] = "192.168.0.150";

        $this->verbose=1;

        switch($this->env){

            case("local");

                $this->Rabbit_exchange = 'Router_NovaImagem';
                $this->Rabbit_queue = 'NovaImagem';

                $this->Rabbit_MessageBroker = "RabbitMQ";
                $this->Rabbit_host = $servidor["rabbitmq"];
                $this->Rabbit_username = "imagens";
                $this->Rabbit_password = "imagens";
                $this->Rabbit_port = 5672 ;
                $this->Rabbit_vhost ="imagens";


                $this->Mongo_banco = "Mongo";
                $this->Mongo_localhost = $servidor["bancodados_images"];
                $this->Mongo_username = "postgres";
                $this->Mongo_port = "27017";
                $this->Mongo_password = "postgres";
                $this->Mongo_db ="championship_local";
            break;

        }


        // extraindo configuracoes adicionais do arquivo config.json
       	$configuracoes_externas = file_get_contents('include/config.json');
       	$config_parsed = json_decode($configuracoes_externas,true);
       	$this->external_config = $config_parsed;

        $this->MongoConf["Index"] = "Imagens";
        $this->MongoConf["Type"]["campeonato"] = "Players";
        $this->MongoConf["Id"] = "id";

        $this->healthcheck = $servidor["images"]."/PaintballSocialNetwork-Images/healthcheck/"; //UNIT TEST

        $this->PushTeamImagetoQueue = $servidor["images"]."/PaintballSocialNetwork-Images/Teams/Analyze/Image/:idtime"; //UNIT TEST
        $this->SaveTeamImage = $servidor["images"]."/PaintballSocialNetwork-Images/Team/:idtime";
        $this->getTeamsImage = $servidor["images"]."/PaintballSocialNetwork-Images/Teams/"; //UNIT TEST
        $this->getTeamImage = $servidor["images"]."/PaintballSocialNetwork-Images/Team/:idtime"; //UNIT TEST
        $this->setTeamImage = $servidor["images"]."/PaintballSocialNetwork-Images/Team/:idtime"; //UNIT TEST
        $this->DeleteTeamImageAPI = $servidor["images"]."/PaintballSocialNetwork-Images/Team/:idtime"; //UNIT TEST

        $this->SaveImage = $servidor["images"]."/PaintballSocialNetwork-Images/Player/:idjogador";
        $this->PushUserImagetoQueue = $servidor["images"]."/PaintballSocialNetwork-Images/Usuarios/Analyze/Image/:idjogador"; //UNIT TEST
        $this->getPlayersImage = $servidor["images"]."/PaintballSocialNetwork-Images/Players/"; //UNIT TEST
        $this->getPlayerImage = $servidor["images"]."/PaintballSocialNetwork-Images/Player/:idjogador"; //UNIT TEST
        $this->setPlayerImage = $servidor["images"]."/PaintballSocialNetwork-Images/Player/:idjogador"; //UNIT TEST
        $this->DeletePlayerImageAPI = $servidor["images"]."/PaintballSocialNetwork-Images/Player/:idjogador"; //UNIT TEST


        $this->CallbackTeamImageProcess = $servidor["times"]."/PaintballSocialNetwork-Teams/Team/ImageProcessed"; //UNIT TEST
        $this->CallbackPlayerImageProcess = $servidor["players"]."/PaintballSocialNetwork-Players/Player/:idusuario/ImageProcessed"; //UNIT TEST


    }

}
