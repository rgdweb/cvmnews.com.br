<?php

class model_logo extends model {

    public $endereco;
    public $endereco2;

    public function __construct() {
        $this->endereco = $this->imagem();
        $this->endereco2 = $this->imagem(); // pode ajustar para outro método se quiser uma segunda logo diferente
    }

    public function imagem() {
        $db = new mysql();
        $exec = $db->executar("SELECT logo FROM adm_config WHERE id='1'");
        $data = $exec->fetch_object();

        if ($data->logo) {
            return PASTA_CLIENTE . 'img_logo/' . $data->logo;
        } else {
            return LAYOUT . "img/logo.png";
        }
    }
}
