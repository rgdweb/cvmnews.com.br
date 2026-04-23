<?php

class textos extends controller {
	
	protected $_modulo_nome = "Textos";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(29);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$textos = new model_textos();		 
		$dados['lista'] = $textos->lista();
		
		$this->view('textos', $dados);
	}
	
	public function novo(){ 
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Novo";

		$dados['aba_selecionada'] = "dados";

		$this->view('textos.nova', $dados);
	}

	public function nova_grv(){		 

		$titulo = $this->post('titulo');
		$conteudo = $_POST['conteudo'];

		$this->valida($titulo);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("texto", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo",
			"conteudo"=>"$conteudo",
			"url"=>"$codigo",
			"nao_remover"=>"0"
		));
		
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Alterar";
		
		$codigo = $this->get('codigo');
		
		$aba = $this->get('aba');
		if($aba){
			$dados['aba_selecionada'] = $aba;
		} else {
			$dados['aba_selecionada'] = 'dados';
		}
		
		$dados['acesso_alterar_titulo'] = true;
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM texto where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
		$this->view('textos.alterar', $dados);
	}

	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');
		$conteudo = $_POST['conteudo'];
		$url = $this->post('url');

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM texto where url='$url' AND codigo!='$codigo' ");
		
		if($exec->num_rows != 0){
			$this->msg('Esta url já esta sendo utilizada por outra página!');
			$this->volta(1);
			exit;
		}

		$this->valida($titulo);
		
		$db = new mysql();
		$db->alterar("texto", array(
			"titulo"=>"$titulo",
			"conteudo"=>"$conteudo",
			"url"=>"$url"
		), " codigo='$codigo' ");


		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}

	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM texto WHERE nao_remover='0' ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				$conexao = new mysql();
				$conexao->apagar("texto", " id='$data->id' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller);		
	}



	public function imagem(){
		
		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];
		
		//carrega model de gestao de imagens
		$arquivo = new model_arquivos_imagens();

		$codigo = $this->get('codigo');

		$diretorio = "arquivos/imagens/";
		
		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {
			
			//pega a exteção
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo  = $arquivo->trata_nome($nome_original);
			
			if(copy($tmp_name, $diretorio.$nome_arquivo)){


				//grava banco
				$db = new mysql();
				$db->alterar("texto", array(
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

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM texto where codigo='$codigo' ");
			$data = $exec->fetch_object();

			if($data->imagem){
				unlink('arquivos/imagens/'.$data->imagem);
			}

			$db = new mysql();
			$db->alterar("texto", array(
				"imagem"=>""
			), " codigo='$codigo' ");
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}


}