<?php

Class model_representantes extends model{

	public function lista($estado){
		
		$lista = array();
		
		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM representante WHERE estado='$estado' ORDER BY nome asc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['nome'] = $data->nome;
			$lista[$n]['contato'] = $data->contato;
			$lista[$n]['regiao'] = $data->regiao;
			$lista[$n]['celular'] = $data->celular;
			$lista[$n]['fone'] = $data->fone;
			$lista[$n]['email'] = $data->email;
			
		$n++;
		}
		
		//echo "<pre>"; print_r($lista); echo "<pre>"; exit;
		return $lista;
	}
	
	public function estados($estado = null){
		
		$lista = array();
		
		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM estado ORDER BY nome asc");
		$n = 0;
		while($data = $coisas->fetch_object()){
			
			$lista[$n]['nome'] = $data->nome;
			$lista[$n]['uf'] = $data->uf;
			
			if($data->uf == $estado){
				$lista[$n]['selected'] = 'selected';
			} else {
				$lista[$n]['selected'] = '';
			}
			
		$n++;
		}
		
		//echo "<pre>"; print_r($lista); echo "<pre>"; exit;
		return $lista;
	}

	public function estado_nome($estado){

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM estado where uf='$estado' ");
		$data = $exec->fetch_object();
		return $data->nome;
	}

}