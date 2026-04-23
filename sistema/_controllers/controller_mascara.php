<?php

class mascara extends controller {
	
	protected $_modulo_nome = "Marca d'água";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(58);
	}

	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";		 

		$lista = array();

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM marcadagua order by titulo asc");
		$i = 0;
		$grupo_titulo = "";
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;

			if($data->posicao == 1){
				$lista[$i]['posicao'] = "Centro";
			}
			if($data->posicao == 2){
				$lista[$i]['posicao'] = "Canto esquerdo superior";
			}
			if($data->posicao == 3){
				$lista[$i]['posicao'] = "Canto direito superior";
			}
			if($data->posicao == 4){
				$lista[$i]['posicao'] = "Canto esquerdo inferior";
			}
			if($data->posicao == 5){
				$lista[$i]['posicao'] = "Canto direito inferior";
			}

			if($data->preencher == 0){
				$lista[$i]['preencher'] = "Não";
			} else {
				$lista[$i]['preencher'] = "Sim";
			}

			$lista[$i]['imagem'] = PASTA_CLIENTE.'img_mascaras/'.$data->imagem;

		$i++;
		}
		$dados['lista'] = $lista;
		
		$this->view('mascara', $dados);
	}
	
	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Nova";


		$this->view('mascara.nova', $dados);
	}

	public function novo_grv(){

		$titulo = $this->post('titulo');
		$posicao = $this->post('posicao');
		$preencher = $this->post('preencher');
		
		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];
		
		$this->valida($titulo);

		//// Definicao de Diretorios / 
		$diretorio = "arquivos/img_mascaras/";		 
		
		//imagem
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
			
				if(!copy($tmp_name, $destino)){
					$this->msg('Não foi possível copiar o arquivo!');
					$this->volta(1);
				}

		}		 

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("marcadagua", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo",
			"imagem"=>"$nome_arquivo",
			"posicao"=>"$posicao",
			"preencher"=>"$preencher"
		));
	 	
		$this->irpara(DOMINIO.$this->_controller);
		
	}
	
	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";

 		$codigo = $this->get('codigo');

 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM marcadagua WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		if(!isset($dados['data']) ) {
			$this->irpara(DOMINIO.$this->_controller);
		}

		$this->view('mascara.alterar', $dados);

	}

	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');
		$posicao = $this->post('posicao');
		$preencher = $this->post('preencher');
		
		$this->valida($codigo);
		$this->valida($titulo);
		
		$db = new mysql();
		$db->alterar("marcadagua", array(
			"titulo"=>"$titulo",
			"posicao"=>"$posicao",
			"preencher"=>"$preencher"
		), " codigo='$codigo' ");
	 	
		$this->irpara(DOMINIO.$this->_controller);		
	}

	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM marcadagua ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
				
				unlink('arquivos/img_mascaras/'.$data->imagem);
				
				$conexao = new mysql();
				$conexao->apagar("marcadagua", " id='$data->id' ");
					
			}
		}
		
		$this->irpara(DOMINIO.$this->_controller);		
	}


}