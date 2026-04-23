<?php

Class model_usuarios extends model{
	
	/////////////////////////////////////////////////////////////////////////////
	//
	
	private $tab_usuario = "adm_usuario";
	private $tab_usuario_acesso = "adm_setores_usuario";
	

	///////////////////////////////////////////////////////////////////////////
	//

	public function adicionar($vars){

		$dados = array(
			'codigo'=>$vars[0],
			'nome'=>$vars[1],
			'email_recuperacao'=>$vars[2],
			'usuario'=>$vars[3],
			'senha'=>$vars[4]
		);

		// executa
		$db = new mysql();
		$db->inserir($this->tab_usuario, $dados);

	}

	///////////////////////////////////////////////////////////////////////////
	//

    public function adiciona_usuario_setor($usuario, $setor){

    	$db = new mysql();
		$db->inserir($this->tab_usuario_acesso, array(
		"usuario"=>"$usuario",
		"setor"=>"$setor"
		));
    }

	///////////////////////////////////////////////////////////////////////////
	//

    public function remove_usuario_setor($usuario, $setor){

    	$db = new mysql();
		$db->apagar($this->tab_usuario_acesso, " usuario='$usuario' AND setor='$setor' ");
    }

	///////////////////////////////////////////////////////////////////////////
	//

	public function alterar($vars, $codigo){

		if($vars[2] AND $vars[3]){

			$dados = array(
				'nome'=>$vars[0],
				'email_recuperacao'=>$vars[1],
				'usuario'=>$vars[2],
				'senha'=>$vars[3]
			);

		} else {

			$dados = array(
				'nome'=>$vars[0],
				'email_recuperacao'=>$vars[1]
			);

		}

		// executa
		$db = new mysql();
		$db->alterar($this->tab_usuario, $dados, " codigo='$codigo' ");

	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function apagar($codigo){
		
		// executa
		$db = new mysql();
		$db->apagar($this->tab_usuario, " codigo='$codigo' AND codigo!='1' ");
		
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

    public function lista(){
    	
    	$lista = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_usuario." WHERE codigo!='1' ");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['nome'] = $data->nome;
			$lista[$i]['email'] = $data->email_recuperacao;
			
		$i++;
		}
	  	
		return $lista;
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function selecionar($codigo){ 

    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_usuario." WHERE codigo='$codigo' ");
		$num = $exec->num_rows;
		if($num == 1){
			return $exec->fetch_object();	
		} else {
			return false;
		}

	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function confere_usuario($usuario, $cod_usuario = null){
    	
    	$usuario_md5 = md5($usuario);

    	$db = new mysql();
    	if( isset($cod_usuario) ){
    		$confere = $db->executar("SELECT * FROM ".$this->tab_usuario." WHERE usuario='$usuario_md5' AND codigo!='$cod_usuario' ");
		} else {
			$confere = $db->executar("SELECT * FROM ".$this->tab_usuario." WHERE usuario='$usuario_md5' ");
		}
		
		if($confere->num_rows != 0){
			return false;
		} else {
			return true;
		}		
    }

    ///////////////////////////////////////////////////////////////////////////
	//

    public function confere_login($usuario, $senha){
    	$db = new mysql();
		$exec = $db->executar("SELECT codigo, usuario FROM ".$this->tab_usuario." WHERE usuario='$usuario' AND senha='$senha' ");
		if($exec->num_rows == 1){
			return $exec->fetch_object();
		} else {
			return false;
		}
    }

    ///////////////////////////////////////////////////////////////////////////
	//
    
    public function confere_acesso($usuario, $setor){
    	
    	if($usuario == 1){
    		return true;
    	} else {

	    	$db = new mysql();
			$exec = $db->executar("SELECT id FROM ".$this->tab_usuario_acesso." WHERE usuario='$usuario' AND setor='$setor' ");
			if($exec->num_rows == 0){
				return false;
			} else {
				return true;
			}
		}
    }
	

}