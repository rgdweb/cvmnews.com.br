<?php

class config extends controller {
	
	protected $_modulo_nome = "Configurações";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(2);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
 		
 		if($this->nivel_acesso(52, false)){
			$dados['acesso_emails'] = true;
		} else {
			$dados['acesso_emails'] = false;
		}
		if($this->nivel_acesso(36, false)){
			$dados['acesso_meta'] = true;
		} else {
			$dados['acesso_meta'] = false;
		}
		if($this->nivel_acesso(37, false)){
			$dados['acesso_smtp'] = true;
		} else {
			$dados['acesso_smtp'] = false;
		}
		if($this->nivel_acesso(38, false)){
			$dados['acesso_logo'] = true;
		} else {
			$dados['acesso_logo'] = false;
		}

 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM meta where id='1' ");
		$dados['data_meta'] = $exec->fetch_object();
		
 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM adm_config where id='1' ");
		$dados['data'] = $exec->fetch_object();
 		
		if($this->get('aba')){
			$dados['aba_selecionada'] = $this->get('aba');
		} else {

			if($dados['acesso_logo']){
				$dados['aba_selecionada'] = 'imagem';
			}
			if($dados['acesso_smtp']){
				$dados['aba_selecionada'] = 'smtp';
			}
			if($dados['acesso_meta']){
				$dados['aba_selecionada'] = 'meta';
			}
			if($dados['acesso_emails']){
				$dados['aba_selecionada'] = 'emails';
			}

		}

		//lista emails
		if($dados['acesso_emails']){

			$contatos = array();

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM contato order by titulo asc");
			$n = 0;
			while($dada = $exec->fetch_object()){

				$contatos[$n]['id'] = $dada->id;
				$contatos[$n]['codigo'] = $dada->codigo;
				$contatos[$n]['email'] = $dada->email;
				$contatos[$n]['titulo'] = $dada->titulo;

			$n++;
			}
			$dados['contatos'] = $contatos;

		}

		$this->view('config', $dados);
	}


	public function novo_email(){

		$dados['_base'] = $this->base_layout();


		$this->view('config.novo.email', $dados);
	}


	public function novo_email_grv(){
		
		$titulo = $this->post('titulo');
		$email = $this->post('email');

		$this->valida($titulo);
		$this->valida($email);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("contato", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo",
			"email"=>"$email"
		));

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/emails');
	}


	public function alterar_email(){

		$dados['_base'] = $this->base_layout();

		$id = $this->get('codigo');

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM contato where id='$id' ");
		$dados['data'] = $exec->fetch_object();

		$this->view('config.alterar.email', $dados);
	}
	
	
	public function alterar_email_grv(){
		
		$id = $this->post('id');
		$titulo = $this->post('titulo');
		$email = $this->post('email');

		$this->valida($titulo);
		$this->valida($email);
		$this->valida($id);

		$db = new mysql();
		$db->alterar("contato", array(
			"titulo"=>"$titulo",
			"email"=>"$email"
		), " id='$id' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/emails');
	}


	public function apagar_emails(){

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM contato ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				$conexao = new mysql();
				$conexao->apagar("contato", " id='$data->id' ");
					
			}
		}

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/emails');

	}


	public function meta_grv(){

		$this->nivel_acesso(36);

		$titulo_pagina = $this->post('titulo_pagina');
		$descricao = $this->post('descricao');

		$db = new mysql();
		$db->alterar("meta", array(
			"titulo_pagina"=>"$titulo_pagina",
			"descricao"=>"$descricao"
		), " id='1' ");

		$titulo_pagina = $this->post('titulo_pagina');
		$keywords1 = $this->post('keywords1');

		$db = new mysql();
		$db->alterar("meta", array(
			"titulo_pagina"=>"$titulo_pagina",
			"keywords1"=>"$keywords1"
		), " id='1' ");

		$titulo_pagina = $this->post('titulo_pagina');
		$keywords2 = $this->post('keywords2');

		$db = new mysql();
		$db->alterar("meta", array(
			"titulo_pagina"=>"$titulo_pagina",
			"keywords2"=>"$keywords2"
		), " id='1' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/meta');
	}


	public function smtp_grv(){

		$this->nivel_acesso(37);

		$email_nome = $this->post('email_nome');
		$email_origem = $this->post('email_origem');
		$email_retorno = $this->post('email_retorno');
		$email_porta = $this->post('email_porta');
		$email_host = $this->post('email_host');
		$email_usuario = $this->post('email_usuario');
		$email_senha = $this->post('email_senha');

		$this->valida($email_nome);
		$this->valida($email_origem);
		$this->valida($email_retorno);
		$this->valida($email_porta);
		$this->valida($email_host);
		$this->valida($email_usuario);
		$this->valida($email_senha);

		$db = new mysql();
		$db->alterar("adm_config", array(
			"email_nome"=>"$email_nome",
			"email_origem"=>"$email_origem",
			"email_retorno"=>"$email_retorno",
			"email_porta"=>"$email_porta",
			"email_host"=>"$email_host",
			"email_usuario"=>"$email_usuario",
			"email_senha"=>"$email_senha"
		), " id='1' ");

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/smtp');
	}


	public function apagar_logo(){

		$this->nivel_acesso(38);

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM adm_config where id='1' ");
		$dada = $exec->fetch_object();

		if($dada->logo){
			unlink('arquivos/img_logo/'.$dada->logo);
		}

		$db = new mysql();
		$db->alterar("adm_config", array(
			"logo"=>""
		), " id='1' "); 

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/imagem');
	}


	public function logo(){

		$this->nivel_acesso(38);

		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];
		
		//// Definicao de Diretorios / 
		$diretorio = "arquivos/img_logo/";
		
		if(substr($_FILES['arquivo']['name'],-3)=="exe" || 
				substr($_FILES['arquivo']['name'],-3)=="php" || 
				substr($_FILES['arquivo']['name'],-4)=="php3" || 
				substr($_FILES['arquivo']['name'],-4)=="php4"){
				
				$this->msg('Não é permitido enviar arquivos com esta extenção!');
				$this->volta(1);
			
		} else {				 

				$ext1 = $_FILES['arquivo']['name'];
				$ext2 = explode(".", $ext1); 
				$ext3 = strtolower(end($ext2)); 
				
				$icode = substr(time().rand(10000,99999),-15);				
				$nome_arquivo = sha1(uniqid($icode)).".".$ext3;
				
				$destino = $diretorio.$nome_arquivo;
				
				if(copy($tmp_name, $destino)){
					
					//so redimenciona se for jpg
					if($ext2 == "jpg"){

						$larg_ft = 500;
						
						$source = imagecreatefromjpeg($tmp_name); 
						$imagex = imagesx($source);
						$imagey = imagesy($source);
						$newy_ft = round(($larg_ft * $imagey) / $imagex);

						require_once('api/redimenciona/redimenciona.php');
						$redimenciona = new redimenciona();
						$redimenciona->reduz_imagem_jpg($destino, $larg_ft , $newy_ft , $destino);

					}


					$db = new mysql();
					$db->alterar("adm_config", array(
						"logo"=>"$nome_arquivo"
					), " id='1' "); 
					
					$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/imagem');
					
				} else {
					
					$this->msg('Não foi possível copiar o arquivo!');
					$this->volta(1);

				}
				
			}

	}


}