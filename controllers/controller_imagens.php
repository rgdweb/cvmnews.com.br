<?php

class galeria extends controller {

    public function init(){}

    // Método para listar os álbuns
    public function albuns() {

        $dados = array();
        $dados['_base'] = $this->base();
        $dados['objeto'] = DOMINIO.$this->_controller.'/';
        $dados['controller'] = $this->_controller;

        // título da página
        $dados['pagina_titulo'] = "Galeria de Álbuns";

        // Buscar os álbuns (grupos)
        $db = new mysql();
        $exec = $db->Executar("SELECT codigo, titulo, imagem FROM fotos_grupos ORDER BY id DESC");
        $albuns = array();
        while($d = $exec->fetch_object()){
            $albuns[] = array(
                'codigo' => $d->codigo,
                'titulo' => $d->titulo,
                'capa'   => $d->imagem ? 'arquivos/'.$d->imagem : 'imagens/sem-capa.jpg'
            );
        }

        $dados['albuns'] = $albuns;

        // Carregar a view pública de álbuns
        $this->view('htm_fotos_lista_albuns', $dados);
    }

    // Método inicial (que você já tinha)
    public function inicial() {

        $dados = array();
        $dados['_base'] = $this->base();
        $dados['objeto'] = DOMINIO.$this->_controller.'/';
        $dados['controller'] = $this->_controller;

        // Banners
        $banners = new model_banners();
        $dados['banners'] = $banners->lista('149601285477607');

        // Texto
        $db = new model_texto();
        $dados['texto'] = $db->conteudo('153693597068266');

        // GALERIA DE IMAGENS
        $db = new mysql();
        $exec = $db->executar("SELECT * FROM fotos_imagem");
        $fotos = array();
        $n = 0;
        while($foto = $exec->fetch_object()){
            $fotos[$n]['id']     = $foto->id;
            $fotos[$n]['codigo'] = $foto->codigo;
            $fotos[$n]['imagem'] = $foto->imagem;
            $n++;
        }
        $dados['fotos'] = $fotos;

        $this->view('fotos_imagem', $dados);
    }

}
