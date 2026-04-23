<?php
class conteudo extends controller {
	
	public function init(){
	}
	
	public function pag(){

		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;
		
		//banner
		$banners = new model_banners();
		$dados['banners'] = $banners->lista('149601285477607');
		
		$codigo_pag = $this->get('id');
		
		//textos
		$texto = new model_texto();
		$dados['pagina'] = $texto->conteudo_url($codigo_pag);
		
		//carrega view e envia dados para a tela
		$this->view('conteudo', $dados);
	}
	
}