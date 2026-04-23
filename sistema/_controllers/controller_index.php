<?php

class index extends controller {
	
	public function init(){
		
		$this->autenticacao();
		
		foreach ($this->lista_menu() as $key => $value) {

			$endereco = $value['endereco'];
			$this->irpara(DOMINIO.$endereco);
			exit;

		}
	}
	
}