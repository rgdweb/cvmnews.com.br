<?php

Class model_areacliente extends model{
	
    public function lista(){
    	
    	$usuarios = new model_usuarios();
    	$lista = array();
    	$i = 0;

    	foreach ($usuarios->lista() as $key => $value) {
			
			$lista[$i]['id'] = $value['id'];
			$lista[$i]['codigo'] = $value['codigo'];
			$lista[$i]['nome'] = $value['nome']; 
			
		$i++;
		}
	  	
		return $lista;
	}


	public function confere_usuario($usuario, $cod_usuario = null){    	 

    	$db = new mysql();
    	if( isset($cod_usuario) ){
    		$confere = $db->executar("SELECT * FROM areacliente WHERE usuario='$usuario' AND codigo!='$cod_usuario' ");
		} else {
			$confere = $db->executar("SELECT * FROM areacliente WHERE usuario='$usuario' ");
		}
		
		if($confere->num_rows != 0){
			return false;
		} else {
			return true;
		}		
    }


    public function arquivos($codigo){    	 
		
		$lista = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM areacliente_arquivos where cliente='$codigo' ORDER BY data desc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['id'] = $data->id;
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['data'] = date('d/m/y H:i',  $data->data);
			$lista[$i]['titulo'] = $data->titulo;

		$i++;
		}
	  	
		return $lista;
    }

    public function nome_usuario($cod_usuario){    	 
    	
    	$db = new mysql();
    	$exec = $db->executar("SELECT nome FROM areacliente WHERE codigo='$cod_usuario' ");
		$data = $exec->fetch_object();

		if(isset($data->nome)){
			return $data->nome;
		} else {
			return "Indisponível";
		}
    }

}