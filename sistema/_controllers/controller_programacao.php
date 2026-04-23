<?php

class programacao extends controller {
	
	protected $_modulo_nome = "Programação";
	
	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(73);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";


		if($this->get('aba')){
			$dados['aba_selecionada'] = $this->get('aba');
		} else {
			$dados['aba_selecionada'] = 'domingo';
		}
		
		$programacao = new model_programacao();
		
		$dados['domingo'] = $programacao->lista(0);
		$dados['segunda'] = $programacao->lista(1);
		$dados['terca'] = $programacao->lista(2);
		$dados['quarta'] = $programacao->lista(3);
		$dados['quinta'] = $programacao->lista(4);
		$dados['sexta'] = $programacao->lista(5);
		$dados['sabado'] = $programacao->lista(6);

		$this->view('programacao', $dados);
	}	

	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

		$this->view('programacao.novo', $dados);
	}

	public function novo_grv(){

		$programa = $this->post('programa');
		$apresentador = $this->post('apresentador');
		$descricao = $this->post('descricao');

		$dia = $this->post('dia');
		$inicio = $this->post('inicio');
 		
		$this->valida($inicio);
		$this->valida($programa); 

		$hora_montada = "1984-08-22 ".$inicio.":00";
		$data_final = strtotime($hora_montada);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("programacao", array(
			"codigo"=>"$codigo",
			"dia"=>"$dia",
			"inicio"=>"$data_final",
			"programa"=>"$programa",
			"apresentador"=>"$apresentador",
			"descricao"=>"$descricao"
		));
	 	
		$this->irpara(DOMINIO.$this->_controller);
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";

 		$codigo = $this->get('codigo');
 		
 		$this->valida($codigo);

 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM programacao where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		$this->view('programacao.alterar', $dados);
	}
	
	public function alterar_grv(){
		
		$codigo = $this->post('codigo');
		
		$programa = $this->post('programa');
		$apresentador = $this->post('apresentador');
		$descricao = $this->post('descricao');
		
		$dia = $this->post('dia');
		$inicio = $this->post('inicio');
		
		$this->valida($codigo); 
		$this->valida($inicio);
		$this->valida($programa);
		
		$hora_montada = "1984-08-22 ".$inicio.":00";
		$data_final = strtotime($hora_montada);
		
		$db = new mysql();
		$db->alterar("programacao", array(
			"dia"=>"$dia",
			"inicio"=>"$data_final",
			"programa"=>"$programa",
			"apresentador"=>"$apresentador",
			"descricao"=>"$descricao"
		), " codigo='$codigo' ");
	 	
		if($dia == 0){ $aba = 'domingo'; }
		if($dia == 1){ $aba = 'segunda'; }
		if($dia == 2){ $aba = 'terca'; }
		if($dia == 3){ $aba = 'quarta'; }
		if($dia == 4){ $aba = 'quinta'; }
		if($dia == 5){ $aba = 'sexta'; }
		if($dia == 6){ $aba = 'sabado'; }
		
		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/'.$aba);
	}
	
	public function apagar(){
		
		$codigo = $this->get('codigo');
		
		$this->valida($codigo);
		
		$db = new mysql();
		$db->apagar("programacao", " codigo='$codigo' ");
		
		$this->irpara(DOMINIO.$this->_controller);
	}

}