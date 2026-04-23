<?php

Class model_programacao extends model{

	public function atual(){

		$time = date('H:i');
		$hora_montada = "1984-08-22 ".$time.":00";
		//$hora_montada = "1984-08-22 23:16"; // simulacao

		$data_final = strtotime($hora_montada);

		$dia = date('w');
 		
		$db = new mysql();
		$exec = $db->executar("select * from programacao WHERE dia='$dia' AND inicio<='$data_final' order by inicio desc limit 1");

		$retorno = array();

		if($exec->num_rows == 1){

			$data = $exec->fetch_object();

			$retorno['programa'] = $data->programa;
			$retorno['apresentador'] = $data->apresentador;
			$retorno['descricao'] = $data->descricao;

		} else {
						
			if($dia == 0){
				$dia = 6;
			} else {
				$dia = $dia-1;
			}
			
			$db = new mysql();
			$exec = $db->executar("select * from programacao WHERE dia='$dia' order by inicio desc limit 1");
			if($exec->num_rows == 1){
				
				$data = $exec->fetch_object();
				
				$retorno['programa'] = $data->programa;
				$retorno['apresentador'] = $data->apresentador;
				$retorno['descricao'] = $data->descricao;

			} else {
				
				$retorno['programa'] = "";
				$retorno['apresentador'] = "";
				$retorno['descricao'] = "";

			}
		}
		
		return $retorno;
	}

	public function proximo(){
		
		$time = date('h:i');
		$hora_montada = "1984-08-22 ".$time.":00";
		$data_final = strtotime($hora_montada);
		
		$dia = date('w');
		
		$db = new mysql();
		$exec = $db->executar("select * from programacao WHERE dia='$dia' AND inicio<='$data_final' order by inicio asc limit 2");
		
		$retorno = array();
		
		$n = 0; 
		while($data = $exec->fetch_object()){

			if($n == 0){
				$hora_inicio_p1 = date('H', $data->inicio);
				$n++;
			}

			if($n == 1){
				
				$hora_inicio_p2 = date('H', $data->inicio);

				if( ($hora_inicio_p1 > 12) AND ($hora_inicio_p2 < 12) ){
					// significa que é divisão de dia e tem que ser outro calculo
				} else {

					$retorno['programa'] = $data->programa;
					$retorno['apresentador'] = $data->apresentador;
					$retorno['descricao'] = $data->descricao;

					$n++;
				}

			}

		}

		if($n <= 1){
			
			$dia = $dia+1;
			
			if( $dia < 0){
				$dia = 6;
			}
			
			$db = new mysql();
			$exec = $db->executar("select * from programacao WHERE dia='$dia' order by inicio desc limit 2");
			if($exec->num_rows == 2){
				
				$n = 0;
				while($data = $exec->fetch_object()){

					if($n == 1){

						$retorno['programa'] = $data->programa;
						$retorno['apresentador'] = $data->apresentador;
						$retorno['descricao'] = $data->descricao;
					}

					$n++;
				}
			}
		}

		return $retorno;
	}

	public function lista_dia($dia){

		$time = date('h:i');
		$hora_montada = "1984-08-22 ".$time.":00";
		$data_final = strtotime($hora_montada);
		
		$db = new mysql();
		$exec = $db->executar("select * from programacao WHERE dia='$dia' order by inicio asc");

		$retorno = array();
		$n = 0;

		while($data = $exec->fetch_object()){
			
			$retorno[$n]['inicio'] = date('H:i', $data->inicio);
			$retorno[$n]['titulo'] = $data->programa;
			$retorno[$n]['apresentador'] = $data->apresentador;
			
			$n++;
		}

		return $retorno;
	}

}