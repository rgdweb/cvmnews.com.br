<?php
class blog extends controller {
	
	public function init(){		
	}
	
	public function inicial(){				
		$this->irpara(DOMINIO.'blog/lista/');
	}
	
	public function lista($categoria_interna = null){
		
		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;
		
		//banner
		$banners = new model_banners();
		$dados['banners'] = $banners->lista('149601285477607');

		// categoria
		$dados['categoria_codigo'] = '';
		if($categoria_interna){
			$dados['categoria_codigo'] = $categoria_interna;
		}
		if($this->get('categoria')){
			$dados['categoria_codigo'] = $this->get('categoria');
		}

		//carrega modulo de noticias/bog
		$blog = new model_postagens();
		$blog->perpage = 6;
		//define variaveis
		
		//gets caso for preenchido define a configuraçao
		if($this->get('busca')){ $blog->busca = $this->get('busca'); }
		if($dados['categoria_codigo']){ $blog->categoria = $dados['categoria_codigo']; }
		if($this->get('startitem')){ $blog->startitem = $this->get('startitem'); }
		if($this->get('startpage')){ $blog->startpage = $this->get('startpage'); }
		if($this->get('endpage')){ $blog->endpage = $this->get('endpage'); }
		if($this->get('reven')){ $blog->reven = $this->get('reven'); }
		
	 	//retorno do blog pra variavel
		$blogarray = $blog->lista();
		$dados['noticias'] = $blogarray['noticias'];
		$dados['paginacao'] = $blogarray['paginacao'];
		$dados['numitems'] = $blogarray['numitems'];
		
		//lista categorias para lateral
		$categorias = new model_postagens_grupos();
		$dados['categorias'] = $categorias->lista();		 


		//carrega view e envia dados para a tela
		$this->view('blog', $dados);
	}

	public function leitura(){

		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;

		//banner
		$banners = new model_banners();
		$dados['banners'] = $banners->lista('149601285477607');
		
		// noticia
		$id = $this->get('id');
		
		//Pega dados da noticia
		$db = new mysql();
		$exec = $db->executar("select * from noticia WHERE id='$id' ");
		$dados['data'] = $exec->fetch_object();
		
		//codigo da noticia
		$codigo = $dados['data']->codigo;
		
		//categoria codigo
		$dados['categoria_codigo'] = $dados['data']->categoria;

		//endereco da noticia
		$postagem = new model_postagens();		
		$dados['endereco_noticia'] = DOMINIO."blog/leitura/id/".$dados['data']->id."/noticia/".$postagem->trata_url_titulo($dados['data']->titulo);

		//autor se tiver
		if($dados['data']->autor){

			$conexao = new mysql();
			$coisas_not_autor = $conexao->Executar("SELECT * FROM noticia_autores WHERE codigo='".$dados['data']->autor."' ");
			$dados['data_autor'] = $coisas_not_autor->fetch_object();

			if($dados['data_autor']->nome){
				$dados['autor'] = $dados['data_autor']->nome;
			} else {
				$dados['autor'] = "";
			}
			
		} else {
			$dados['autor'] = "";
		}
		
		//dia
		//$mes = new data();
		//$dados['dia'] = date('d', $dados['data']->data)." ".$mes->mes($dados['data']->data, 2)." ".date('Y', $dados['data']->data);
		$dados['dia'] = date('d/m', $dados['data']->data);
		
		
		//pega imagens
		$imagens = array();
		$conexao = new mysql();
		$coisas_ordem = $conexao->Executar("SELECT * FROM noticia_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
		$data_ordem = $coisas_ordem->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$ii = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas_img = $conexao->Executar("SELECT id, imagem FROM noticia_imagem WHERE id='$value'");
				$data_img = $coisas_img->fetch_object();

				if(isset($data_img->imagem)){
					
					//carrega legenda se tiver
					$conexao = new mysql();
					$coisas_leg = $conexao->Executar("SELECT legenda FROM noticia_imagem_legenda WHERE id_img='$data_img->id'");
					$data_leg = $coisas_leg->fetch_object();
					if(isset($data_leg->legenda)){
						$imagens[$ii]['legenda'] = $data_leg->legenda;
					} else {
						$imagens[$ii]['legenda'] = '';
					}
					
					if($ii == 0){
						$dados['imagem_principal'] = PASTA_CLIENTE."img_postagens_g/".$codigo."/".$data_img->imagem;
					}
					
					$imagens[$ii]['id'] = $data_img->id;
					$imagens[$ii]['imagem_g'] = PASTA_CLIENTE."img_postagens_g/".$codigo."/".$data_img->imagem;
					$imagens[$ii]['imagem_p'] = PASTA_CLIENTE."img_postagens_p/".$codigo."/".$data_img->imagem;
					
					$ii++;
				}

			}
		}
		$dados['imagens'] = $imagens;
		
		//lista categorias para lateral
		$categorias = new model_postagens_grupos();
		$dados['categorias'] = $categorias->lista();
		$dados['categoria_codigo'] = $dados['data']->categoria;
		$dados['categoria'] = $categorias->titulo($dados['data']->categoria);

 		//banner laterais
		$banners = new model_banners();
		$dados['lista_banners_laterais'] = $banners->lista_rand('149601285477607'); 

		//carrega view e envia dados para a tela
		$this->view('blog.leitura', $dados);
	}
	
}