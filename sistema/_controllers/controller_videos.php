<?php
class videos extends controller {
	protected $_modulo_nome = "Vídeos";
	
	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(64);
	}
	
	public function inicial(){
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		$videos = new model_videos(); 	
		$dados['lista'] = $videos->lista();
		$this->view('videos', $dados);
	}
	
	public function novo(){
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Novo";
		$dados['aba_selecionada'] = "dados";
		$videos = new model_videos();
		$this->view('videos.novo', $dados);
	}
	
	public function novo_grv(){
    $titulo = $this->post('titulo');
    $video = $_POST['video'];
    $this->valida($titulo);
    $this->valida($video);
    $codigo = $this->gera_codigo();
    
    $db = new mysql();
    $db->inserir("videos", array(
        "codigo"=>"$codigo",
        "titulo"=>"$titulo",
        "video"=>"$video"
    ));
    
    $ultid = $db->ultimo_id();
    
    // Busca a ordem atual
    $conexao = new mysql();
    $coisas = $conexao->Executar("SELECT * FROM videos_ordem ORDER BY id DESC LIMIT 1");
    $data = $coisas->fetch_object();
    
    if(isset($data->data) && !empty($data->data)){
        $novaordem = $data->data.",".$ultid;
    } else {
        $novaordem = $ultid;
    }
    
    // Remove ordens antigas e insere a nova
    $db->apagar("videos_ordem", "id > 0");
    $db->inserir("videos_ordem", array("data"=>"$novaordem"));
    
    $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);
}
	
	public function alterar(){
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Alterar";
		$videos = new model_videos();
		$codigo = $this->get('codigo');
		$dados['data'] = $videos->carrega($codigo);
		$this->view('videos.alterar', $dados);
	}
	
	public function alterar_grv(){
		$codigo = $this->post('codigo');
		$titulo = $this->post('titulo');
		$video = $_POST['video'];
		$this->valida($codigo);
		$this->valida($titulo);
		$this->valida($video);
		$db = new mysql();
		$db->alterar("videos", array(
			"titulo"=>"$titulo",
			"video"=>"$video"
		), " codigo='$codigo' ");
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}
	
	public function ordem(){
    $ordem = $this->post('ordem');
    
    if(empty($ordem)){
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','msg'=>'Ordem não informada']);
        return;
    }
    
    // Converte a string em array
    $nova_ordem = explode(',', $ordem);
    
    if(empty($nova_ordem) || count($nova_ordem) == 0){
        header('Content-Type: application/json');
        echo json_encode(['status'=>'error','msg'=>'Ordem inválida']);
        return;
    }
    
    try {
        $db = new mysql();
        
        // Remove registros antigos da tabela de ordem
        $db->apagar("videos_ordem", "id > 0");
        
        // Insere a nova ordem
        $ordem_final = implode(',', $nova_ordem);
        $db->inserir("videos_ordem", array("data" => $ordem_final));
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'ordem' => $ordem_final,
            'total' => count($nova_ordem)
        ]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'msg' => 'Erro ao salvar ordem: ' . $e->getMessage()
        ]);
    }
}
	
	public function apagar_varios(){
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM videos");
		while($data = $exec->fetch_object()){
			if($this->post('apagar_'.$data->id) == 1){				
				$conexao = new mysql();
				$conexao->apagar("videos", " codigo='$data->codigo'");
			}
		}
		$this->irpara(DOMINIO.$this->_controller.'/inicial');		
	}
}