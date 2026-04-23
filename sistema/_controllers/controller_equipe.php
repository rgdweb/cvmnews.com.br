<?php

class equipe extends controller {
	
	protected $_modulo_nome = "Equipe";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(71);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		//intancia
		$parceiros = new model_parceiros();
	 	$dados['lista'] = $parceiros->lista();		 
		
		$this->view('parceiros', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";
 		
 		$dados['aba_selecionada'] = "dados";
 	 	
		$this->view('parceiros.novo', $dados);
	}

	public function novo_grv(){

		//intancia
		$parceiros = new model_parceiros();
		
		//vars
		$titulo = $this->post('titulo');
		$endereco = $_POST['endereco'];

		//validacoes
		$this->valida($titulo);		 

		// grava
		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("parceiros", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo",
			"endereco"=>"$endereco"
		));
	 	$ultid = $db->ultimo_id();

	 	$ordematual = $parceiros->ordem();

		if($ordematual){
			$novaordem = $ordematual.",".$ultid;
		} else {
			$novaordem = $ultid;
		}		
		
		$db = new mysql();
		$db->inserir("parceiros_ordem", array(
			"data"=>"$novaordem"
		));

		$this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$codigo);
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";

 		//intancia
		$parceiros = new model_parceiros();

 		$codigo = $this->get('codigo');

 		$aba = $this->get('aba');
 		if($aba){
 			$dados['aba_selecionada'] = $aba;
 		} else {
 			$dados['aba_selecionada'] = 'dados';
 		}
 		
 		// pega dados
		$dados['data'] = $parceiros->carregar($codigo);


		$this->view('parceiros.alterar', $dados);
	}

	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');
		$endereco = $_POST['endereco'];

		$this->valida($codigo);
		$this->valida($titulo);

		$db = new mysql();
		$db->alterar("parceiros", array(
			"titulo"=>"$titulo",
			"endereco"=>"$endereco"
		), " codigo='$codigo' ");
	 	

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}

	public function imagem(){

		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		//carrega model de gestao de imagens
		$arquivo = new model_arquivos_imagens();

		$codigo = $this->get('codigo');

		$diretorio = "arquivos/img_parceiros/";

		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {
			
			//pega a exteção
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo  = $arquivo->trata_nome($nome_original);
			
			if(copy($tmp_name, $diretorio.$nome_arquivo)){
				
				if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
					
					$largura_g = 500;
					$altura_g = $arquivo->calcula_altura_jpg($tmp_name, $largura_g);
					
					//redimenciona
					$arquivo->jpg($diretorio.$nome_arquivo, $largura_g , $altura_g , $diretorio.$nome_arquivo);
				}
				
				//grava banco
				$db = new mysql();
				$db->alterar("parceiros", array(
					"imagem"=>"$nome_arquivo"
				), " codigo='$codigo' ");
				
				$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
				
			} else {
				
				$this->msg('Erro ao gravar imagem!');
				$this->irpara(DOMINIO.$this->_controller."/alterar/codigo/".$codigo."/aba/imagem");
				
			}

		}
		
	}

	public function apagar_imagem(){
		
		$codigo = $this->get('codigo');
		
		if($codigo){

			//intancia
			$parceiros = new model_parceiros();
			$data = $parceiros->carregar($codigo);

			if($data->imagem){
				unlink('arquivos/img_parceiros/'.$data->imagem);
			}

			$db = new mysql();
			$db->alterar("parceiros", array(
				"imagem"=>""
			), " codigo='$codigo' ");
		}


		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}

	public function ordem(){

		$list = $this->post('list');

		$output = array();
		parse_str($list, $output);
		$ordem = implode(',', $output['item']);

		$db = new mysql();
		$db->apagar("parceiros_ordem", " id!='0' ");
		
		$db = new mysql();
		$db->inserir("parceiros_ordem", array(
			"data"=>"$ordem"
		));

	}

	public function apagar_varios(){
		
		//intancia
		$parceiros = new model_parceiros();
		
		foreach ($parceiros->lista() as $key => $value) {
			
			if($this->post('apagar_'.$value['id']) == 1){
				
				if($value['arquivo']){
					unlink('arquivos/img_parceiros/'.$value['arquivo']);
				}
				
				$db = new mysql();
				$db->apagar("parceiros", " id='".$value['id']."' ");
				
			}
		}
		
		$this->irpara(DOMINIO.$this->_controller);
	}


}