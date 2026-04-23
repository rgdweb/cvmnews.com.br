<?php

Class model_redes_sociais extends model{
    
    public function codigo($codigo){
    	
		$db = new mysql();
		$exec = $db->executar("select * from rede_social WHERE codigo='$codigo' ");
		$data = $exec->fetch_object();
		
		$return = array();
		
		if(isset($data->endereco)){
			$return['endereco'] = $data->endereco;
		} else {
			$return['endereco'] = '';
		}

		if(isset($data->imagem)){
			$return['imagem'] = PASTA_CLIENTE.'img_redes_sociais/'.$data->imagem;
		} else {
			$return['imagem'] = '';
		}
		
		return $return;
    }

    public function lista(){
		
    	$lista = array();

		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM rede_social_ordem ORDER BY id desc limit 1");		
		$data_ordem = $exec->fetch_object();

		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM rede_social WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->imagem)){
					
					$lista[$n]['imagem'] = PASTA_CLIENTE.'img_redes_sociais/'.$data->imagem;

					if($data->endereco){
						$lista[$n]['endereco'] = $data->endereco;
					} else {
						$lista[$n]['endereco'] = '';
					}

				$n++;
				}
				
			}
		}
		
		//print_r($lista); exit;
		return $lista;
	}



}