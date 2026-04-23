<?php

Class model_textos extends model{
	
    public function lista(){
    	
    	$lista = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM texto ORDER BY titulo asc");
		$n = 0;
		while($data = $coisas->fetch_object()){
			
			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['titulo'] = $data->titulo;
			$lista[$n]['nao_remover'] = $data->nao_remover;
			$lista[$n]['url'] = 'conteudo/pag/id/'.$data->url;
			
		$n++;
		}
	  	
		return $lista;
	}

	public function lista_simples(){
    	
    	$lista = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM texto_simples ORDER BY titulo asc");
		$n = 0;
		while($data = $coisas->fetch_object()){
			
			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['titulo'] = $data->titulo;
			$lista[$n]['conteudo'] = $data->conteudo;
			
		$n++;
		}
	  	
		return $lista;
	}

}