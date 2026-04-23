<?php

Class model_equipe extends model{
 	
	public function lista(){
    	
    	$lista = array();

		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM parceiros_ordem ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();

		if(isset($data_ordem->data)){

			$order = explode(',', $data_ordem->data);

			$n = 0;
			foreach($order as $key => $value){

				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM parceiros WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->imagem)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['endereco'] = $data->endereco;
					$lista[$n]['imagem'] = PASTA_CLIENTE.'img_parceiros/'.$data->imagem;
					
				$n++;
				}
			}
		}
	  	
		return $lista;
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function lista_inicial(){
    	
    	$lista = array();
    	$n = 0;
    	
    	$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM parceiros order by RAND() ");
		while($data = $coisas->fetch_object()){
			
			if(isset($data->imagem)){
			
				$lista[$n]['id'] = $data->id;
				$lista[$n]['codigo'] = $data->codigo;
				$lista[$n]['titulo'] = $data->titulo;
				$lista[$n]['endereco'] = $data->endereco;
				$lista[$n]['imagem'] = PASTA_CLIENTE.'img_parceiros/'.$data->imagem;
				
			$n++;
			}
		}
		
		return $lista;
	}

	///////////////////////////////////////////////////////////////////////////
	//


	public function carregar($codigo){
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM parceiros where codigo='$codigo' ");
		return $exec->fetch_object();
    }

	///////////////////////////////////////////////////////////////////////////
	//

	public function ordem(){ 
    	$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM parceiros_ordem ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		if(isset($data_ordem->data)){
			return $data_ordem->data;
		} else {
			return "";
		}
	}
	
}