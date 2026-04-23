<?php

Class model_postagens_grupos extends model{	 

    public function lista(){
    	
    	//lista categorias para lateral
		$categorias = array();
		$conexao = new mysql();
		$coisas_categorias = $conexao->Executar("SELECT * FROM noticia_grupo order by titulo asc");
		$n = 0;
		while($data_categorias = $coisas_categorias->fetch_object()){ 
			
			$categorias[$n]['codigo'] = $data_categorias->codigo;			 
			$categorias[$n]['titulo'] = $data_categorias->titulo; 
		
		$n++;
		}		
		
		//retorna para a pagina a array com todos as informações
		return $categorias;
	}

	public function titulo($codigo){
    	
		$conexao = new mysql();
		$coisas_categorias = $conexao->Executar("SELECT titulo FROM noticia_grupo where codigo='$codigo' ");
		$data_categorias = $coisas_categorias->fetch_object();
		
		return $data_categorias->titulo;
	}

}