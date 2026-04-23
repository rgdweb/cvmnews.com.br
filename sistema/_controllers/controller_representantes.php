<?php

class representantes extends controller {
	
	protected $_modulo_nome = "Representantes";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(62);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";		 

		$estado = $this->get('estado');
		if(!$estado){
			$estado = 'PR';
		}
		
		$representantes = new model_representantes();
		$dados['estados'] = $representantes->estados($estado);
		$dados['lista'] = $representantes->lista($estado);
		
		$this->view('representantes', $dados);
	}
	
	public function novo(){

		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

 		$dados['aba_selecionada'] = "dados";

 		$estados = array();
 		
		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM estado ORDER BY nome asc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$estados[$n]['nome'] = $data->nome;
			$estados[$n]['uf'] = $data->uf;

		$n++;
		}
		$dados['estados'] = $estados;


		$this->view('representantes.novo', $dados);
	}


	public function nova_grv(){
		
		$estado = $this->post('estado');
		$nome = $this->post('nome');
		$contato = $this->post('contato');
		$regiao = $this->post('regiao');
		$fone = $this->post('fone');
		$celular = $this->post('celular');
		$email = $this->post('email');

		$this->valida($nome);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("representante", array(
			"codigo"=>"$codigo",
			"estado"=>"$estado",
			"nome"=>"$nome",
			"contato"=>"$contato",
			"regiao"=>"$regiao",
			"fone"=>"$fone",
			"celular"=>"$celular",
			"email"=>"$email"
		));
	 	
		$this->irpara(DOMINIO.$this->_controller.'/inicial/estado/'.$estado);
	}
	

	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";
 		
 		$codigo = $this->get('codigo');
 		
 		$dados['aba_selecionada'] = "dados";
 		
 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM representante where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		$estados = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM estado ORDER BY nome asc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$estados[$n]['nome'] = $data->nome;
			$estados[$n]['uf'] = $data->uf;

			if($data->uf == $dados['data']->estado){
				$estados[$n]['selected'] = 'selected';
			} else {
				$estados[$n]['selected'] = '';
			}

		$n++;
		}
		$dados['estados'] = $estados;
 		
		$this->view('representantes.alterar', $dados);
	}


	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$estado = $this->post('estado');
		$nome = $this->post('nome');
		$contato = $this->post('contato');
		$regiao = $this->post('regiao');
		$fone = $this->post('fone');
		$celular = $this->post('celular');
		$email = $this->post('email');

		$this->valida($nome);
		
		$db = new mysql();
		$db->alterar("representante", array(
			"estado"=>"$estado",
			"nome"=>"$nome",
			"contato"=>"$contato",
			"regiao"=>"$regiao",
			"fone"=>"$fone",
			"celular"=>"$celular",
			"email"=>"$email"
		), " codigo='$codigo' ");	 	

		$this->irpara(DOMINIO.$this->_controller.'/inicial/estado/'.$estado);
	}


	public function apagar_varios(){
				
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM representante ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){				
				 
				$conexao = new mysql();
				$conexao->apagar("representante", " id='$data->id' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
		
	}


}