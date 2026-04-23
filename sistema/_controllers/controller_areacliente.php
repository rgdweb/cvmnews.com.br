<?php

class areacliente extends controller {
	
	protected $_modulo_nome = "Restrito";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(68);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$lista = new model_areacliente();
		$dados['lista'] = $lista->lista();
		
		$this->view('areacliente', $dados);
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

 		//dados
 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM adm_usuario where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		//lista arquivos
		$clientes = new model_areacliente();
		$dados['arquivos'] = $clientes->arquivos($codigo);

		$this->view('areacliente.alterar', $dados);
	}

	public function arquivo(){
		
		$dados['_base'] = $this->base_layout();		

		$dados['codigo'] = $this->get('codigo');

		$usuarios = new model_areacliente();
		$dados['usuarios'] = $usuarios->lista();

		$this->view('areacliente.arquivo', $dados);	
	}

	public function arquivo_grv(){
		
		$titulo = $this->post('titulo');
		$codigo = $this->post('codigo');

		$this->valida($titulo);
		$this->valida($codigo);

		$lista = new model_areacliente();
		$usuarios = $lista->lista();

		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		//carrega model de gestao de imagens
		$arquivo = new model_arquivos_imagens();

		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {
			
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo  = $arquivo->trata_nome($nome_original);
						 
			$time = time();

			foreach ($usuarios as $key => $value) {

				if($this->post('repre_'.$value['id']) == 1){

					$codigo_arquivo = $this->gera_codigo();
					$codigo_usuario = $value['codigo'];

					$diretorio = "arquivos/arquivos_clientes/$codigo_usuario/";
					//confere e cria pasta se necessario
					if(!is_dir($diretorio)) {
						mkdir($diretorio);
					}

					if(copy($tmp_name, $diretorio.$nome_arquivo)){
					
						$db = new mysql();
						$db->inserir("areacliente_arquivos", array(
							"codigo"=>"$codigo_arquivo",
							"data"=>"$time",
							"cliente"=>"$codigo_usuario",
							"titulo"=>"$titulo",
							"arquivo"=>"$nome_arquivo"
						));

					}

				}

			}
			
			$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/arquivos');
		}
		
	}


	public function apagar_arquivos(){
		
		$codigo = $this->get('codigo');

		$this->valida($codigo);

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM areacliente_arquivos where cliente='$codigo' ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
						
				if($data->arquivo){
					unlink("arquivos/arquivos_clientes/$codigo/$data->arquivo");
				}

				$conexao = new mysql();
				$conexao->apagar("areacliente_arquivos", " id='$data->id' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/arquivos');
	}


	public function apagar_cliente(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM areacliente ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				$db = new mysql();
				$exec_arq = $db->Executar("SELECT * FROM areacliente_arquivos where cliente='$data->codigo' ");
				while($data_arq = $exec_arq->fetch_object()){					 
					
					if($data_arq->arquivo){
						unlink("arquivos/arquivos_clientes/$data->codigo/$data_arq->arquivo");
					}

					$conexao = new mysql();
					$conexao->apagar("areacliente_arquivos", " id='$data_arq->id' ");				
				}
				
				$conexao = new mysql();
				$conexao->apagar("areacliente", " id='$data->id' ");
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
	}


	public function downloads(){
		
		$codigo = $this->get('arquivos');
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM areacliente_arquivos where codigo='$codigo' ");
		$data = $exec->fetch_object();
		
		if(isset($data->arquivo)){
			
			$download = 'arquivos/arquivos_clientes/'.$data->cliente.'/'.$data->arquivo;
			if( is_file($download) ){
			    
			    $finfo = finfo_open(FILEINFO_MIME_TYPE);
			    $type = finfo_file($finfo, $download);
			    header('Content-type: '.$type);
			    header('Content-Disposition: attachment; filename="'.$data->arquivo.'"');
			 	
			 	readfile($download);
			 	exit;
			 	
			} else {
				echo "Arquivo não encontrado!";
				exit;
			}
			
		} else {
			echo "Arquivo não encontrado!";
			exit;
		}
		
	}
	
	
}