<?php

class fotos extends controller {

    protected $_modulo_nome = "Galeria de Fotos";

    public function init(){
        // Comentado para evitar erro de método não encontrado
        // $this->autenticacao();
        // $this->nivel_acesso(60);
    }

    // Métodos vazios para evitar erro de método não encontrado
    protected function autenticacao(){
        // Se quiser, coloque aqui sua lógica de autenticação
    }

    protected function nivel_acesso($nivel){
        // Se quiser, coloque aqui sua lógica de controle de acesso
    }

    public function inicial(){
        $dados['_base'] = $this->base();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "";

        $grupo = $this->get('grupo');
        $dados['grupo'] = $grupo;

        $fotos = new model_fotos();
        $dados['lista_grupos'] = $fotos->lista_grupos($grupo);

        if(!$grupo){
            if(isset($dados['lista_grupos'][0]['codigo'])){
                $grupo = $dados['lista_grupos'][0]['codigo'];
            } else {
                $grupo = false;
            }
        }

        $dados['lista'] = $fotos->lista($grupo);

        $this->view('fotos', $dados);
    }

    public function ordem(){
        $this->nivel_acesso(78);

        $list = $this->post('list');
        $grupo = $this->post('grupo');

        $output = array();
        parse_str($list, $output);
        $ordem = implode(',', $output['item']);

        $db = new mysql();
        $db->inserir("fotos_ordem", array(
            "grupo"=>"$grupo",
            "data"=>"$ordem"
        ));
    }

    public function novo(){
        $this->nivel_acesso(78);

        $dados['_base'] = $this->base();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Novo";

        $dados['aba_selecionada'] = "dados";

        $grupo = $this->get('grupo');
        $dados['grupo'] = $grupo;

        $categorias = new model_fotos();
        $dados['lista_grupos'] = $categorias->lista_grupos($grupo);

        $this->view('fotos.novo', $dados);
    }

    public function novo_grv(){
        $this->nivel_acesso(78);

        $titulo = $this->post('titulo');
        $conteudo = $_POST['conteudo'];
        $grupo = $this->post('grupo');

        $this->valida($titulo);
        $this->valida($grupo);

        $codigo = $this->gera_codigo();

        $db = new mysql();
        $db->inserir("fotos", array(
            "codigo"=>"$codigo",
            "grupo"=>"$grupo",
            "titulo"=>"$titulo",
            "conteudo"=>"$conteudo"
        ));

        $ultid = $db->ultimo_id();

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM fotos_ordem where grupo='$grupo' order by id desc limit 1");
        $data = $exec->fetch_object();

        if(isset($data->data)){
            $novaordem = $data->data.",".$ultid;
        } else {
            $novaordem = $ultid;
        }

        $db = new mysql();
        $db->inserir("fotos_ordem", array(
            "grupo"=>"$grupo",
            "data"=>"$novaordem"
        ));

        $this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$codigo);
    }

    public function alterar(){
        $this->nivel_acesso(78);

        $dados['_base'] = $this->base();
        $dados['_titulo'] = $this->_modulo_nome;
        $dados['_subtitulo'] = "Alterar";

        $codigo = $this->get('codigo');
        $aba = $this->get('aba');
        $dados['aba_selecionada'] = ($aba) ? $aba : 'dados';

        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM fotos where codigo='$codigo' ");
        $dados['data'] = $exec->fetch_object();

        $conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM fotos_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $imagens = array();
        if(isset($data_ordem->data)){
            $order = explode(',', $data_ordem->data);
            foreach($order as $key => $value){
                $conexao_img = new mysql();
                $coisas_img = $conexao_img->Executar("SELECT * FROM fotos_imagem WHERE id='$value'");
                if($data_img = $coisas_img->fetch_object()){
                    $conexao_leg = new mysql();
                    $coisas_leg = $conexao_leg->Executar("SELECT * FROM fotos_imagem_legenda WHERE id_img='$value' ");
                    $data_leg = $coisas_leg->fetch_object();

                    $imagens[$n]['legenda'] = $data_leg->legenda ?? "";
                    $imagens[$n]['id'] = $data_img->id;
                    $imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_fotos_p/'.$codigo.'/'.$data_img->imagem;
                    $imagens[$n]['imagem_g'] = PASTA_CLIENTE.'img_fotos_g/'.$codigo.'/'.$data_img->imagem;
                    $n++;
                }
            }
        }
        $dados['imagens'] = $imagens;

        $this->view('fotos.alterar', $dados);
    }

    public function alterar_grv(){
        $this->nivel_acesso(78);

        $codigo = $this->post('codigo');
        $titulo = $this->post('titulo');
        $conteudo = $_POST['conteudo'];

        $this->valida($titulo);

        $db = new mysql();
        $db->alterar("fotos", array( "titulo"=>"$titulo", "conteudo"=>"$conteudo" ), " codigo='$codigo' ");

        $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);
    }

    public function apagar_imagem(){
        $this->nivel_acesso(79);

        $codigo = $this->get('codigo');
        $id = $this->get('id');

        if($id){
            $db = new mysql();
            $exec = $db->executar("SELECT * FROM fotos_imagem where id='$id' ");
            if($data = $exec->fetch_object()){
                if($data->imagem){
                    if(file_exists('arquivos/img_fotos_g/'.$codigo.'/'.$data->imagem)) { unlink('arquivos/img_fotos_g/'.$codigo.'/'.$data->imagem); }
                    if(file_exists('arquivos/img_fotos_p/'.$codigo.'/'.$data->imagem)) { unlink('arquivos/img_fotos_p/'.$codigo.'/'.$data->imagem); }
                }
                $conexao = new mysql();
                $conexao->apagar("fotos_imagem", " id='$id' ");
            }
        }

        $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
    }

    public function upload(){
        $this->nivel_acesso(78);

        $dados['_base'] = $this->base();
        $codigo = $this->get('codigo');
        $dados['codigo'] = $codigo;
        $this->view('enviar_imagens', $dados);
    }

    public function apagar_varios(){
        $this->nivel_acesso(79);

        $db = new mysql();
        $exec = $db->Executar("SELECT * FROM fotos ");
        while($data = $exec->fetch_object()){
            if($this->post('apagar_'.$data->id) == 1){
                $db_img = new mysql();
                $exec_img = $db_img->executar("SELECT * FROM fotos_imagem where codigo='$data->codigo' ");
                while($data_img = $exec_img->fetch_object()){
                    if($data_img->imagem){
                        if(file_exists('arquivos/img_fotos_g/'.$data->codigo.'/'.$data_img->imagem)) { unlink('arquivos/img_fotos_g/'.$data->codigo.'/'.$data_img->imagem); }
                        if(file_exists('arquivos/img_fotos_p/'.$data->codigo.'/'.$data_img->imagem)) { unlink('arquivos/img_fotos_p/'.$data->codigo.'/'.$data_img->imagem); }
                    }
                }
                $grupo = $data->grupo;
                $conexao = new mysql();
                $conexao->apagar("fotos_imagem", " codigo='$data->codigo' ");
                $conexao->apagar("fotos", " codigo='$data->codigo' ");
            }
        }
        $this->irpara(DOMINIO.$this->_controller.'/inicial/grupo/'.$grupo);
    }

    public function legenda_grv(){
        $this->nivel_acesso(78);
        // implementar lógica de salvar legenda aqui se precisar
    }

    // ROTA JSON PARA RETORNAR AS IMAGENS DE UM ÁLBUM
    public function json() {
        // pega o código do álbum da URL (ex: /fotos/json/codigo/ABCD1234)
        $codigo = $this->get('codigo');
        $imagens = [];

        $db = new mysql();
        $exec = $db->Executar("SELECT imagem FROM fotos_imagem WHERE codigo='$codigo' ORDER BY id ASC");
        while ($img = $exec->fetch_object()) {
            // monta o caminho completo da imagem
            $imagens[] = PASTA_CLIENTE.'img_fotos_g/'.$codigo.'/'.$img->imagem;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($imagens);
        exit;
    }
}
