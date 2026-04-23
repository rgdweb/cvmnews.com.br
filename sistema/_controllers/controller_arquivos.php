<?php

class arquivos extends controller {
	
	protected $_modulo_nome = "Arquivos";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(41);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$lista = array();

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM hospedararquivo ");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;
			$lista[$i]['arquivo'] = $data->arquivo;
			
		$i++;
		}
		$dados['lista'] = $lista;
		
		$this->view('arquivos', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo"; 		

		$this->view('arquivos.novo', $dados);
	}

	public function novo_grv(){
		
		$titulo = $this->post('titulo');
		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		$this->valida($titulo);
		
		//// Definicao de Diretorios / 
		$diretorio = "arquivos/arquivos/";
		
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

					$codigo = $this->gera_codigo();
					
					$db = new mysql();
					$db->inserir("hospedararquivo", array(
						"codigo"=>"$codigo",
						"titulo"=>"$titulo",
						"arquivo"=>"$nome_arquivo"
					));
					
					$this->irpara(DOMINIO.$this->_controller);
					
				} else {
					
					$this->msg('Não foi possível copiar o arquivo!');
					$this->volta(1);

				}
				
			}	 
		
	}
	

	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM hospedararquivo ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){

				unlink("arquivos/arquivos/$data->arquivo");
				
				$conexao = new mysql();
				$conexao->apagar("hospedararquivo", " id='$data->id' ");

			}
		}

		$this->irpara(DOMINIO.$this->_controller);		
	}


}