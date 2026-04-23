<?php

class downloads extends controller {
	
	protected $_modulo_nome = "Downloads";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(70);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
				
		$lista = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM downloads ORDER BY titulo asc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['titulo'] = $data->titulo;

		$n++;
		}
		$dados['lista'] = $lista;

		$this->view('downloads', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

 		$dados['aba_selecionada'] = "dados";

		$this->view('downloads.novo', $dados);
	}

	public function novo_grv(){
		
		$titulo = $this->post('titulo');
		$endereco = $this->post('endereco');

		$this->valida($titulo);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("downloads", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo",
			"endereco"=>"$endereco"
		));
	 	
		$this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$codigo);
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

 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM downloads where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		$this->view('downloads.alterar', $dados);
	}

	public function alterar_grv(){
				
		$codigo = $this->post('codigo');		
		$titulo = $this->post('titulo');
		$endereco = $this->post('endereco'); 

		$this->valida($titulo);
		$this->valida($endereco);
		
		$db = new mysql();
		$db->alterar("downloads", array(
			"titulo"=>"$titulo",
			"endereco"=>"$endereco"
		), " codigo='$codigo' ");
	 	
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}

	public function imagem(){

		$codigo = $this->get('codigo');
		$this->valida($codigo);

		// carrega model
		$arquivo = new model_arquivos_imagens();

		if(!$arquivo->filtro_imagem($_FILES['arquivo'])){
			
			$this->msg('Arquivo com formato inválido ou inexistente!');
			$this->volta(1);
			
		} else {

			$arquivo_original = $_FILES['arquivo'];
			$tmp_name = $_FILES['arquivo']['tmp_name'];
		 	
			//// Definicao de Diretorios / 
			$diretorio = "arquivos/downloads/";
			
			//pega a exteção
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo = $arquivo->trata_nome($nome_original);
			
			if(copy($tmp_name, $diretorio.$nome_arquivo)){
										
				$db = new mysql();
				$db->alterar("downloads", array(
					"imagem"=>"$nome_arquivo"
				), " codigo='$codigo' ");
				
				$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/imagem');
				
			} else {
				
				$this->msg('Não foi possível copiar o arquivo!');
				$this->volta(1);
				
			}
				
		}	 
		
	}

	public function apagar_imagem(){
		
		$codigo = $this->get('codigo');

		if($codigo){

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM downloads where codigo='$codigo' ");
			$data = $exec->fetch_object();

			if($data->imagem){
				unlink('arquivos/downloads/'.$data->imagem);
			}
			
			$db = new mysql();
			$db->alterar("downloads", array(
				"imagem"=>""
			), " codigo='$codigo' ");
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}
	
	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM downloads ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				if($data->imagem){
					unlink('arquivos/downloads/'.$data->imagem);
				}

				$conexao = new mysql();
				$conexao->apagar("downloads", " id='$data->id' ");
					
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
		
	}


}