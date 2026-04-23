<?php

class perfil extends controller {
	
	protected $_modulo_nome = "Usuários";

	public function init(){
		$this->autenticacao();
	}


	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "";
 		
 		$dados['data'] = $this->_dados_usuario;
 		
		if($this->get('aba')){
			$dados['aba_selecionada'] = $this->get('aba');
		} else {
			$dados['aba_selecionada'] = 'informacoes';	
		}
		
		$dados['listamenu'] = $this->lista_menu();
		
		$this->view('perfil', $dados);
	}


	public function alterar_grv(){

		$nome = $this->post('nome');
		$email_recuperacao = $this->post('email_recuperacao');
		
		$valida = new model_valida();
		if(!$valida->email($email_recuperacao)){
			$this->msg('E-mail inválido!');
		}
		$this->valida($nome);
		
		$db = new mysql();
		$db->alterar("adm_usuario", array(
			"nome"=>"$nome",
			"email_recuperacao"=>"$email_recuperacao"
		), " codigo='$this->_cod_usuario' ");  

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/informacoes');
	}


	public function apagar_imagem(){

		if($this->_dados_usuario->imagem){
			unlink('arquivos/img_usuarios/'.$this->_dados_usuario->imagem);
		}

		$db = new mysql();
		$db->alterar("adm_usuario", array(
			"imagem"=>""
		), " codigo='$this->_cod_usuario' "); 

		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/imagem');
	}


	public function alterar_senha(){
		
		$usuario = $this->post('usuario');
		$senha = $this->post('senha');
	 	
		$this->valida($usuario);
		$this->valida($senha);		 

		$usuario_md5 = md5($usuario);
		$senha_md5 = md5($senha);

		$db = new mysql();
		$db->alterar("adm_usuario", array(
			"usuario"=>"$usuario_md5",
			"senha"=>"$senha_md5"
		), " codigo='$this->_cod_usuario' ");

		$this->msg('Senha alterada com sucesso!');
		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/senha');
	}


	public function imagem(){
		
		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];
		
		$arquivo = new model_arquivos_imagens();
		
		//// Definicao de Diretorios / 
		$diretorio = "arquivos/img_usuarios/";
		
		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {		 

				//pega a exteção
				$nome_original = $arquivo_original['name'];
				$extensao = $arquivo->extensao($nome_original);
				$nome_arquivo = $arquivo->trata_nome($nome_original);
				
				if(copy($tmp_name, $diretorio.$nome_arquivo)){
					
					if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
						
						// foto grande
						$largura_g = 600;
						$altura_g = $arquivo->calcula_altura_jpg($diretorio.$nome_arquivo, $largura_g);
						
						//redimenciona
						$arquivo->jpg($diretorio.$nome_arquivo, $largura_g , $altura_g, $diretorio.$nome_arquivo);
						
					}
					
					$db = new mysql();
					$db->alterar("adm_usuario", array(
						"imagem"=>"$nome_arquivo"
					), " codigo='$this->_cod_usuario' "); 
					
					$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/imagem');
					
				} else {
					
					$this->msg('Não foi possível copiar o arquivo!');
					$this->volta(1);
					
				}
				
			}

	}
	
	
	public function ordem(){

		$list = $this->post('list');
		$output = array();
		parse_str($list, $output);
		$ordem = implode(',', $output['item']);

		$db = new mysql();
		$db->apagar("adm_setores_ordem", " usuario='$this->_cod_usuario' ");
		
		$db = new mysql();
		$db->inserir("adm_setores_ordem", array(
			"usuario"=>"$this->_cod_usuario",
			"data"=>"$ordem"
		));

	}
	
	
}