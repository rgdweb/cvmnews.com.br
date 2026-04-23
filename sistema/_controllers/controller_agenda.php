<?php

class agenda extends controller {
	
	protected $_modulo_nome = "Agenda";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(45);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = ""; 
		
		$lista = array();
		
		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM agenda ORDER BY data desc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['data'] = date("d/m/Y H:i", $data->data);
			$lista[$n]['titulo'] = $data->titulo;

		$n++;
		}
		$dados['lista'] = $lista;
		
		$this->view('agenda', $dados);
	}
	

	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

 		$dados['hora'] = date('H', time());
 		$dados['minuto'] = date('i', time());
 		$dados['dia'] = date('d', time());
 		$dados['mes'] = date('m', time());
 		$dados['ano'] = date('Y', time());


		$this->view('agenda.novo', $dados);
	}


	public function novo_grv(){
		
		$titulo = $this->post('titulo');
		$descricao = $this->post('descricao');

		$hora = $this->post('hora');
		$dia = $this->post('dia');
		
		$arraydata = explode("/", $dia);
		
		$hora_montada = $arraydata[2]."-".$arraydata[1]."-".$arraydata[0]." ".$hora.":00";
		$data_final = strtotime($hora_montada);

		$this->valida($titulo);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("agenda", array(
			"codigo"=>"$codigo",
			"data"=>"$data_final",
			"titulo"=>"$titulo",
			"descricao"=>"$descricao"
		));
	 	
		$this->irpara(DOMINIO.$this->_controller);
	}
	

	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";

 		$codigo = $this->get('codigo');
 
 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM agenda where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		$dados['descricao'] = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $dados['data']->descricao);
 		$dados['hora'] = date('H', $dados['data']->data);
 		$dados['minuto'] = date('i', $dados['data']->data);
 		$dados['dia'] = date('d', $dados['data']->data);
 		$dados['mes'] = date('m', $dados['data']->data);
 		$dados['ano'] = date('Y', $dados['data']->data);

 		
		$this->view('agenda.alterar', $dados);
	}


	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');		 
		$descricao = $this->post('descricao');

		$hora = $this->post('hora');
		$dia = $this->post('dia');
		
		$arraydata = explode("/", $dia);
		
		$hora_montada = $arraydata[2]."-".$arraydata[1]."-".$arraydata[0]." ".$hora.":00";
		$data_final = strtotime($hora_montada);

		$this->valida($titulo);

		$db = new mysql();
		$db->alterar("agenda", array(
			"data"=>"$data_final",
			"titulo"=>"$titulo",
			"descricao"=>"$descricao"
		), " codigo='$codigo' ");
	 	

		$this->irpara(DOMINIO.$this->_controller);		
	}


	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM agenda ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){

				$conexao = new mysql();
				$conexao->apagar("agenda", " codigo='$data->codigo' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
	}


}