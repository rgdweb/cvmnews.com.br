<?php

Class model_ordena_permissoes {

	public $_lista_n = 0;
	public $_lista_certa = array();
	
	public function monta( $id_pai, $arrayCats){
		
		foreach ($arrayCats as $key => $value) {
			
			if ( $value['id_pai'] == $id_pai ){
				
				$this->_lista_certa[$this->_lista_n]['id_pai'] = $value['id_pai'];
				$this->_lista_certa[$this->_lista_n]['id'] = $value['id'];
				$this->_lista_certa[$this->_lista_n]['titulo'] = $value['titulo'];
				$this->_lista_certa[$this->_lista_n]['check'] = $value['check'];
				$this->_lista_n++;
				
				$this->monta( $value['id'], $arrayCats);
			}
		}
	}
	
}