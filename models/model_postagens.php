<?php

Class model_postagens extends model{
    
	public $perpage = 100000; //itens por pagina
	public $numlinks = 10; //total de paginas mostradas na paginação
	public $busca = '-';
	public $categoria = 0;
	public $startitem = 0;
	public $startpage = 1;
	public $endpage = '';
	public $reven = 1;
	public $destaque = 0;
	public $ordem = ''; // 'rand' para randomico ou em branco para data desc

    public function lista(){
    	
    	//define variaveis
		$perpage = $this->perpage;
		$numlinks = $this->numlinks;
		$busca = $this->busca;
		$categoria = $this->categoria;
		$startitem = $this->startitem;
		$startpage = $this->startpage;
		$endpage = $this->endpage;
		$reven = $this->reven;
		$destaque = $this->destaque;
		$ordem = $this->ordem;

		//retorno 
		$dados = array();

    	//FILTROS
		$query = "SELECT * FROM noticia ";

		//se tiver busca ignora tudo e faz a busca
		if($busca != "-"){
		    $query = "SELECT * FROM noticia WHERE titulo LIKE '%$busca%' OR previa LIKE '%$busca%' ";
		} else {

			//se selecionou a categoria tem prioridade sobre o destaque
			if($categoria != 0){
				$query = "SELECT * FROM noticia WHERE categoria='$categoria' ";
			} else {

				//destaque mostra todos os itens que estao marcados com destaque
				if($destaque != 0){
					$query = "SELECT * FROM noticia WHERE destaque='$destaque' ";
				}

			}

		}
		
		//faz a busca no banco e retorno numero de itens para paginação
		$conexao = new mysql();
		$coisas_noticias = $conexao->Executar($query);
		if($coisas_noticias->num_rows) {
		  $numitems = $coisas_noticias->num_rows;
		} else {
		  $numitems = 0;
		}
		$dados['numitems'] = $numitems;
		
		
		//calcula paginação
		if($numitems > 0) {
		  $numpages = ceil($numitems / $perpage); 
		  if($startitem + $perpage > $numitems) { $enditem = $numitems; } else { $enditem = $startitem + $perpage; }
		  if(!$startpage) { $startpage = 1; }
		  if(!$endpage) { 
		    if($numpages > $numlinks) { $endpage = $numlinks; } else { $endpage = $numpages; }
		  }
		} else {
		  $numpages = 0;
		}

		$noticias = array();
		$mes = new model_data();

		//ordena e limita aos itens da pagina
		if($ordem == 'rand'){
			$query .= " ORDER BY RAND() LIMIT $startitem, $perpage";
		} else {
			$query .= " ORDER BY data desc LIMIT $startitem, $perpage";
		}

		$conexao = new mysql();
		$coisas_noticias = $conexao->Executar($query);
		$n = 0;
		while($data_noticias = $coisas_noticias->fetch_object()){

			//seta imagem como não existente
			$imagem = "";

			//confere se tem imagem ordenada
			$conexao = new mysql();
			$coisas_ordem = $conexao->Executar("SELECT * FROM noticia_imagem_ordem WHERE codigo='$data_noticias->codigo' ORDER BY id desc limit 1");
			$data_ordem = $coisas_ordem->fetch_object();
			
			//se tiver ordem segue o baile
			if(isset($data_ordem->data)){

				$order = explode(',', $data_ordem->data);

				$ii = 0;
				foreach($order as $key => $value){

					$conexao = new mysql();
					$coisas_img = $conexao->Executar("SELECT imagem FROM noticia_imagem WHERE id='$value'");
					$data_img = $coisas_img->fetch_object();
					
					//pega primeira imagem da ordem e coloca na variavel
					if( ($ii == 0) AND (isset($data_img->imagem)) ){
						
						$imagem = PASTA_CLIENTE."img_postagens_g/".$data_noticias->codigo."/".$data_img->imagem;
						
					$ii++;
					}
				}
			}

			$noticias[$n]['imagem'] = $imagem;
			
			//verifica nome do grupo
			$conexao = new mysql();
			$coisas_noticias_cat = $conexao->Executar("SELECT titulo FROM noticia_grupo WHERE codigo='$data_noticias->categoria'");
			$data_noticias_cat = $coisas_noticias_cat->fetch_object();

			$noticias[$n]['categoria'] = $data_noticias_cat->titulo;
			$noticias[$n]['categoria_codigo'] = $data_noticias->categoria;

			//restante
			$noticias[$n]['id'] = $data_noticias->id;
			$noticias[$n]['codigo'] = $data_noticias->codigo;
			$noticias[$n]['titulo'] = $data_noticias->titulo;
			$noticias[$n]['previa'] = $data_noticias->previa;
			$noticias[$n]['conteudo'] = $data_noticias->conteudo; 
			$noticias[$n]['data'] = date('d', $data_noticias->data)." de ".$mes->mes($data_noticias->data, 2)." de ".date('Y', $data_noticias->data);
			$noticias[$n]['data_cod'] = $data_noticias->data;

			$noticias[$n]['endereco'] = DOMINIO."blog/leitura/id/".$data_noticias->id."/noticia/".$this->trata_url_titulo($data_noticias->titulo);

		$n++;
		}
		$dados['noticias'] = $noticias;

		//lista paginação
		$paginacao = "<ul>";

		if($numpages > 1) { 
			if($startpage > 1) {
				$prevstartpage = $startpage - $numlinks;
				$prevstartitem = $prevstartpage - 1;
				$prevendpage = $startpage - 1;

				$link = DOMINIO."blog/lista/categoria/$categoria/busca/$busca/";
				$link .= "startitem/$prevstartitem/startpage/$prevstartpage/endpage/$prevendpage/reven/$prevstartpage/";

            }

			for($n = $startpage; $n <= $endpage; $n++) {

				$nextstartitem = ($n - 1) * $perpage;

				if($n != $reven) {

					$link = DOMINIO."blog/lista/categoria/$categoria/busca/$busca/";
					$link .= "startitem/$nextstartitem/startpage/$startpage/endpage/$endpage/reven/$n/";
					$paginacao .= "<li><a href='$link' >&nbsp;$n&nbsp;</a></li>";

				} else {
					$paginacao .= "<li><a href='#' class='active' >&nbsp;$n&nbsp;</a></li>";
				}
			}

			if($endpage < $numpages) {

				$nextstartpage = $endpage + 1;

				if(($endpage + $numlinks) < $numpages) { 
					$nextendpage = $endpage + $numlinks; 
				} else {
					$nextendpage = $numpages;
				}

				$nextstartitem = ($n - 1) * $perpage;

				$link = DOMINIO."blog/lista/categoria/$categoria/busca/$busca/";
				$link .= "startitem/$nextstartitem/startpage/$nextstartpage/endpage/$nextendpage/reven/$nextstartpage/";

			}
		}
		$paginacao .= "</ul>";

		$dados['paginacao'] = $paginacao;

		//retorna para a pagina a array com todos as informações
		return $dados;
	}

	//trata nome para url
	public function trata_url_titulo($titulo){

		//remove acentos
		$titulo_tratado = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"), $titulo);

		//remove caracteres indesejados
		$titulo_tratado = str_replace(array("?", ",", ".", "+", "'", "/", ")", "(", "&", "%", "#", "@", "!", "=", ">", "<", ";", ":", "|", "*", "$"), "", $titulo_tratado);
		//coloca ifen para separar palavras
		$titulo_tratado = str_replace(array(" ", "_", "+"), "-", $titulo_tratado);
		//certifica que não tem ifens repetidos
		$titulo_tratado = preg_replace('/(.)\1+/', '$1', $titulo_tratado);		 

		return $titulo_tratado;
	}


}