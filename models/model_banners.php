<?php
Class model_banners extends model{

    public function lista($grupo){
		
    	$lista = array();

		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM banner_ordem WHERE codigo='$grupo' ORDER BY id desc limit 1");		
		$data_ordem = $exec->fetch_object();

		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM banner WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->imagem)){
					
					$lista[$n]['imagem'] = PASTA_CLIENTE.'img_banners/'.$data->imagem;

					if($data->endereco){
						$lista[$n]['link'] = $data->endereco;
					} else {
						$lista[$n]['link'] = '';
					}

				$n++;
				}
				
			}
		}
		
		return $lista;
	}


	public function lista_rand($grupo){
		
    	$lista = array();

		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM banner_ordem WHERE codigo='$grupo' ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM banner WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->imagem)){
					
					$lista[$n]['imagem'] = PASTA_CLIENTE.'img_banners/'.$data->imagem;
					
					if($data->endereco){
						$lista[$n]['link'] = " onClick=\"window.open('$data->endereco', '_blank');\" ";
					} else {
						$lista[$n]['link'] = '';
					}

				$n++;
				}
				
			}
		}
		
		shuffle($lista);
		return $lista;
	}


}