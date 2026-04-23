<?php

class layout extends controller {
	
	protected $_modulo_nome = "Layout";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(43);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

 		$dados['data'] = $this->_dados_usuario;

		if($this->get('aba')){
			$dados['aba_selecionada'] = $this->get('aba');
		} else {
			$dados['aba_selecionada'] = 'menu';
		}

		$objeto_end = DOMINIO.$this->_controller.'/';

		function montaCategorias($id_pai, $objeto_end){

			$lista = '';

			$conexao = new mysql();
			$exec = $conexao->Executar("SELECT * FROM layout_menu_ordem WHERE id_pai='$id_pai' ORDER BY id desc limit 1");
			$data_ordem = $exec->fetch_object();

			if(isset($data_ordem->data)){

					$order = explode(',', $data_ordem->data);

					$lista .= '<ol class="dd-list">';

					foreach($order as $key => $value){

						$conexao = new mysql();
						$coisas = $conexao->Executar("SELECT * FROM layout_menu WHERE id='$value' ");
						$data = $coisas->fetch_object();

						if(isset($data->titulo)){

							$lista .= '<li class="dd-item dd3-item" data-id="'.$value.'" >';

							$lista .= '
							<div class="dd-handle dd3-handle" ><i class="fa fa-arrows"></i></div>
							<div class="dd3-content-editar" onClick="modal(\''.$objeto_end.'alterar_menu/codigo/'.$data->codigo.'\', \'Alterar Menu\');" ><i class="fa fa-pencil"></i></div>
							<div class="dd3-content">'.$data->titulo.'</div>';

							$lista .= montaCategorias($value, $objeto_end);

							$lista .= '</li>';

						}
					}

					$lista .= '</ol>';
			}
			return $lista;
		}
		$lista = montaCategorias(0, $objeto_end);		
		$dados['listamenu'] = $lista;

		function montaCategorias_rodape($id_pai, $objeto_end){

			$lista = '';

			$conexao = new mysql();
			$exec = $conexao->Executar("SELECT * FROM layout_menu_rodape_ordem WHERE id_pai='$id_pai' ORDER BY id desc limit 1");
			$data_ordem = $exec->fetch_object();

			if(isset($data_ordem->data)){

					$order = explode(',', $data_ordem->data);

					$lista .= '<ol class="dd-list">';

					foreach($order as $key => $value){

						$conexao = new mysql();
						$coisas = $conexao->Executar("SELECT * FROM layout_menu_rodape WHERE id='$value' ");
						$data = $coisas->fetch_object();

						if(isset($data->titulo)){

							$lista .= '<li class="dd-item dd3-item" data-id="'.$value.'" >';
							
							$lista .= '
							<div class="dd-handle dd3-handle" ><i class="fa fa-arrows"></i></div>
							<div class="dd3-content-editar" onClick="modal(\''.$objeto_end.'alterar_menu_rodape/codigo/'.$data->codigo.'\', \'Alterar Menu\');" ><i class="fa fa-pencil"></i></div>
							<div class="dd3-content">'.$data->titulo.'</div>';
							
							$lista .= montaCategorias_rodape($value, $objeto_end);
							
							$lista .= '</li>';

						}
					}

					$lista .= '</ol>';
			}
			return $lista;
		}
		$lista = montaCategorias_rodape(0, $objeto_end);
		$dados['listamenu_rodape'] = $lista;


		//cores
		$cores = new model_layout();
		$dados['listacores'] = $cores->lista();

		
		$this->view('layout', $dados);
	}

	public function novo_menu(){

		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$this->view('layout.novo.menu', $dados);
	}

	public function novo_menu_grv(){

		$nome = $this->post('nome');
		$destino = $_POST['destino'];
		
		$this->valida($nome);
		$this->valida($destino);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("layout_menu", array(
			"codigo"=>"$codigo",
			"titulo"=>"$nome",
			"endereco"=>"$destino"
		));

		$ultid = $db->ultimo_id();

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM layout_menu_ordem where id_pai='0' order by id desc limit 1");
		$data = $coisas->fetch_object();

		if(isset($data->data)){
			$novaordem = $data->data.",".$ultid;
		} else {
			$novaordem = $ultid;
		}

		$db = new mysql();
		$db->inserir("layout_menu_ordem", array(
			"id_pai"=>"0",
			"data"=>"$novaordem"
		));

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu');
	}

	public function salvar_ordem_menu(){

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
						$db->apagar("layout_menu_ordem", " id_pai='$pai_remover' ");
					}

			  	}

			  	$novaordem = substr($lista,0,-1);

			  	$db = new mysql();
				$db->inserir("layout_menu_ordem", array(
					"id_pai"=>"$id_pai",
					"data"=>"$novaordem"
				));

			}
			converte_array_para_banco($json);
			
			$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu');
			
		} else {
			$this->msg('Ocorreu um erro ao carregar ordem!');
			$this->volta(1);
		}

	}

	public function alterar_menu(){

		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$codigo = $this->get('codigo');

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM layout_menu WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		$this->view('layout.alterar.menu', $dados);
	}

	public function alterar_menu_grv(){

		$codigo = $this->post('codigo');

		$nome = $this->post('nome');
		$destino = $_POST['destino'];
		$visivel = $this->post('visivel');		 

		$this->valida($codigo);
		$this->valida($nome);		
		$this->valida($destino);

		$db = new mysql();
		$db->alterar("layout_menu", array(
			"titulo"=>"$nome",
			"endereco"=>"$destino",
			"visivel"=>"$visivel"
		), " codigo='$codigo' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu');
	}

	public function apagar_menu(){
		
		$codigo = $this->get('codigo');
		
		$this->valida($codigo);

		$db = new mysql();
		$db->apagar("layout_menu", " codigo='$codigo' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu');
	}

	///// rodape

	public function novo_menu_rodape(){

		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$this->view('layout.novo.menu.rodape', $dados);
	}

	public function novo_menu_rodape_grv(){

		$nome = $this->post('nome');
		$destino = $_POST['destino'];
		
		$this->valida($nome);
		$this->valida($destino);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("layout_menu_rodape", array(
			"codigo"=>"$codigo",
			"titulo"=>"$nome",
			"endereco"=>"$destino"
		));

		$ultid = $db->ultimo_id();

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM layout_menu_rodape_ordem where id_pai='0' order by id desc limit 1");
		$data = $coisas->fetch_object();

		if(isset($data->data)){
			$novaordem = $data->data.",".$ultid;
		} else {
			$novaordem = $ultid;
		}

		$db = new mysql();
		$db->inserir("layout_menu_rodape_ordem", array(
			"id_pai"=>"0",
			"data"=>"$novaordem"
		));

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu_rodape');
	}

	public function salvar_ordem_menu_rodape(){

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
						$db->apagar("layout_menu_rodape_ordem", " id_pai='$pai_remover' ");
					}

			  	}

			  	$novaordem = substr($lista,0,-1);

			  	$db = new mysql();
				$db->inserir("layout_menu_rodape_ordem", array(
					"id_pai"=>"$id_pai",
					"data"=>"$novaordem"
				));

			}
			converte_array_para_banco($json);
				
			$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu_rodape');
			
		} else {
			$this->msg('Ocorreu um erro ao carregar ordem!');
			$this->volta(1);
		}

	}

	public function alterar_menu_rodape(){

		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$codigo = $this->get('codigo');

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM layout_menu_rodape WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		$this->view('layout.alterar.menu.rodape', $dados);
	}

	public function alterar_menu_rodape_grv(){

		$codigo = $this->post('codigo');

		$nome = $this->post('nome');
		$destino = $_POST['destino'];
		$visivel = $this->post('visivel');		 

		$this->valida($codigo);
		$this->valida($nome);		
		$this->valida($destino);

		$db = new mysql();
		$db->alterar("layout_menu_rodape", array(
			"titulo"=>"$nome",
			"endereco"=>"$destino",
			"visivel"=>"$visivel"
		), " codigo='$codigo' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu_rodape');
	}

	public function apagar_menu_rodape(){
		
		$codigo = $this->get('codigo');
		
		$this->valida($codigo);

		$db = new mysql();
		$db->apagar("layout_menu_rodape", " codigo='$codigo' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/menu_rodape');
	}


	public function cores_grv(){
				
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM layout_cor ");
		while($data = $exec->fetch_object()){

			$titulo = $this->post('cor_titulo_'.$data->id);
			$cor = $_POST['cor_'.$data->id];

			$conexao = new mysql();
			$conexao->alterar("layout_cor", array(
				"cor"=>"$cor"
			), " id='$data->id' ");			
			
		}

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/cores');
	}


	public function cores_adm(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

 		$dados['data'] = $this->_dados_usuario;		 

		//cores
		$cores = new model_layout();
		$dados['listacores'] = $cores->lista();

		$this->view('layout_cores.adm', $dados);
	}	 

	public function cores_adm_grv(){

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM layout_cor ");
		while($data = $exec->fetch_object()){

			$titulo = $this->post('cor_titulo_'.$data->id);
			$cor = $_POST['cor_'.$data->id];			 
			
			$conexao = new mysql();
			$conexao->alterar("layout_cor", array(
				"titulo"=>"$titulo",
				"cor"=>"$cor"
			), " id='$data->id' ");
			
		}

		$this->irpara(DOMINIO.$this->_controller.'/cores_adm');
	}

	public function nova_cor(){
		
		$db = new mysql();
		$db->inserir("layout_cor", array(
			"titulo"=>"",
			"cor"=>""
		));
		
		$this->irpara(DOMINIO.$this->_controller.'/cores_adm');
	}
	
	public function apagar_cor(){
		
		$id = $this->get('id');			
		$this->valida($id);
		
		$db = new mysql();
		$db->apagar("layout_cor", " id='$id' ");
		
		$this->irpara(DOMINIO.$this->_controller.'/cores_adm');
	}
	
	
}