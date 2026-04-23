<?php

class modulos extends controller {
	
	protected $_modulo_nome = "Módulos";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso('adm');
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$setores = new model_setores();
		$dados['lista'] = $setores->lista_completa();
		
		$this->view('modulos', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";
 		
		$this->view('modulos.novo', $dados);
	}
	
	public function novo_grv(){
		
		$titulo = $this->post('titulo');
		$titulo_tecnico = $this->post('titulo_tecnico');
		$ico = $this->post('ico');
		$id_pai = $this->post('id_pai');
		$endereco = $this->post('endereco');
		
		$this->valida($titulo);
		$this->valida($titulo_tecnico);
		
		$setores = new model_setores();
		$setores->inserir(array(
			$id_pai,
			$titulo,
			$titulo_tecnico,
			$endereco,
			$ico
		));
	 	
		$this->irpara(DOMINIO.$this->_controller);		
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Alterar";
 		
 		$id = $this->get('codigo');

 		$setores = new model_setores();
		$dados['data'] = $setores->selecionar($id);

		if(!isset($dados['data']) ) {
			$this->irpara(DOMINIO.$this->_controller);
		}

		$this->view('modulos.alterar', $dados);
	}

	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');
		$titulo_tecnico = $this->post('titulo_tecnico');
		$ico = $this->post('ico');
		$id_pai = $this->post('id_pai');
		$endereco = $this->post('endereco');
		
		$this->valida($codigo);
		$this->valida($titulo);
		$this->valida($titulo_tecnico);
		
		$setores = new model_setores();
		$setores->alterar(array(
			$id_pai,
			$titulo,
			$titulo_tecnico,
			$endereco,
			$ico
		), array(
			$codigo
		));
	 	
		$this->irpara(DOMINIO.$this->_controller);		
	}

	public function apagar_varios(){
		
		$setores = new model_setores();
		$lista = $setores->lista_completa();

		foreach ($lista as $key => $value) {			
			if($this->post('apagar_'.$value['id']) == 1){

				$setores->apagar(array(
					$value['id']
				));

			}
		}
		
		$this->irpara(DOMINIO.$this->_controller);
	}
	
	public function bloqueios(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Bloqueios";

		$setores = new model_setores();
		$lista = $setores->lista_completa();

		$lista_org = new model_ordena_permissoes();
		$lista_org->monta(0, $lista);

		$dados['lista'] = $lista_org->_lista_certa;
  		
		$this->view('modulos.bloqueios', $dados);
	}
	
	public function bloqueios_grv(){
		
		$setores = new model_setores();
		$lista = $setores->lista_completa();

		foreach ($lista as $key => $value) {

			if( $this->post('setor_'.$value['id']) ){
				
				if(!$value['check']){ 
					$setores->adiciona_perfil($value['id']);
				}
				
			} else {
				
				if($value['check']){ 
					$setores->remove_perfil($value['id']);					
				}

			}
		
		}

		$this->irpara(DOMINIO.$this->_controller.'/bloqueios');		
	}

}