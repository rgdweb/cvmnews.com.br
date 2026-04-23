<?php
class programacao extends controller {
	
	public function init(){
		
	}
	
	public function inicial(){
		
		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;
		
		//banner
		$banners = new model_banners();
		$dados['banners'] = $banners->lista('149601285477607');

		$programacao = new model_programacao();
		$dados['proximo'] = $programacao->proximo();
		$dados['programacao'] = $programacao->atual();
		
		$dia = $this->get('d');
		if(!$dia){
			$dia = date('w')+1;
		}
		$dados['dia'] = $dia;
 		$dados['lista_dia'] = $programacao->lista_dia($dia-1);
 		

		//carrega view e envia dados para a tela
		$this->view('programacao', $dados);
	}
	
	public function listaini(){
		
		$dados = array();
		$dados['_base'] = $this->base();

		$programacao = new model_programacao();
		$dados['proximo'] = $programacao->proximo();
		$dados['programacao'] = $programacao->atual();
		
		$dia = $this->post('dia');
		if(!$dia){
			$dia = date('w')+1;
		}
		$dados['dia'] = $dia;
 		$dados['lista_dia'] = $programacao->lista_dia($dia-1);
 		
 		$this->view('programacao_ini', $dados);
	}
	 
}