<?php

class cotacoes extends controller {
	
	protected $_modulo_nome = "Cotações";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(67);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$grupo = $this->get('grupo');
				
		$cotacoes = new model_cotacoes();		
		$dados['lista_grupos'] = $cotacoes->lista_grupos($grupo);
		
		if(!$grupo){
			if(isset($dados['lista_grupos'][0]['codigo'])){
				$grupo = $dados['lista_grupos'][0]['codigo'];
			} else {
				$grupo = false;
			}
		}
		
		$dados['lista'] = $cotacoes->lista($grupo);
		$dados['grupo'] = $grupo;

		$this->view('cotacoes', $dados);
	}

	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";
 		
 		$grupo = $this->get('grupo');
 		$dados['grupo'] = $grupo;
 		
 		//categorias
 		$cotacoes = new model_cotacoes();
		$dados['lista_grupos'] = $cotacoes->lista_grupos($grupo);


		
 		$this->view('cotacoes.novo', $dados);
	}

	public function novo_grv(){
		 
		$grupo = $this->post('grupo');
		$titulo = $this->post('titulo');
		$regiao = $this->post('regiao');
		$valor = $this->post('valor');

		$this->valida($titulo);
		$this->valida($grupo);
		$this->valida($valor);

		$codigo = $this->gera_codigo();
		$time = time();

		$db = new mysql();
		$db->inserir("cotacoes", array(
			"codigo"=>"$codigo",
			"grupo"=>"$grupo",
			"data"=>"$time",
			"titulo"=>"$titulo",
			"regiao"=>"$regiao",
			"valor"=>"$valor"
		));	 	 

		$this->irpara(DOMINIO.$this->_controller);
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";
 		
 		$codigo = $this->get('codigo'); 		 
 		
 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM cotacoes where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		//categorias
 		$cotacoes = new model_cotacoes();
		$dados['lista_grupos'] = $cotacoes->lista_grupos($dados['data']->grupo);
		
		
		$this->view('cotacoes.alterar', $dados);
	}
	
	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$grupo = $this->post('grupo');
		$titulo = $this->post('titulo');
		$regiao = $this->post('regiao');
		$valor = $this->post('valor');
		
		$this->valida($titulo);
		$this->valida($grupo);
		$this->valida($valor);
		
		$time = time();

		$db = new mysql();
		$db->alterar("cotacoes", array(
			"grupo"=>"$grupo",
			"data"=>"$time",
			"titulo"=>"$titulo",
			"regiao"=>"$regiao",
			"valor"=>"$valor"
		), " codigo='$codigo' ");
	 	

		$this->irpara(DOMINIO.$this->_controller);		
	}

	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM cotacoes ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				$grupo = $data->grupo;
				 
				$conexao = new mysql();
				$conexao->apagar("cotacoes", " codigo='$data->codigo' ");
				
			}
		}
		
		$this->irpara(DOMINIO.$this->_controller.'/inicial/grupo/'.$grupo);		
	}

	public function grupos(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Grupos";
		
		$cotacoes = new model_cotacoes();
		$dados['lista_grupos'] = $cotacoes->lista_grupos(); 

		$this->view('cotacoes.grupos', $dados);
	}

	public function novo_grupo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo Grupo";

		$this->view('cotacoes.grupos.novo', $dados);
	}

	public function novo_grupo_grv(){
		
		$titulo = $this->post('titulo');		
		$this->valida($titulo);
		
		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("cotacoes_grupos", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo"
		));

		$ultid = $db->ultimo_id();

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM cotacoes_grupos_ordem order by id desc limit 1");
		$data = $coisas->fetch_object();

		if(isset($data->data)){
			$novaordem = $data->data.",".$ultid;
		} else {
			$novaordem = $ultid;
		}

		$db = new mysql();
		$db->inserir("cotacoes_grupos_ordem", array(
			"id_pai"=>"0",
			"data"=>"$novaordem"
		));

		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}

	public function alterar_grupo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar Grupo";

 		$codigo = $this->get('codigo');

 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM cotacoes_grupos WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		if(!isset($dados['data']) ) {
			$this->irpara(DOMINIO.$this->_controller.'/grupos');
		}

		$this->view('cotacoes.grupos.alterar', $dados);
	}	

	public function alterar_grupo_grv(){
		
		$codigo = $this->post('codigo');
		$titulo = $this->post('titulo');
		
		$this->valida($codigo);
		$this->valida($titulo);

		$db = new mysql();
		$db->alterar("cotacoes_grupos", array(
			"titulo"=>"$titulo"
		), " codigo='$codigo' ");
	 	
		$this->irpara(DOMINIO.$this->_controller.'/grupos');		
	}

	public function apagar_grupo(){
		
		$codigo = $this->get('codigo');

		$this->valida($codigo);

		$conexao = new mysql();
		$conexao->apagar("cotacoes_grupos", " codigo='$codigo' ");
		
		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}

	public function salvar_ordem_grupos(){

		$ordem = stripcslashes($_POST['ordem']);		 

		if($ordem){

			$json = json_decode($ordem, true);
			
			function converte_array_para_banco($jsonArray, $id_pai = 0) {

				$lista = "";

				foreach ($jsonArray as $subArray) {

					$lista .= $subArray['id'].",";

					if (isset($subArray['children'])) {
				  		converte_array_para_banco($subArray['children'], $subArray['id']);
					} else {
						$pai_remover = $subArray['id'];
						$db = new mysql();
						$db->apagar("cotacoes_grupos_ordem", " id_pai='$pai_remover' ");
					}

			  	}

			  	$novaordem = substr($lista,0,-1);

			  	$db = new mysql();
				$db->inserir("cotacoes_grupos_ordem", array(
					"id_pai"=>"$id_pai",
					"data"=>"$novaordem"
				));

			}
			converte_array_para_banco($json);
			
			$this->irpara(DOMINIO.$this->_controller.'/grupos');
			
		} else {
			$this->msg('Ocorreu um erro ao carregar ordem!');
			$this->volta(1);
		}
	}

//termina classe
}