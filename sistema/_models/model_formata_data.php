<?php

Class model_formata_data extends model{
	
	public function mes($mes, $tipo = null){
		
		$mes = date('m', $mes);
		
		if($tipo == 1){
			$meses = array ('01' => "JAN", '02' => "FEV", '03' => "MAR", '04' => "ABR", '05' => "MAI", '06' => "JUN", '07' => "JUL", '08' => "AGO", '09' => "SET", '10' => "OUT", '11' => "NOV", '12' => "DEZ");
		} else {
			$meses = array ('01' => "Janeiro", '02' => "Fevereiro", '03' => "Março", '04' => "Abril", '05' => "Maio", '06' => "Junho", '07' => "Julho", '08' => "Agosto", '09' => "Setembro", '10' => "Outubro", '11' => "Novembro", '12' => "Dezembro");
		}

		return $meses[$mes];
	}

	public function mes_numerico($mes, $tipo = null){
		
		if($tipo == 1){
			$meses = array ('01' => "JAN", '02' => "FEV", '03' => "MAR", '04' => "ABR", '05' => "MAI", '06' => "JUN", '07' => "JUL", '08' => "AGO", '09' => "SET", '10' => "OUT", '11' => "NOV", '12' => "DEZ");
		} else {
			$meses = array ('01' => "Janeiro", '02' => "Fevereiro", '03' => "Março", '04' => "Abril", '05' => "Maio", '06' => "Junho", '07' => "Julho", '08' => "Agosto", '09' => "Setembro", '10' => "Outubro", '11' => "Novembro", '12' => "Dezembro");
		}
		
		return $meses[$mes];
	}

	public function semana($semana){
		$diasemana = array('domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado');
		$diasemana_numero = date('w', $semana);
		return $diasemana[$diasemana_numero];
	}


}