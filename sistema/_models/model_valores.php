<?php

Class model_valores extends model{
	
    public function trata_valor( $valor ){
		$valor = number_format($valor,2,',','.');
		return $valor;
	}
	public function trata_valor_banco( $valor ){
	    $valor = str_replace(".", "", $valor);
	    $valor = str_replace(",", ".", $valor);
	    $valor = $this->trata_valor_calculo($valor);
	    return $valor;
	}
	public function trata_valor_calculo( $valor ){
	    $valor = bcmul($valor, '100', 2);
	    $valor = bcdiv($valor, '100', 2);
	    return $valor;
	}
    
}