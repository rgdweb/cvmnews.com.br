<?php

class galeria extends controller {
    
    // A página não precisa de autenticação.
    public function init(){}
    
    public function inicial(){
    $dados = array();
    $dados['_base'] = $this->base();
    $dados['objeto'] = DOMINIO.$this->_controller.'/';
    $dados['controller'] = $this->_controller;

    // Banners e texto
    $banners = new model_banners();
    $dados['banners'] = $banners->lista('149601285477607');

    $db = new model_texto();
    $dados['texto'] = $db->conteudo('153693597068266');	

    // GALERIA DE IMAGENS
    $galeria_model = new model_galeria_imagem();
    $dados['fotos'] = $galeria_model->lista_completa_urls();

    $this->view('fotos_imagem', $dados);
}

    
}