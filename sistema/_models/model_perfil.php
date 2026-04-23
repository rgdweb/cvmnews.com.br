<?php

Class model_perfil extends model{
	
	/////////////////////////////////////////////////////////////////////////////
	//
	
	private $tab_usuario = "adm_usuario";
	private $tab_ordem = "adm_setores_ordem";
	private $tab_setor = "adm_setores";


	///////////////////////////////////////////////////////////////////////////
	//

	public function alterar_info($vars, $usuario){

		$dados = array(
			'nome'=>$vars[0],
			'email_recuperacao'=>$vars[1]
		);

		// executa
		$db = new mysql();
		$db->alterar($this->tab_usuario, $dados, " codigo='".$usuario."' ");
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function alterar_senha($vars, $usuario){
		
		$dados = array(
			'usuario'=>$vars[0],
			'senha'=>$vars[1]
		);
		
		// executa
		$db = new mysql();
		$db->alterar($this->tab_usuario, $dados, " codigo='".$usuario."' ");
	}
	
	///////////////////////////////////////////////////////////////////////////
	//
	
	public function alterar_imagem($imagem, $usuario){ 

		// executa
		$db = new mysql();
		$db->alterar($this->tab_usuario, array("imagem"=>"$imagem"), " codigo='".$usuario."' ");
		
	}
	
	///////////////////////////////////////////////////////////////////////////
	//

	public function ordem($usuario){
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_ordem." where usuario='$usuario' order by id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){  	
			return $data_ordem->data;
		} else {
			return false;
		}

	}

	///////////////////////////////////////////////////////////////////////////
	//
	
	public function alterar_ordem_menu($data, $usuario){
		
		$db = new mysql();
		$db->apagar($this->tab_ordem, " usuario='$usuario' ");
		
		$db = new mysql();
		$db->inserir($this->tab_ordem, array(
			"usuario"=>"$usuario",
			"data"=>"$data"
		));
		
	}	
	
	///////////////////////////////////////////////////////////////////////////
	//
	
	public function encolhe_menu($usuario){

		$db = new mysql();
		$exec = $db->executar("SELECT abre_fecha_menu FROM ".$this->tab_usuario." where codigo='$usuario' ");
		$data = $exec->fetch_object();
		
		if($data->abre_fecha_menu == 0){
			
			$db = new mysql();
			$exec = $db->alterar($this->tab_usuario, array(
				"abre_fecha_menu"=>"1"			
			), " codigo='$usuario' ");

		} else {

			$db = new mysql();
			$exec = $db->alterar($this->tab_usuario, array(
				"abre_fecha_menu"=>"0"
			), " codigo='$usuario' ");

		}

	}
    
    ///////////////////////////////////////////////////////////////////////////
	//

	public function lista_menu($usuario, $controller){
    	
		$usuarios = new model_usuarios();
		
    	$lista = array();
    	
		$ordem = $this->ordem($usuario);
		
		if($ordem AND ($usuario != 1) ){			 

			//a ordem existe
			$order = explode(',', $ordem);
			$i = 0;	
			foreach($order as $key => $value){
				
				$db = new mysql();
				$exec = $db->Executar("SELECT * FROM ".$this->tab_setor." WHERE id='$value' AND id_pai='0' ");
				$data_menu = $exec->fetch_object();
				
				if(isset($data_menu->id)){
					
					if( $usuarios->confere_acesso($usuario, $data_menu->id) ){
						
						$lista[$i]['id'] = $data_menu->id;
						$lista[$i]['titulo'] = $data_menu->titulo;
						$lista[$i]['icone'] = $data_menu->ico;
						$lista[$i]['endereco'] = $data_menu->endereco;
						
						if($controller == $data_menu->endereco){
							$lista[$i]['ativo'] = true;
						} else {
							$lista[$i]['ativo'] = false;
						}
						
						$i++;
					}
				}
			}

		} else { 

			//carrega lista por ordem alfabetica
			$db = new mysql();
			$exec = $db->executar("SELECT * FROM ".$this->tab_setor." where id_pai='0' order by titulo asc");
			$i = 0;
			while($data_menu = $exec->fetch_object()){
				
				if( $usuarios->confere_acesso($usuario, $data_menu->id) ){

						$lista[$i]['id'] = $data_menu->id;
						$lista[$i]['titulo'] = $data_menu->titulo;
						$lista[$i]['icone'] = $data_menu->ico;
						$lista[$i]['endereco'] = $data_menu->endereco;

						if($controller == $data_menu->endereco){
							$lista[$i]['ativo'] = true;
						} else {
							$lista[$i]['ativo'] = false;
						}
						
				$i++;
				}

			}
		}

        return $lista;
    }
	
}