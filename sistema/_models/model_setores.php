<?php

Class model_setores extends model{

	/////////////////////////////////////////////////////////////////////////////
	//

	private $tab_principal = "adm_setores";
	private $tab_perfil = "adm_setores_perfil";
	private $tab_ordem = "adm_setores_ordem";

	///////////////////////////////////////////////////////////////////////////
	//

	public function inserir($vars){
 		
 		// condições da base de dados
		$dados = array(
			'id_pai'=>$vars[0],
			'titulo'=>$vars[1],
			'titulo_tecnico'=>$vars[2],
			'endereco'=>$vars[3],
			'ico'=>$vars[4]
		);

		// executa
		$db = new mysql();
		$db->inserir($this->tab_principal, $dados);
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function alterar($vars, $condicoes){

		$dados = array(
			'id_pai'=>$vars[0],
			'titulo'=>$vars[1],
			'titulo_tecnico'=>$vars[2],
			'endereco'=>$vars[3],
			'ico'=>$vars[4]
		);

		// executa
		$db = new mysql();
		$db->alterar($this->tab_principal, $dados, " id='".$condicoes[0]."' ");
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function apagar($condicoes){

		// executa
		$db = new mysql();
		$db->apagar($this->tab_principal, " id='".$condicoes[0]."' ");

	}
	
	///////////////////////////////////////////////////////////////////////////
	//

    public function lista(){
    	
    	$lista = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_principal." order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$db = new mysql();
			$exec_confere = $db->Executar("SELECT * FROM ".$this->tab_perfil." where setor='$data->id' ");
			if($exec_confere->num_rows != 0){
				
				$lista[$i]['id'] = $data->id;
				$lista[$i]['id_pai'] = $data->id_pai;
				$lista[$i]['titulo'] = $data->titulo;
				$lista[$i]['titulo_tecnico'] = $data->titulo_tecnico;
				$lista[$i]['check'] = '';
				
			$i++;
			}
		}
	  	
		return $lista;
	}
	
	///////////////////////////////////////////////////////////////////////////
	//
	
	public function lista_completa(){
    	
    	$lista = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_principal." order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			 				
				$lista[$i]['id'] = $data->id;
				$lista[$i]['id_pai'] = $data->id_pai;
				$lista[$i]['titulo'] = $data->titulo;
				$lista[$i]['titulo_tecnico'] = $data->titulo_tecnico;

				$db = new mysql();
				$exec_confere = $db->Executar("SELECT setor FROM ".$this->tab_perfil." where setor='$data->id' ");
				if($exec_confere->num_rows != 0){
					$lista[$i]['check'] = true;
				} else {
					$lista[$i]['check'] = false;
				}
				
			$i++;		
		}
	  	
		return $lista;
	}	 
	
	///////////////////////////////////////////////////////////////////////////
	//
	
	public function adiciona_perfil($id){

		$db = new mysql();
		$db->inserir($this->tab_perfil, array(
			"setor"=>"$id"
		));

	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function remove_perfil($id){
		
		$db = new mysql();
		$db->apagar($this->tab_perfil, " setor='$id' ");
		
	}	 

    ///////////////////////////////////////////////////////////////////////////
	//

    public function total_modulos(){
    	$db = new mysql();
		$exec = $db->executar("SELECT id FROM ".$this->tab_principal." where id_pai='0' order by id asc");
		return $exec->num_rows;
    }

    ///////////////////////////////////////////////////////////////////////////
	//

    public function selecionar($id){
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM ".$this->tab_principal." where id='$id' ");
		return $exec->fetch_object();
    }
	
}