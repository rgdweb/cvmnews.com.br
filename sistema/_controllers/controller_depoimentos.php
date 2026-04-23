<?php

class depoimentos extends controller {
	
	protected $_modulo_nome = "Depoimentos";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(51);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$aba = $this->get('aba');

		if($aba){
			$dados['aba_selecionada'] = $aba;
		} else {
			$dados['aba_selecionada'] = 'aguardando';
		}

		//intancia
		$depoimentos = new model_depoimentos();	

		$lista = $depoimentos->lista();
		$dados['aguardando'] = $lista['aguardando'];
		$dados['aprovados'] = $lista['aprovados'];		

		$this->view('depoimentos', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

		$this->view('depoimentos.novo', $dados);
	}

	public function novo_grv(){
		
		$nome = $this->post('nome');
		$email = $this->post('email');
		$cidade = $this->post('cidade');
		$conteudo = $this->post('conteudo');

		$this->valida($nome);
		$this->valida($conteudo); 

		$time = time();

		$dados = array(
			'data'		=>$time,
			'nome'		=>$nome,
			'email'		=>$email,
			'cidade'	=>$cidade,
			'conteudo'	=>$conteudo,
			'bloqueio'	=>"2"
		);
		$db = new mysql();
		$db->inserir("depoimento", $dados);
	 	

		$this->irpara(DOMINIO.$this->_controller);
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Alterar";
 		
 		//intancia
		$depoimentos = new model_depoimentos();

 		$id = $this->get('id');
 		
		$dados['data'] = $depoimentos->carregar($id);


		$this->view('depoimentos.alterar', $dados);
	}

	public function alterar_grv(){
		
		$id = $this->post('id');
		$nome = $this->post('nome');
		$email = $this->post('email');
		$cidade = $this->post('cidade');
		$conteudo = $this->post('conteudo');
		$bloqueio = $this->post('bloqueio');

		$this->valida($id);
		$this->valida($nome);
		$this->valida($conteudo);

		$dados = array(
			'nome'		=>$nome,
			'email'		=>$email,
			'cidade'	=>$cidade,
			'conteudo'	=>$conteudo,
			'bloqueio'	=>$bloqueio
		);
		$db = new mysql();
		$db->alterar("depoimento", $dados, " id='".$id."' ");


		$this->irpara(DOMINIO.$this->_controller);		
	}

	public function apagar(){
		
		$id = $this->get('id');
		
		$this->valida($id);
		
		//intancia
		$depoimentos = new model_depoimentos();
		$data = $depoimentos->carregar($id);
		
		if($data->imagem){
			unlink('arquivos/img_depoimentos/'.$data->imagem);
		}
		
		// executa
		$db = new mysql();
		$db->apagar("depoimento", " id='".$id."' ");
		
		
		$this->irpara(DOMINIO.$this->_controller);		
	}

	public function imagem(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Alterar";
 		
 		//intancia
		$depoimentos = new model_depoimentos();

 		$id = $this->get('id');
 		
		$dados['data'] = $depoimentos->carregar($id);

		$this->view('depoimentos.imagem', $dados);
	}

	public function alterar_imagem(){

		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		//carrega model de gestao de imagens
		$arquivo = new model_arquivos_imagens();
		
		$depoimentos = new model_depoimentos();		

		$id = $this->post('id');
		$this->valida($id);

		$diretorio = "arquivos/img_depoimentos/";

		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {
			
			//pega a exteção
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo  = $arquivo->trata_nome($nome_original);
			
			if(copy($tmp_name, $diretorio.$nome_arquivo)){

				if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
					
					$largura_g = 800;
					$altura_g = $arquivo->calcula_altura_jpg($diretorio.$nome_arquivo, $largura_g);
					//redimenciona
					$arquivo->jpg($diretorio.$nome_arquivo, $largura_g , $altura_g , $diretorio.$nome_arquivo);
					
				}

				$db = new mysql();
				$db->alterar("depoimento", array(
					"imagem"=>$nome_arquivo
				), " id='".$id."' ");

				$this->irpara(DOMINIO.$this->_controller.'/imagem/id/'.$id);
				
			} else {
				
				$this->msg('Erro ao gravar imagem!');
				$this->irpara(DOMINIO.$this->_controller.'/imagem/id/'.$id);
				
			}

		}
		
	}

	public function apagar_imagem(){
		
		// Instancia
		$depoimentos = new model_depoimentos();

		$id = $this->get('id');
		
		if($id){

			$data = $depoimentos->carregar($id);

			if($data->imagem){
				unlink('arquivos/img_depoimentos/'.$data->imagem);
			}

			//grava banco
			$db = new mysql();
			$db->alterar("depoimento", array(
				"imagem"=>""
			), " id='".$id."' ");

		}

		$this->irpara(DOMINIO.$this->_controller.'/imagem/id/'.$id);
	}
	
	
}