<?php

Class model_cores extends model{
    
    public function lista(){
    	
    	$lista = array();
    	
		$db = new mysql();
		$exec = $db->executar("select * from layout_cor order by id desc");
		while($data = $exec->fetch_object()){
			
			$lista[ $data->id ] = $data->cor;
			
		}
		
		return $lista;
    }

}