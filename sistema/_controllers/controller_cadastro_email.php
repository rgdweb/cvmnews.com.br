<?php

class cadastro_email extends controller {
	
	protected $_modulo_nome = "Cadastro de E-mails";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(42);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$lista = array();

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM cadastro_email ");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['nome'] = $data->nome;
			$lista[$i]['email'] = $data->email;
			$lista[$i]['interesse'] = $data->interesse;
			
		$i++;
		}
		$dados['lista'] = $lista;
		
		$this->view('cadastro.email', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";
				

		$this->view('cadastro.email.novo', $dados);
	}

	public function novo_grv(){
		
		$nome = $this->post('nome');
		$email = $this->post('email');

		$this->valida($nome);
		$this->valida($email);

		$valida = new model_valida();
		if(!$valida->email($email)){
			$this->msg('Email inválido!');
			$this->volta(1);
		}

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM cadastro_email where email='$email' ");
		$linhas = $exec->num_rows;

		if($linhas != 0){
			$this->msg('E-mail já cadastrado!');
			$this->volta(1);
		} else {

			$db = new mysql();
			$db->inserir("cadastro_email", array(
				"nome"=>"$nome",
				"email"=>"$email",
				"interesse"=>"Cadastro manual"
			));
		 	
			$this->irpara(DOMINIO.$this->_controller);
		}
	}


	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM cadastro_email ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				$conexao = new mysql();
				$conexao->apagar("cadastro_email", " id='$data->id' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
	}


	public function importar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Importar";
		

		$this->view('cadastro.email.importar', $dados);
	}

	public function importar_lista(){
		
		$lista = $this->post('lista');
		$formato = $this->post('formato');
		
		if($formato == 1){
			$lista_array = explode(';', $lista);
		} else {
			$lista_array = explode(',', $lista);
		}

		$valida = new model_valida();

		$importados = 0;
		foreach ($lista_array as $key => $value) {

			if($valida->email($value)){

				$db = new mysql();
				$exec = $db->Executar("SELECT * FROM cadastro_email where email='$value' ");
				$linhas = $exec->num_rows;

				if($linhas == 0){

					$db = new mysql();
					$db->inserir("cadastro_email", array(
						"nome"=>"",
						"email"=>"$value",
						"interesse"=>"Importado"
					));
				 	
					$importados++;
				}
			}
		}

		$this->msg($importados.' email(s) importado(s)');
 		
		$this->irpara(DOMINIO.$this->_controller);
	}


	public function exportar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Exportar";
		
		$dados['mostrar_lista'] = false;

		//grupos
		$lista_grupos = array();

		$db = new mysql();
		$exec = $db->executar("SELECT interesse FROM cadastro_email ");
		$i = 0;
		while($data = $exec->fetch_object()) {			
			if(!in_array($data->interesse, $lista_grupos)){
				$lista_grupos[$i] = $data->interesse;
				$i++;
			}
		}
		$dados['lista_grupos'] = $lista_grupos;

		$formato = $this->post('formato');
		$grupo = $this->post('grupo');

		$dados['grupo'] = $grupo;
		$dados['formato'] = $formato;

		if($formato AND $grupo){

			$dados['mostrar_lista'] = true;

			if($formato == 1){
				$separador = ';';
			} else {
				$separador = ',';
			}

			$lista_exportada = '';
			if($grupo == 'todos'){

				$db = new mysql();
				$exec = $db->executar("SELECT * FROM cadastro_email ");
				while($data = $exec->fetch_object()) {
					$lista_exportada .= $data->email.$separador;
				}

			} else {

				$db = new mysql();
				$exec = $db->executar("SELECT * FROM cadastro_email where interesse='$grupo' ");
				while($data = $exec->fetch_object()) {
					$lista_exportada .= $data->email.$separador;
				}

			}
			$dados['lista_exportada'] = $lista_exportada;
		}

		$this->view('cadastro.email.exportar', $dados);
	}


}