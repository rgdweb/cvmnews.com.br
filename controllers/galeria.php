<?php
class galeria extends controller {

    // O núcleo SEMPRE chama init(), então mantemos mesmo vazio
    public function init(){
    }

    // Método inicial - lista os álbuns
    public function inicial(){

        $dados = array();
        $dados['_base'] = $this->base();
        $dados['objeto'] = DOMINIO.$this->_controller.'/';
        $dados['controller'] = $this->_controller;

        // banner
        $banners = new model_banners();
        $dados['banners'] = $banners->lista('149601285477607');

        // texto opcional
        $texto_model = new model_texto();
        $dados['texto'] = $texto_model->conteudo('153693597068266');

        // Buscar os álbuns
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

        // carrega view de lista de álbuns
        $this->view('htm_fotos_lista_albuns', $dados);
    }

    // Método para exibir as fotos de um álbum
    public function ver_album(){

        $dados = array();
        $dados['_base'] = $this->base();
        $dados['objeto'] = DOMINIO.$this->_controller.'/';
        $dados['controller'] = $this->_controller;

        // captura código do álbum via GET
        $codigo = isset($_GET['codigo']) ? $_GET['codigo'] : '';
        if(!$codigo){
            echo "<p>Álbum não informado.</p>";
            exit;
        }

        // busca o álbum
        $db = new mysql();
        $consulta = $db->Executar("SELECT titulo FROM fotos_grupos WHERE codigo='".$db->escape_string($codigo)."' ");
        if(!$consulta->num_rows){
            echo "<p>Álbum não encontrado.</p>";
            exit;
        }
        $info = $consulta->fetch_object();
        $dados['pagina_titulo'] = $info->titulo;

        // busca as fotos do álbum
        $fotos = array();
        $exec = $db->Executar("SELECT id, imagem FROM fotos_imagem WHERE codigo='".$db->escape_string($codigo)."' ORDER BY id DESC");
        while($f = $exec->fetch_object()){
            $fotos[] = array(
                'id'     => $f->id,
                'imagem' => 'arquivos/'.$f->imagem
            );
        }
        $dados['fotos'] = $fotos;

        // carrega a view que exibe as fotos do álbum
        $this->view('htm_fotos_ver_album', $dados);
    }
}
