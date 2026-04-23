<?php

Class model_videos extends model{
	
    public function lista(){
    	
		$lista = array();
		
		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM videos_ordem ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM videos WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->codigo)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['video'] = $data->video;
					
				$n++;
				}
			}
		}
	  	
		return $lista;
	}
	
	public function carrega($codigo){
		
		$db = new mysql();
		$exec = $db->executar("select * from videos WHERE codigo='$codigo' ");		
		if($exec->num_rows == 1){
			return $exec->fetch_object();
		} else {
			return false;
		}
    }
    
}