<?php

Class model_depoimentos extends model{
    
    public function lista($limite = null){
 		
		$depoimentos = array();
 		
		$db = new mysql();
		
		if($limite){
			$exec = $db->executar("select * from depoimento WHERE bloqueio='2' order by rand() limit $limite");
		} else {
			$exec = $db->executar("select * from depoimento WHERE bloqueio='2' order by rand() ");
		}
		$n = 0;
		while($data = $exec->fetch_object()){
			
			$depoimentos[$n]['data'] = date('d/m/Y', $data->data);
			$depoimentos[$n]['nome'] = $data->nome;
			$depoimentos[$n]['cidade'] = $data->cidade;
			$depoimentos[$n]['conteudo'] = $data->conteudo;
			$depoimentos[$n]['imagem'] = PASTA_CLIENTE."img_depoimentos/".$data->imagem;
			
		$n++;
		}

		return $depoimentos;

    }

}