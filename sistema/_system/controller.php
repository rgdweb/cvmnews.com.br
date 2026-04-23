<?php

class controller extends system {
	
	protected $_sessao = false;
	protected $_acesso = false;
	protected $_cod_usuario = false;
	protected $_dados_usuario = false;
	
	protected function autenticacao(){
 		
		if(FORCAR_SSL == true){
			if(!isset($_SERVER['HTTPS'])){
				$destino= "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
				$this->irpara($destino);
			}
		}
		
		if( isset($_SESSION['adm_sessao']) AND isset($_SESSION['adm_acesso']) AND isset($_SESSION['adm_cod_usuario']) ){
			
			// segurança
			// confere se é a mesma pessoa
			if($_SESSION['adm_acesso'] != TOKEN2){
				session_destroy();
				$this->irpara( DOMINIO );
			}
			
			if($_SESSION['adm_acesso'] != TOKEN2){
				session_destroy();
				$this->irpara( DOMINIO );
			}

			$this->_sessao = $_SESSION['adm_sessao'];
			$this->_acesso = $_SESSION['adm_acesso'];
			$this->_cod_usuario = $_SESSION['adm_cod_usuario'];
			$usuario = $_SESSION['adm_usuario'];

			$db = new mysql();
			$exec = $db->executar("SELECT * FROM adm_usuario WHERE codigo='".$this->_cod_usuario."' AND usuario='$usuario' ");
			
			if($exec->num_rows != 1){
				session_destroy();
				$this->irpara( DOMINIO );
			} else {
				$this->_dados_usuario = $exec->fetch_object();
			}
			
		} else {
			$this->irpara(DOMINIO.'autenticacao');
		}
	}

	protected function nivel_acesso_visibilidade($setor){
		if($this->nivel_acesso($setor, false)){
			$db = new mysql();
			$exec = $db->executar("SELECT * FROM adm_setores_perfil WHERE setor='$setor' ");
			if($exec->num_rows != 0){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	protected function nivel_acesso($setor, $msg = true){
    	// função para verificar acesso aos setores

		if( ($this->_cod_usuario == 1) AND ($setor == 0) ){
			return true;
			exit;
		}

		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM adm_setores WHERE id='$setor' ");
		$data_menu = $exec->fetch_object();

		// confere se o modulo existe e é valido
		if(isset($data_menu->titulo)){

			// o cod_usuario = 1 é o principal de cada conta tem acesso a tudo dentro da sua conta
			if($this->_cod_usuario == 1){

				return true;
				exit;

			} else {

			    // caso for usuario normal confere se foi concedido o acesso a ele consultando a base de dados
				$db = new mysql();
				$exec = $db->executar("SELECT id FROM adm_setores_usuario WHERE usuario='".$this->_cod_usuario."' AND setor='".$setor."' ");

				if($exec->num_rows == 0){

					if($msg){
						$this->msg('Permissão negada!');
						$this->irpara( DOMINIO );
					}
					return false;
					exit;

				} else {

					return true;
					exit;

				}

			}

		} else {

			if($msg){
				$this->msg('Permissão negada!');
				$this->irpara( DOMINIO );
			}
			return false;
			exit;

		}

	}

	protected function lista_menu(){

		$lista = array();

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM adm_setores_ordem where usuario='".$this->_cod_usuario."' order by id desc limit 1");
		$data_ordem = $exec->fetch_object();

		if(isset($data_ordem->data)){

			//se a ordem existe
			$order = explode(',', $data_ordem->data);
			$i = 0;	
			foreach($order as $key => $value){

				$db = new mysql();
				$exec = $db->Executar("SELECT * FROM adm_setores WHERE id='".$value."'");
				$data_menu = $exec->fetch_object();

				if(isset($data_menu->id)){

					$db = new mysql();
					$exec_confere = $db->Executar("SELECT * FROM adm_setores_perfil where setor='".$data_menu->id."' ");
					if($exec_confere->num_rows != 0){

						if( $this->nivel_acesso($data_menu->id, false) ){

							$lista[$i]['id'] = $data_menu->id;
							$lista[$i]['titulo'] = $data_menu->titulo;
							$lista[$i]['icone'] = $data_menu->ico;
							$lista[$i]['endereco'] = $data_menu->endereco;

							if($this->_controller == $data_menu->endereco){
								$lista[$i]['ativo'] = true;
							} else {
								$lista[$i]['ativo'] = false;
							}

							$i++;
						}
					}
				}
			}

		} else {

			//carrega lista por ordem alfabetica
			$db = new mysql();
			$exec = $db->executar("SELECT * FROM adm_setores where id_pai='0' AND menu='0' order by titulo asc");
			$i = 0;
			while($data_menu = $exec->fetch_object()){

				$db = new mysql();
				$exec_confere = $db->Executar("SELECT * FROM adm_setores_perfil where setor='$data_menu->id' ");
				if($exec_confere->num_rows != 0){

					if( $this->nivel_acesso($data_menu->id, false) ){

						$lista[$i]['id'] = $data_menu->id;
						$lista[$i]['titulo'] = $data_menu->titulo;
						$lista[$i]['icone'] = $data_menu->ico;
						$lista[$i]['endereco'] = $data_menu->endereco;

						if($this->_controller == $data_menu->endereco){
							$lista[$i]['ativo'] = true;
						} else {
							$lista[$i]['ativo'] = false;
						}

						$i++;
					}					
				}
			}
		}		

		return $lista;
	}

    // carrega coisas basicas que tem em todas as views 
	protected function base_layout(){

		$dados = array();
		$dados['libera_views'] = true;

		$navegador = new model_navegador();
		$dados['navegador'] = $navegador->nome();

		//logo do sistema
		$logo = new model_logo();
		$dados['logo'] = $logo->imagem();
		
		//objeto
		$dados['objeto'] = DOMINIO.$this->_controller.'/';

		////////////////////////////////////////////////////
		// parte logado

		if($this->_acesso){

			//carrega menu
			$dados['menu_lateral'] = $this->lista_menu();

			//carrega imagem do usuario
			if($this->_dados_usuario->imagem){
				$dados['conta_imagem'] = PASTA_CLIENTE."img_usuarios/".$this->_dados_usuario->imagem;
			} else {
				$dados['conta_imagem'] = LAYOUT."img/usuario.png";
			}

			//nome do usuário
			$dados['conta_nome'] = $this->_dados_usuario->nome;

			//codigo do usuario
			$dados['conta_codigo'] = $this->_dados_usuario->codigo;

			//email
			$dados['conta_email'] = $this->_dados_usuario->email_recuperacao;

			if($this->_dados_usuario->codigo == 1){
				$dados['conta_tipo'] = "administrador";
			} else {
				$dados['conta_tipo'] = "usuário";
			}
			
			//menu aberto ou fechado
			$dados['menu_fechado'] = $this->_dados_usuario->abre_fecha_menu;
			
		}
				
		return $dados;
	}

    // carrega view na tela 
	protected function view( $arquivo, $vars = null ){

		if( is_array($vars) && count($vars) > 0){
			//transforma array em variavel
			//com prefixo
			//extract($vars, EXTR_PREFIX_ALL, 'htm_');
			//se ouver variaveis iguais adiciona prefixo
			extract($vars, EXTR_PREFIX_SAME, 'htm_');
		}

		$url_view = VIEWS."htm_".$arquivo.".php";

		return require_once($url_view);
	}

	// funções padrçoes de uso basico, coloquei aqui pois achei mais pratico

	protected function gera_codigo(){
		return substr(time().rand(10000,99999),-15);
	}

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