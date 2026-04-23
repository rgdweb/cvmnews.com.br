<?php

class controller extends system {

	public function init(){ //inicialização
	}

	protected function base(){

		$dados = array();
		$dados['libera_views'] = true;
		
		//informações basicas de metan
		$db = new mysql();
		$config = $db->executar("select titulo_pagina, descricao, keywords1, keywords2 from meta where id='1' ")->fetch_object();
		$dados['titulo_pagina'] = $config->titulo_pagina;
		$dados['descricao'] = $config->descricao;

		$dados['keywords1'] = $config->keywords1;
		$dados['keywords2'] = $config->keywords2;
		
		//carrega imagens do setadas no painel de controle
		$db = new mysql();
		$exec = $db->executar("select codigo, imagem from imagem ");
		while($data = $exec->fetch_object()){
			if($data->imagem){
				$dados['imagem'][$data->codigo] = PASTA_CLIENTE.'imagens/'.$data->imagem;
			} else {
				$dados['imagem'][$data->codigo] = '';
			}
		}
		
		// carrega cores do painel
		$cores = new model_cores();
		$dados['cor']  = $cores->lista();
		
		// menus
		$menu = new model_menu();
		$dados['menu'] = $menu->lista();
		$dados['menu_rodape'] = $menu->lista_rodape();

		// textos padroes
		$textos = new model_texto();

		$dados['topo_horarios'] = $textos->conteudo_simples('153685499223700');
		$dados['topo_email'] = $textos->conteudo_simples('153685506965795');
		$dados['topo_ligue'] = $textos->conteudo_simples('153685514555478');
		
		$dados['copy'] = $textos->conteudo_simples('152965044167413');		
		$dados['rodape_email'] = $textos->conteudo_simples('153693350552184');
		$dados['rodape_contato1'] = $textos->conteudo_simples('153693355357720');
		$dados['rodape_contato2'] = $textos->conteudo_simples('153693359814357');
		$dados['rodape_endereco'] =  $textos->conteudo_simples('153693364021998'); 
		
		//rede social
		$redessociais = new model_redes_sociais();
		$dados['listaredes'] = $redessociais->lista();
		
		$db = new mysql();
		$exec = $db->executar("select * FROM webradio WHERE id='1' ");
		$data = $exec->fetch_object();

		$dados['radio_ip'] = $data->ip;
		$dados['radio_porta'] = $data->porta;
		$dados['radio_whatsapp'] = $data->whatsapp;

		// programacao
		$programacao = new model_programacao();
		$dados['programacao'] = $programacao->atual();

		return $dados;
	}
	
	//carrega o html 
	protected function view( $arquivo, $vars = null ){
		
		if( is_array($vars) && count($vars) > 0){
			//transforma array em variavel
			//com prefixo
			//extract($vars, EXTR_PREFIX_ALL, 'htm_');
			//se ouver variaveis iguais adiciona prefixo
			extract($vars, EXTR_PREFIX_SAME, 'htm_');
		}

		$url_view = VIEWS."htm_".$arquivo.".php";
		
		if(!file_exists($url_view)){
			$this->erro();
		} else {
			return require_once($url_view);
		}
		
	}
	
	//gera codigo que nunca se repete
	protected function gera_codigo(){
		return substr(time().rand(10000,99999),-15);
	}
	
	//confere se foi preenchido um campo post ou get
	protected function valida($var, $msg = null){
		if(!$var){
			if($msg){
				$this->msg($msg);
				$this->volta(1);
			} else {
				$this->msg('Preencha todos os campos e tente novamente!');
				$this->volta(1);
			}
		}
	}	
	
}