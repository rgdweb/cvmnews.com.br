<?php

Class model_galeria_imagem extends model{
    
    // ... (sua função original 'imagens_categoria' continua aqui) ...

    /**
     * Busca todas as imagens e retorna um array simples com as URLs completas.
     * Este é o método que nosso controller chama.
     */
    public function lista_completa_urls(){
        $lista_de_urls = array();
        $db = new mysql();
        $exec = $db->Executar("SELECT imagem FROM fotos_imagem ORDER BY id DESC");
        
        while($data = $exec->fetch_object()){
            if (!empty($data->imagem)) {
                $url_completa = PASTA_CLIENTE . 'img_fotos_g/' . $data->imagem;
                $lista_de_urls[] = $url_completa;
            }
        }
        return $lista_de_urls;
    }

}