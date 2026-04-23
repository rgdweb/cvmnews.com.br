<?php

class erro extends controller {
	
	protected $_modulo_nome = "Erro";

	public function init(){ 
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		
		$this->view('erro', $dados);
	}

}