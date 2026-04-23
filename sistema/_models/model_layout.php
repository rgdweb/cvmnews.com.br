<?php

Class model_layout extends model{
		
    public function lista(){
    	
    	$lista = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM layout_cor order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['titulo'] = $data->titulo;
			$lista[$i]['cor'] = $data->cor;

		$i++;
		}
	  	
		return $lista;
	}

}