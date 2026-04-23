<?php

Class model_programacao extends model{
	
    public function lista($semana){
    	
    	$lista = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM programacao WHERE dia='$semana' ORDER BY inicio asc");
		$n = 0;
		while($data = $coisas->fetch_object()){
			
			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['inicio'] = date('H:i', $data->inicio);
			$lista[$n]['programa'] = $data->programa;

		$n++;
		}
	  	
		return $lista;
	}

}