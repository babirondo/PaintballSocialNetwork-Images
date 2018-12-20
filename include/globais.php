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

        $this->MongoConf["Index"] = "Imagens";
        $this->MongoConf["Type"]["campeonato"] = "Players";
        $this->MongoConf["Id"] = "id";

        $this->SaveImage = $servidor["images"]."/PaintballSocialNetwork-Images/Player/:idjogador";
    }

}
