<?php

class webradio extends controller {
	
	protected $_modulo_nome = "Webradio";
	
	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(72);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM webradio where id='1' ");
		$dados['data'] = $exec->fetch_object();

		if($this->get('aba')){
			$dados['aba_selecionada'] = $this->get('aba');
		} else {
			$dados['aba_selecionada'] = 'geral';
		}

		$this->view('webradio', $dados);
	}
	
	public function geral_grv(){
		
		$ip = $this->post_htm('ip');
		
		$this->valida($ip);
		
		$db = new mysql();
		$db->alterar("webradio", array(
			"ip"=>"$ip"
		), " id='1' ");

		
		
		$porta = $this->post_htm('porta');
		
		$this->valida($porta);
		
		$db = new mysql();
		$db->alterar("webradio", array(
			"porta"=>"$porta"
		), " id='1' ");

		$whatsapp = $this->post_htm('whatsapp');
		
		$this->valida($whatsapp);
		
		$db = new mysql();
		$db->alterar("webradio", array(
			"whatsapp"=>"$whatsapp"
		), " id='1' ");
		
		$this->irpara(DOMINIO.$this->_controller.'/inicial/aba/geral');
	}
	
}