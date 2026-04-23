<?php

Class model_downloads extends model{
    
    public function lista(){
 		
		$retorno = array();
 		
		$db = new mysql();
		$exec = $db->executar("select * from downloads order by rand()");		
		$n = 0;
		while($data = $exec->fetch_object()){
			
			$retorno[$n]['titulo'] = $data->titulo;
			$retorno[$n]['endereco'] = $data->endereco;
			$retorno[$n]['imagem'] = PASTA_CLIENTE."downloads/".$data->imagem;
			
		$n++;
		}

		return $retorno;
    }

}