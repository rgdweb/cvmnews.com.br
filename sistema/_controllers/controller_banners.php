<?php

class banners extends controller {
    
    protected $_modulo_nome = "Banners";

    public function init(){
        $this->autenticacao();
        $this->nivel_acesso(44);
    }

    public function inicial(){
        
        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "";
        
        $dados['acesso_grupos'] = false;
        
        $grupo = $this->get('grupo');
        
        $categorias = array();

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM banner_grupo ORDER BY titulo ASC");
        $i = 0;
        $grupo_titulo = "";
        while($data = $exec->fetch_object()) {
            $categorias[$i]['id'] = $data->id;
            $categorias[$i]['codigo'] = $data->codigo;
            $categorias[$i]['titulo'] = $data->titulo;

            if($grupo == $data->codigo){
                $categorias[$i]['selected'] = "selected";
                $grupo_titulo = $data->titulo;
            } else {
                $categorias[$i]['selected'] = "";
            }

            if(($i == 0) AND (!$grupo)){
                $grupo = $data->codigo;
                $categorias[$i]['selected'] = "selected";
                $grupo_titulo = $data->titulo;
            }
        $i++;
        }
        $dados['categorias'] = $categorias;
        $dados['grupo'] = $grupo;

        $lista = array();
        $exec = $db->Executar("SELECT data FROM banner_ordem WHERE codigo='$grupo' ORDER BY id DESC LIMIT 1");
        $data_ordem = $exec->fetch_object();

        if(isset($data_ordem->data)){
            $order = explode(',', $data_ordem->data);
            $n = 0;
            foreach($order as $id){
                $coisas = $db->Executar("SELECT * FROM banner WHERE id='$id' AND grupo='$grupo' LIMIT 1");
                if($coisas->num_rows > 0){
                    $data = $coisas->fetch_object();
                    $lista[$n]['id'] = $data->id;
                    $lista[$n]['codigo'] = $data->codigo;
                    $lista[$n]['titulo'] = $data->titulo;
                    $lista[$n]['categoria'] = $grupo_titulo;
                    $n++;
                }
            }
        }

        $dados['lista'] = $lista;
        $this->view('banners', $dados);
    }

    public function novo(){
        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Novo";
        $dados['aba_selecionada'] = "dados";
        $grupo = $this->get('grupo');
        $dados['grupo'] = $grupo;

        $lista = array();
        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM banner_grupo ORDER BY titulo ASC");
        $n = 0;
        while($data = $exec->fetch_object()){
            $lista[$n]['codigo'] = $data->codigo;
            $lista[$n]['titulo'] = $data->titulo;
            $lista[$n]['selected'] = ($grupo == $data->codigo) ? 'selected' : '';
        $n++;
        }
        $dados['categorias'] = $lista;
        $this->view('banners.novo', $dados);
    }

    public function nova_grv(){
        $titulo = $this->post('titulo');
        $categoria = $this->post('grupo');
        $endereco = $_POST['endereco'];

        $this->valida($titulo);
        $this->valida($categoria);

        $codigo = $this->gera_codigo();

        $db = new mysql();
        $db->inserir("banner", array(
            "codigo"=>$codigo,
            "titulo"=>$titulo,
            "grupo"=>$categoria,
            "endereco"=>$endereco
        ));
        $ultid = $db->ultimo_id();

        $coisas = $db->Executar("SELECT * FROM banner_ordem WHERE codigo='$categoria' ORDER BY id DESC LIMIT 1");
        $data = $coisas->fetch_object();

        if(isset($data->data)){
            $novaordem = $data->data.",".$ultid;
            $db->alterar("banner_ordem", array("data"=>$novaordem), "codigo='$categoria'");
        } else {
            $db->inserir("banner_ordem", array("codigo"=>$categoria, "data"=>$ultid));
        }

        $this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$codigo);
    }

    public function alterar(){
        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Alterar";

        $codigo = $this->get('codigo');
        $aba = $this->get('aba');
        $dados['aba_selecionada'] = $aba ? $aba : 'dados';

        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM banner WHERE codigo='$codigo'");
        $dados['data'] = $exec->fetch_object();

        $lista = array();
        $exec = $db->Executar("SELECT * FROM banner_grupo ORDER BY titulo ASC");
        $n = 0;
        while($data = $exec->fetch_object()){
            $lista[$n]['codigo'] = $data->codigo;
            $lista[$n]['titulo'] = $data->titulo;
            $lista[$n]['selected'] = ($dados['data']->grupo == $data->codigo) ? "selected" : "";
        $n++;
        }
        $dados['categorias'] = $lista;
        $this->view('banners.alterar', $dados);
    }

    public function alterar_grv(){
        $codigo = $this->post('codigo');
        $titulo = $this->post('titulo');
        $endereco = $_POST['endereco'];

        $this->valida($codigo);
        $this->valida($titulo);

        $db = new mysql();
        $db->alterar("banner", array("titulo"=>$titulo,"endereco"=>$endereco), "codigo='$codigo'");
        $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);
    }

    public function imagem(){
    $arquivo_original = $_FILES['arquivo'];
    $tmp_name = $_FILES['arquivo']['tmp_name'];
    $arquivo = new model_arquivos_imagens();
    $codigo = $this->get('codigo');
    $diretorio = "arquivos/img_banners/";

    $db = new mysql(); // ✅ ADICIONADO AQUI

    if(!$arquivo->filtro($arquivo_original)){
        $this->msg('Arquivo com formato inválido ou inexistente!');
        $this->volta(1);
    } else {
        $nome_original = $arquivo_original['name'];
        $extensao = $arquivo->extensao($nome_original);
        $nome_arquivo = $arquivo->trata_nome($nome_original);

        if(copy($tmp_name, $diretorio.$nome_arquivo)){
            if(in_array(strtolower($extensao), ['jpg','jpeg'])){
                $exec = $db->executar("SELECT grupo FROM banner WHERE codigo='$codigo'");
                $data = $exec->fetch_object();

                $exec = $db->executar("SELECT * FROM banner_grupo WHERE codigo='$data->grupo'");
                $data_grupo = $exec->fetch_object();

                if($data_grupo->largura){
                    $largura_g = $data_grupo->largura;
                    $altura_g = $arquivo->calcula_altura_jpg($tmp_name, $largura_g);
                    $arquivo->jpg($diretorio.$nome_arquivo, $largura_g, $altura_g, $diretorio.$nome_arquivo);
                }
            }

            // ✅ Agora o $db existe e pode usar
            $db->alterar("banner", array("imagem"=>$nome_arquivo), "codigo='$codigo'");
            $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
        } else {
            $this->msg('Erro ao gravar imagem!');
            $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
        }
    }
}



   
   public function apagar_imagem(){
        $codigo = $this->get('codigo');
        if($codigo){
            $db = new mysql();
            $exec = $db->executar("SELECT imagem FROM banner WHERE codigo='$codigo'");
            $data = $exec->fetch_object();
            if($data->imagem){
                unlink('arquivos/img_banners/'.$data->imagem);
            }
            $db->alterar("banner", array("imagem"=>""), "codigo='$codigo'");
        }
        $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
    }

    public function apagar(){
        $codigo = $this->get('codigo');
        if($codigo){
            $db = new mysql();
            $exec = $db->executar("SELECT id, grupo FROM banner WHERE codigo='$codigo'");
            $data = $exec->fetch_object();

            // remove imagem se existir
            $exec = $db->executar("SELECT imagem FROM banner WHERE codigo='$codigo'");
            $banner = $exec->fetch_object();
            if($banner->imagem){
                unlink('arquivos/img_banners/'.$banner->imagem);
            }

            // apagar banner
            $db->apagar("banner", "codigo='$codigo'");

            // atualiza ordem
            $coisas = $db->executar("SELECT data FROM banner_ordem WHERE codigo='$data->grupo'");
            $data_ordem = $coisas->fetch_object();
            if(isset($data_ordem->data)){
                $order = explode(",", $data_ordem->data);
                $novo = array();
                foreach($order as $id){
                    if($id != $data->id){
                        $novo[] = $id;
                    }
                }
                $nova_ordem = implode(",", $novo);
                $db->alterar("banner_ordem", array("data"=>$nova_ordem), "codigo='$data->grupo'");
            }
        }
        $this->irpara(DOMINIO.$this->_controller);
    }

    public function ordem(){
        $grupo = $this->post('grupo');
        $ordem = $this->post('ordem');

        $this->valida($grupo);

        $db = new mysql();
        $db->alterar("banner_ordem", array("data"=>$ordem), "codigo='$grupo'");
    }

    public function apagar_varios(){
        
        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM banner ");
        while($data = $exec->fetch_object()){
            
            if($this->post('apagar_'.$data->id) == 1){
                
                if($data->imagem){
                    unlink('arquivos/img_banners/'.$data->imagem);
                }

                $conexao = new mysql();
                $conexao->apagar("banner", " codigo='$data->codigo' ");
                
                $grupo = $data->grupo;
            }            
        }
        
        $this->irpara(DOMINIO.$this->_controller.'/inicial/grupo/'.$grupo);
        
    }


    public function grupos(){
        
        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Categorias";

        $categorias = array();

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM banner_grupo order by titulo asc");
        $i = 0;
        while($data = $exec->fetch_object()) {
            
            $categorias[$i]['id'] = $data->id;
            $categorias[$i]['codigo'] = $data->codigo;
            $categorias[$i]['titulo'] = $data->titulo;
            $categorias[$i]['largura'] = $data->largura;
            $categorias[$i]['altura'] = $data->altura;
            
        $i++;
        }
        $dados['categorias'] = $categorias;     
        
        $this->view('banners.categorias', $dados);
    }


    public function novo_grupo(){
        
        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Nova Categoria";

        $this->view('banners.categorias.nova', $dados);
    }


    public function novo_grupo_grv(){
        
        $titulo = $this->post('titulo');
        $largura = $this->post('largura');
        $altura = $this->post('altura');

        $this->valida($titulo);
        $this->valida($largura);
        $this->valida($altura);
        
        $codigo = $this->gera_codigo();

        $db = new mysql();
        $db->inserir("banner_grupo", array(
            "codigo"=>"$codigo",
            "titulo"=>"$titulo",
            "largura"=>"$largura",
            "altura"=>"$altura"
        ));

        $this->irpara(DOMINIO.$this->_controller.'/grupos');        
    }


    public function alterar_grupo(){

        $dados['_base'] = $this->base_layout();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Alterar Grupo";

        $codigo = $this->get('codigo');

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM banner_grupo WHERE codigo='$codigo' ");
        $dados['data'] = $exec->fetch_object();

        if(!isset($dados['data']) ) {
            $this->irpara(DOMINIO.$this->_controller.'/grupos');
        }

        $this->view('banners.categorias.alterar', $dados);
    }


    public function alterar_grupo_grv(){
        
        $codigo = $this->post('codigo');

        $titulo = $this->post('titulo');
        $largura = $this->post('largura');
        $altura = $this->post('altura');

        $this->valida($codigo);
        $this->valida($titulo);
        $this->valida($largura);
        $this->valida($altura);     
        
        $db = new mysql();
        $db->alterar("banner_grupo", array(
            "titulo"=>"$titulo",
            "largura"=>"$largura",
            "altura"=>"$altura"
        ), " codigo='$codigo' ");

        $this->irpara(DOMINIO.$this->_controller.'/grupos');        
    }


    public function apagar_grupos(){
        
        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM banner_grupo ");
        while($data = $exec->fetch_object()){
            
            if($this->post('apagar_'.$data->id) == 1){
                
                $conexao = new mysql();
                $conexao->apagar("banner_grupo", " id='$data->id' ");

            }
        }

        $this->irpara(DOMINIO.$this->_controller.'/grupos');        
    }

}

                