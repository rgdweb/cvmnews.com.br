<?php

Class model_depoimentos extends model{

	public function lista(){
    	
    	$dados = array();
    	$aguardando = array();
		$aprovados = array();
    	
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM depoimento order by id desc");
		$i1 = 0;
		$i2 = 0;
		while($data = $exec->fetch_object()) {
			
			if($data->bloqueio == 2){

				$aprovados[$i1]['id'] = $data->id;
				$aprovados[$i1]['data'] = date('d/m/Y', $data->data);
				$aprovados[$i1]['nome'] = $data->nome;
				$aprovados[$i1]['email'] = $data->email;
				$aprovados[$i1]['cidade'] = $data->cidade;
				if($data->imagem){
					$aprovados[$i1]['imagem'] = PASTA_CLIENTE.'img_depoimentos/'.$data->imagem;
				} else {
					$aprovados[$i1]['imagem'] = "";
				}
				$aprovados[$i1]['conteudo'] = $data->conteudo;
				
			$i1++;
			} else {
				
				$aguardando[$i2]['id'] = $data->id;
				$aguardando[$i2]['data'] = date('d/m/Y', $data->data);
				$aguardando[$i2]['nome'] = $data->nome;
				$aguardando[$i2]['email'] = $data->email;
				$aguardando[$i2]['cidade'] = $data->cidade;
				if($data->imagem){
					$aguardando[$i2]['imagem'] = PASTA_CLIENTE.'img_depoimentos/'.$data->imagem;
				} else {
					$aguardando[$i2]['imagem'] = "";
				}
				$aguardando[$i2]['conteudo'] = $data->conteudo;

			$i2++;
			}
		}

		$dados['aguardando'] = $aguardando;
		$dados['aprovados'] = $aprovados;
	  	
		return $dados;
	}

	///////////////////////////////////////////////////////////////////////////
	//

	public function carregar($id){
    	$db = new mysql();
		$exec = $db->executar("SELECT * FROM depoimento where id='$id' ");
		return $exec->fetch_object();
    }
    
	///////////////////////////////////////////////////////////////////////////
	//

	public function apagar($id){

		// executa
		$db = new mysql();
		$db->apagar("depoimento", " id='".$id."' ");

	}
     
	
}