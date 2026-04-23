<?php

class produtos extends controller {
	
	protected $_modulo_nome = "Produtos";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(61);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$grupo = $this->get('grupo');
		$dados['grupo'] = $grupo;
		
		$produtos = new model_produtos();		
		$dados['lista_grupos'] = $produtos->lista_grupos($grupo);
		
		//echo "<pre>"; print_r($dados['lista_grupos']); echo "</pre>"; exit;

		if(!$grupo){
			if(isset($dados['lista_grupos'][0]['codigo'])){
				$grupo = $dados['lista_grupos'][0]['codigo'];
			} else {
				$grupo = false;
			}
		}
		
		$dados['lista'] = $produtos->lista($grupo);
		
		$this->view('produtos', $dados);
	}

	public function ordem(){

		$list = $this->post('list');
		$grupo = $this->post('grupo');

		$output = array();
		parse_str($list, $output);
		$ordem = implode(',', $output['item']);

		$db = new mysql();
		$db->inserir("produtos_ordem", array(
			"grupo"=>"$grupo",
			"data"=>"$ordem"
		));

	}

	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";
 		
 		$dados['aba_selecionada'] = "dados";
 		
 		$grupo = $this->get('grupo');
 		$dados['grupo'] = $grupo;
 		
 		//categorias
 		$categorias = new model_produtos();
		$dados['lista_grupos'] = $categorias->lista_grupos($grupo);
		
 		$this->view('produtos.novo', $dados);
	}

	public function novo_grv(){
		
		$titulo = $this->post('titulo');
		$conteudo = $_POST['conteudo'];
		$grupo = $this->post('grupo');
		
		$this->valida($titulo);
		$this->valida($grupo);

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("produtos", array(
			"codigo"=>"$codigo",
			"grupo"=>"$grupo",
			"titulo"=>"$titulo",
			"conteudo"=>"$conteudo"
		));

	 	$ultid = $db->ultimo_id();

	 	//ordem
	 	$db = new mysql();
	 	$exec = $db->executar("SELECT * FROM produtos_ordem where grupo='$grupo' order by id desc limit 1");
	 	$data = $exec->fetch_object();
	 	
	 	if(isset($data->data)){
	 		$novaordem = $data->data.",".$ultid;
	 	} else {
	 		$novaordem = $ultid;
	 	}

	 	$db = new mysql();
	 	$db->inserir("produtos_ordem", array(
	 		"grupo"=>"$grupo",
	 		"data"=>"$novaordem"
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
 		$exec = $db->Executar("SELECT * FROM produtos where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
		
 		//imagens
 		$conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM produtos_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $imagens = array();
        if(isset($data_ordem->data)){

        	$order = explode(',', $data_ordem->data); 

        	foreach($order as $key => $value){
                	
                $conexao = new mysql();
                $coisas_img = $conexao->Executar("SELECT * FROM produtos_imagem WHERE id='$value'");
                $data_img = $coisas_img->fetch_object();                                

                if(isset($data_img->imagem)){

                	$conexao = new mysql();
	                $coisas_leg = $conexao->Executar("SELECT * FROM produtos_imagem_legenda WHERE id_img='$value' ");
	                $data_leg = $coisas_leg->fetch_object();
	                
	                if(isset($data_leg->legenda)){
	                	$imagens[$n]['legenda'] = $data_leg->legenda;
	                } else {
	                	$imagens[$n]['legenda'] = "";
	                }

                	$imagens[$n]['id'] = $data_img->id;
               		$imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_produtos_p/'.$codigo.'/'.$data_img->imagem;
               		$imagens[$n]['imagem_g'] = PASTA_CLIENTE.'img_produtos_g/'.$codigo.'/'.$data_img->imagem;
               		
                $n++;
                }
            }
        }
        $dados['imagens'] = $imagens;       

		$this->view('produtos.alterar', $dados);
	}

	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$titulo = $this->post('titulo');
		$conteudo = $_POST['conteudo']; 
		
		$this->valida($titulo);

		$db = new mysql();
		$db->alterar("produtos", array(
			"titulo"=>"$titulo",
			"conteudo"=>"$conteudo"
		), " codigo='$codigo' ");
	 	

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}

	public function apagar_imagem(){
		
		$codigo = $this->get('codigo');
		$id = $this->get('id');

		if($id){

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM produtos_imagem where id='$id' ");
			$data = $exec->fetch_object();

			if($data->imagem){
				unlink('arquivos/img_produtos_g/'.$codigo.'/'.$data->imagem);
				unlink('arquivos/img_produtos_p/'.$codigo.'/'.$data->imagem);
			}

			$conexao = new mysql();
			$conexao->apagar("produtos_imagem", " id='$id' ");
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}

	public function ordenar_imagem(){
		
		$codigo = $this->post('codigo');
		$list = $this->post('list');
		
		$this->valida($codigo);
		$this->valida($list);

		$output = array();
		parse_str($list, $output);
		$ordem = implode(',', $output['item']);

		$db = new mysql();
		$db->inserir("produtos_imagem_ordem", array(
			"codigo"=>"$codigo",
			"data"=>"$ordem"
		));

	}

	public function legenda(){
		
		$dados['_base'] = $this->base_layout();

		$id = $this->get('id');
		$codigo = $this->get('codigo');

		$dados['codigo'] = $codigo;
		$dados['id'] = $id;

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM produtos_imagem_legenda where id_img='$id' ");
		$data = $exec->fetch_object();

		if(isset($data->id)){
			$dados['legenda'] = $data->legenda;
		} else {
			$dados['legenda'] = '';
		}

		$this->view('produtos.legenda', $dados);
	}

	public function legenda_grv(){
		
		$id = $this->post('id');
		$legenda = $this->post('legenda');
		$codigo = $this->post('codigo');

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM produtos_imagem_legenda where id_img='$id' ");
		$data = $exec->fetch_object();

		if(isset($data->id)){
			$db = new mysql();
			$db->alterar("produtos_imagem_legenda", array(
				"legenda"=>"$legenda"
			), " id='$data->id' ");
		} else {
			$db = new mysql();
			$db->inserir("produtos_imagem_legenda", array(
				"id_img"=>"$id",
				"legenda"=>"$legenda"
			));
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}

	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM produtos ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){
			 	
				$db = new mysql();
				$exec_img = $db->executar("SELECT * FROM produtos_imagem where codigo='$data->codigo' ");
				while($data_img = $exec_img->fetch_object()){

					if($data_img->imagem){
						unlink('arquivos/img_produtos_g/'.$data->codigo.'/'.$data_img->imagem);
						unlink('arquivos/img_produtos_p/'.$data->codigo.'/'.$data_img->imagem);
					}
					
				}
				
				$grupo = $data->grupo;
				
				$conexao = new mysql();
				$conexao->apagar("produtos_imagem", " codigo='$data->codigo' ");

				$conexao = new mysql();
				$conexao->apagar("produtos", " codigo='$data->codigo' ");
				
			}
		}
		
		$this->irpara(DOMINIO.$this->_controller.'/inicial/grupo/'.$grupo);
		
	}

	public function grupos(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "Grupos";
		
		$produtos = new model_produtos();
		$dados['lista_grupos'] = $produtos->lista_grupos(); 

		$this->view('produtos.grupos', $dados);
	}

	public function novo_grupo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo Grupo";

		$this->view('produtos.grupos.novo', $dados);
	}

	public function novo_grupo_grv(){
		
		$titulo = $this->post('titulo');		
		$this->valida($titulo);
		
		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("produtos_grupos", array(
			"codigo"=>"$codigo",
			"titulo"=>"$titulo"
		));

		$ultid = $db->ultimo_id();

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM produtos_grupos_ordem order by id desc limit 1");
		$data = $coisas->fetch_object();

		if(isset($data->data)){
			$novaordem = $data->data.",".$ultid;
		} else {
			$novaordem = $ultid;
		}

		$db = new mysql();
		$db->inserir("produtos_grupos_ordem", array(
			"id_pai"=>"0",
			"data"=>"$novaordem"
		));

		$this->irpara(DOMINIO.$this->_controller.'/alterar_grupo/codigo/'.$codigo.'/aba/imagem');		
	}

	public function alterar_grupo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar Grupo";

 		$aba = $this->get('aba');
 		if($aba){
 			$dados['aba_selecionada'] = $aba;
 		} else {
 			$dados['aba_selecionada'] = 'dados';
 		}

 		$codigo = $this->get('codigo');

 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM produtos_grupos WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();

		if(!isset($dados['data']) ) {
			$this->irpara(DOMINIO.$this->_controller.'/grupos');
		}

		$this->view('produtos.grupos.alterar', $dados);

	}	

	public function alterar_grupo_grv(){
		
		$codigo = $this->post('codigo');
		$titulo = $this->post('titulo');
		
		$this->valida($codigo);
		$this->valida($titulo);

		$db = new mysql();
		$db->alterar("produtos_grupos", array(
			"titulo"=>"$titulo"
		), " codigo='$codigo' ");
	 	
		$this->irpara(DOMINIO.$this->_controller.'/grupos');		
	}

	public function alterar_grupo_imagem(){
		
		$arquivo_original = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		$arquivo = new model_arquivos_imagens();

		$codigo = $this->get('codigo');

		$diretorio = "arquivos/img_produtos_grupos/";

		if(!$arquivo->filtro($arquivo_original)){ $this->msg('Arquivo com formato inválido ou inexistente!'); $this->volta(1); } else {

			//pega a exteção
			$nome_original = $arquivo_original['name'];
			$extensao = $arquivo->extensao($nome_original);
			$nome_arquivo = $arquivo->trata_nome($nome_original);
			
			if(copy($tmp_name, $diretorio.$nome_arquivo)){

				if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
					
					// foto grande
					$largura_g = 800;
					$altura_g = $arquivo->calcula_altura_jpg($diretorio.$nome_arquivo, $largura_g);
					
					//redimenciona
					$arquivo->jpg($diretorio.$nome_arquivo, $largura_g , $altura_g, $diretorio.$nome_arquivo);
					
				}

				$db = new mysql();
				$db->alterar("produtos_grupos", array(
					"imagem"=>"$nome_arquivo"
				), " codigo='$codigo' ");
				
				$this->irpara(DOMINIO.$this->_controller.'/alterar_grupo/codigo/'.$codigo.'/aba/imagem');
				
			} else {
				
				$this->msg('Erro ao gravar imagem!');
				$this->irpara(DOMINIO.$this->_controller."/alterar_grupo/codigo/".$codigo."/aba/imagem");
				
			}

		}
		
	}

	public function apagar_imagem_grupo(){
		
		$codigo = $this->get('codigo');
		
		if($codigo){

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM produtos_grupos where codigo='$codigo' ");
			$data = $exec->fetch_object();

			if($data->imagem){
				unlink('arquivos/img_produtos_grupos/'.$data->imagem);
			}

			$db = new mysql();
			$db->alterar("produtos_grupos", array(
				"imagem"=>""
			), " codigo='$codigo' ");
		}

		$this->irpara(DOMINIO.$this->_controller.'/alterar_grupo/codigo/'.$codigo.'/aba/imagem');
	}

	public function apagar_grupo(){
		
		$codigo = $this->get('codigo');

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM produtos_grupos where codigo='$codigo' ");

		if($exec->num_rows == 1){

			$data = $exec->fetch_object();
			
			if($data->imagem){
				unlink('arquivos/img_produtos_grupos/'.$data->imagem);
			}

			$conexao = new mysql();
			$conexao->apagar("produtos_grupos", " id='$data->id' ");
			
		}

		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}

	public function salvar_ordem_grupos(){

		$ordem = stripcslashes($_POST['ordem']);		 

		if($ordem){

			$json = json_decode($ordem, true);
			
			function converte_array_para_banco($jsonArray, $id_pai = 0) {

				$lista = "";

				foreach ($jsonArray as $subArray) {

					$lista .= $subArray['id'].",";

					if (isset($subArray['children'])) {
				  		converte_array_para_banco($subArray['children'], $subArray['id']);
					} else {
						$pai_remover = $subArray['id'];
						$db = new mysql();
						$db->apagar("produtos_grupos_ordem", " id_pai='$pai_remover' ");
					}

			  	}

			  	$novaordem = substr($lista,0,-1);

			  	$db = new mysql();
				$db->inserir("produtos_grupos_ordem", array(
					"id_pai"=>"$id_pai",
					"data"=>"$novaordem"
				));

			}
			converte_array_para_banco($json);
			
			$this->irpara(DOMINIO.$this->_controller.'/grupos');
			
		} else {
			$this->msg('Ocorreu um erro ao carregar ordem!');
			$this->volta(1);
		}
	}

	public function upload(){
		
		//carrega normal
		$dados['_base'] = $this->base_layout();

 		$codigo = $this->get('codigo');
 		$dados['codigo'] = $codigo;
 		
		$this->view('enviar_imagens', $dados);
	}
	

	public function imagem_redimencionada(){

		$codigo = $this->post('codigo');
		
		//pasta onde vai ser salvo os arquivos
		$pasta = "produtos";
		$diretorio_g = "arquivos/img_".$pasta."_g/".$codigo."/";
		$diretorio_p = "arquivos/img_".$pasta."_p/".$codigo."/";

		//confere e cria pasta se necessario
		if(!is_dir($diretorio_g)) {
			mkdir($diretorio_g);
		}
		if(!is_dir($diretorio_p)) {
			mkdir($diretorio_p);
		}
		
		//carrega model de gestao de imagens
		$img = new model_arquivos_imagens();
		
		// Recuperando imagem em base64
		// Exemplo: data:image/png;base64,AAAFBfj42Pj4
		$imagem = $_POST['imagem'];
		$nome_original = $this->post('nomeimagem');

		$nome_foto  = $img->trata_nome($nome_original);
		$extensao = $img->extensao($nome_original);
		
		// Separando tipo dos dados da imagem
		// $tipo: data:image/png
		// $dados: base64,AAAFBfj42Pj4
		list($tipo, $dados) = explode(';', $imagem);
		
		// Isolando apenas o tipo da imagem
		// $tipo: image/png
		list(, $tipo) = explode(':', $tipo);
		
		// Isolando apenas os dados da imagem
		// $dados: AAAFBfj42Pj4
		list(, $dados) = explode(',', $dados);

		// Convertendo base64 para imagem
		$dados = base64_decode($dados);

		// Gerando nome aleatório para a imagem
		$nome = md5(uniqid(time()));

		// Salvando imagem em disco
		if(file_put_contents($diretorio_g.$nome_foto, $dados)) {			
				
				//confere e se jpg reduz a miniatura
				if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
					
					copy($diretorio_g.$nome_foto, $diretorio_p.$nome_foto);
					
					// foto grande
					$largura_g = 1200;
					$altura_g = $img->calcula_altura_jpg($tmp_name, $largura_g);
					// foto minuatura
					$largura_p = 300;
					$altura_p = $img->calcula_altura_jpg($tmp_name, $largura_p);
					//redimenciona
					$img->jpg($diretorio_g.$nome_foto, $largura_g , $altura_g , $diretorio_g.$nome_foto);
					$img->jpg($diretorio_p.$nome_foto, $largura_p , $altura_p , $diretorio_p.$nome_foto);
					
				} else {
					
					//caso nao possa redimencionar copia a imagem original para a pasta de miniaturas
					copy($diretorio_g.$nome_foto, $diretorio_p.$nome_foto);
					
				} 

				//grava banco
				$db = new mysql();
				$db->inserir("produtos_imagem", array(
					"codigo"=>"$codigo",
					"imagem"=>"$nome_foto"
				));	
				$ultid = $db->ultimo_id();

				//ordem
				$db = new mysql();
				$exec = $db->executar("SELECT * FROM produtos_imagem_ordem where codigo='$codigo' order by id desc limit 1");
				$data = $exec->fetch_object();
				
				if(isset($data->data)){
					$novaordem = $data->data.",".$ultid;
				} else {
					$novaordem = $ultid;
				}
				
				$db = new mysql();
				$db->inserir("produtos_imagem_ordem", array(
					"codigo"=>"$codigo",
					"data"=>"$novaordem"
				));
				
		}

	}

	public function imagem_manual(){

		$arquivo = $_FILES['arquivo'];
		$tmp_name = $_FILES['arquivo']['tmp_name'];

		$codigo = $this->get('codigo');		

		$nome_original = $_FILES['arquivo']['name'];

		//definições de pasta
		$pasta = "produtos";
		$diretorio_g = "arquivos/img_".$pasta."_g/".$codigo."/";
		$diretorio_p = "arquivos/img_".$pasta."_p/".$codigo."/";
		
		if(!is_dir($diretorio_g)) {
			mkdir($diretorio_g);
		}
		if(!is_dir($diretorio_p)) {
			mkdir($diretorio_p);
		}
		
		$img = new model_arquivos_imagens();
 		
		if($tmp_name) {
	 		
			$nome_foto  = $img->trata_nome($nome_original);
			$extensao = $img->extensao($nome_original);
			
			if(copy($tmp_name, $diretorio_g.$nome_foto)){
				
				//confere e se jpg reduz a miniatura
				if( ($extensao == "jpg") OR ($extensao == "jpeg") OR ($extensao == "JPG") OR ($extensao == "JPEG") ){
					
					copy($diretorio_g.$nome_foto, $diretorio_p.$nome_foto);
					
					// foto grande
					$largura_g = 1200;
					$altura_g = $img->calcula_altura_jpg($tmp_name, $largura_g);
					// foto minuatura
					$largura_p = 300;
					$altura_p = $img->calcula_altura_jpg($tmp_name, $largura_p);
					//redimenciona
					$img->jpg($diretorio_g.$nome_foto, $largura_g , $altura_g , $diretorio_g.$nome_foto);
					$img->jpg($diretorio_p.$nome_foto, $largura_p , $altura_p , $diretorio_p.$nome_foto);
					
				} else {
					
					//caso nao possa redimencionar copia a imagem original para a pasta de miniaturas
					copy($diretorio_g.$nome_foto, $diretorio_p.$nome_foto);
					
				} 
				
				//grava banco
				$db = new mysql();
				$db->inserir("produtos_imagem", array(
					"codigo"=>"$codigo",
					"imagem"=>"$nome_foto"
				));
				$ultid = $db->ultimo_id();
				
				//ordem
				$db = new mysql();
				$exec = $db->executar("SELECT * FROM produtos_imagem_ordem where codigo='$codigo' order by id desc limit 1");
				$data = $exec->fetch_object();
				
				if(isset($data->data)){
					$novaordem = $data->data.",".$ultid;
				} else {
					$novaordem = $ultid;
				}
				
				$db = new mysql();
				$db->inserir("produtos_imagem_ordem", array(
					"codigo"=>"$codigo",
					"data"=>"$novaordem"
				));

			} else {
				$this->msg('Erro ao gravar imagem!');				
			}
			
			$this->irpara(DOMINIO.$this->_controller."/alterar/codigo/".$codigo."/aba/imagem");
		}
		
	}

//termina classe
}