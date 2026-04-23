<?php

Class model_postagens extends model{
	
    public $limit = 1000; //itens por pagina
	public $busca = '-';
	public $categoria = 0;
	public $destaque = 0;
	public $imagens = false;
	public $ordem = ''; // 'rand' para randomico ou em branco para data desc


    public function lista(){
    	
    	//define variaveis
		$limit = $this->limit;
		$busca = $this->busca;
		$categoria = $this->categoria;
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

		$noticias = array();
		$mes = new model_datas();

		//ordena e limita aos itens da pagina
		if($ordem == 'rand'){
			$query .= " ORDER BY RAND() LIMIT $limit";
		} else {
			$query .= " ORDER BY data desc LIMIT $limit";
		}

		$conexao = new mysql();
		$coisas_noticias = $conexao->Executar($query);
		$n = 0;
		while($data_noticias = $coisas_noticias->fetch_object()){

			//verfica se vai listar imagens
			if($this->imagens){

				$imagens = $this->imagens($data_noticias->codigo);

				$noticias[$n]['imagem'] = $imagens['principal'];
				$noticias[$n]['imagens'] = $imagens['lista'];
				
			}

			//verifica nome do grupo
			$conexao = new mysql();
			$coisas_noticias_cat = $conexao->Executar("SELECT titulo FROM noticia_grupo WHERE codigo='$data_noticias->categoria'");
			$data_noticias_cat = $coisas_noticias_cat->fetch_object();

			if(isset($data_noticias_cat->titulo)){
				$noticias[$n]['categoria'] = $data_noticias_cat->titulo;
				$noticias[$n]['categoria_codigo'] = $data_noticias->categoria;
			} else {
				$noticias[$n]['categoria'] = "";
				$noticias[$n]['categoria_codigo'] = 0;
			}

			if($data_noticias->destaque){

				//verifica nome do grupo
				$conexao = new mysql();
				$coisas_noticias_des = $conexao->Executar("SELECT titulo FROM noticia_destaque WHERE codigo='$data_noticias->destaque'");
				$data_noticias_des = $coisas_noticias_des->fetch_object();
				
				$noticias[$n]['destaque'] = $data_noticias_des->titulo;
				$noticias[$n]['destaque_codigo'] = $data_noticias->destaque;
				
			} else {
				$noticias[$n]['destaque'] = "";
				$noticias[$n]['destaque_codigo'] = 0;
			}

			//autor se tiver
			if($data_noticias->autor){

				$conexao = new mysql();
				$coisas_not_autor = $conexao->Executar("SELECT nome FROM noticia_autores WHERE codigo='$data_noticias->autor'");
				$data_not_autor = $coisas_not_autor->fetch_object();

				if($data_not_autor->nome){
					$noticias[$n]['autor'] = $data_not_autor->nome;
				} else {
					$noticias[$n]['autor'] = "";
				}
				
			} else {
				$noticias[$n]['autor'] = "";
			}

			//restante
			$noticias[$n]['id'] = $data_noticias->id;
			$noticias[$n]['codigo'] = $data_noticias->codigo;
			$noticias[$n]['titulo'] = $data_noticias->titulo;
			$noticias[$n]['previa'] = $data_noticias->previa;
			$noticias[$n]['data'] = date('d', $data_noticias->data)." ".$mes->mes($data_noticias->data, 2)." ".date('Y', $data_noticias->data);
			$noticias[$n]['data_cod'] = $data_noticias->data;			 

		$n++;
		}
		$dados['noticias'] = $noticias;

		//retorna para a pagina a array com todos as informações
		return $dados;
	}


	public function imagens($codigo){

		$conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM noticia_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $dados = array();
        $imagens = array();
        if(isset($data_ordem->data)){

        	$order = explode(',', $data_ordem->data); 

        	foreach($order as $key => $value){
                
                $conexao = new mysql();
                $coisas_img = $conexao->Executar("SELECT * FROM noticia_imagem WHERE id='$value'");
                $data_img = $coisas_img->fetch_object();                                

                if(isset($data_img->imagem)){

                	if($n == 0){
                		$dados['principal'] = PASTA_CLIENTE.'img_postagens_g/'.$codigo.'/'.$data_img->imagem;
                	}

                	$conexao = new mysql();
	                $coisas_leg = $conexao->Executar("SELECT * FROM noticia_imagem_legenda WHERE id_img='$value' ");
	                $data_leg = $coisas_leg->fetch_object();
	                
	                if(isset($data_leg->legenda)){
	                	$imagens[$n]['legenda'] = $data_leg->legenda;
	                } else {
	                	$imagens[$n]['legenda'] = "";
	                }

                	$imagens[$n]['id'] = $data_img->id;
               		$imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_postagens_p/'.$codigo.'/'.$data_img->imagem;
               		$imagens[$n]['imagem_g'] = PASTA_CLIENTE.'img_postagens_g/'.$codigo.'/'.$data_img->imagem;

                $n++;
                }
            }
        }
        $dados['lista'] = $imagens;
        return $dados;
	}


	public function grupos($codigo = null){ 

		$lista = array();
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM noticia_grupo order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;

			if($codigo == $data->codigo){
				$lista[$i]['selected'] = "selected";
			} else {
				$lista[$i]['selected'] = "";
			}
			
		$i++;
		}
		return $lista;
	}


	public function autores($codigo = null){ 

		$lista = array();
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM noticia_autores order by nome asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['nome'] = $data->nome;

			if($codigo == $data->codigo){
				$lista[$i]['selected'] = "selected";
			} else {
				$lista[$i]['selected'] = "";
			}
			
		$i++;
		}
		return $lista;

	}


	public function destaques($codigo = null){ 
		
		$lista = array();
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM noticia_destaque order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;
			
			if($codigo == $data->codigo){
				$lista[$i]['selected'] = "selected";
			} else {
				$lista[$i]['selected'] = "";
			}
			
		$i++;
		}
		return $lista;
		
	}


	public function audios($codigo){ 
		
		$lista = array();
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM noticia_audio WHERE codigo='$codigo' order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;
			$lista[$i]['arquivo'] = PASTA_CLIENTE."audios/$codigo/".$data->arquivo;
			
		$i++;
		}
		return $lista;
		
	}

	
}