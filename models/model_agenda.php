<?php

Class model_agenda extends model{
    
    public function lista($limit = 1000){
    	
		//lista ultimos itens da agenda
		$agenda = array();
		
		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM agenda ORDER BY data asc limit $limit");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$agenda[$n]['data'] = date('d/m', $data->data);
			$agenda[$n]['hora'] = date('H:i', $data->data);
			$agenda[$n]['titulo'] = $data->titulo;
			$agenda[$n]['descricao'] = $data->descricao;

		$n++;
		}
		
		return $agenda;
    }
    
}