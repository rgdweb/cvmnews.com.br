<?php

class menu extends controller {

	public function init(){
		$this->autenticacao();
	}
	
	public function altera(){
		
		$db = new mysql();
		$exec = $db->executar("SELECT abre_fecha_menu FROM adm_usuario where codigo='$this->_cod_usuario' ");
		$dada = $exec->fetch_object();

		if($dada->abre_fecha_menu == 0){

			$db = new mysql();
			$exec = $db->alterar("adm_usuario", array(
				'abre_fecha_menu' => '1'			
			), " codigo='$this->_cod_usuario' ");

		} else {

			$db = new mysql();
			$exec = $db->alterar("adm_usuario", array(
				'abre_fecha_menu' => '0'			
			), " codigo='$this->_cod_usuario' ");

		}
		
	}

}