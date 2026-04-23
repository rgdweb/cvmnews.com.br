<?php

Class model_servicos extends model{
	
	public function lista(){
			
			$lista = array();
			
			$conexao = new mysql();
			$exec = $conexao->Executar("SELECT * FROM servicos_ordem ORDER BY id desc limit 1");
			$data_ordem = $exec->fetch_object();

			if(isset($data_ordem->data)){

				$order = explode(',', $data_ordem->data);

				$n = 0;
				foreach($order as $key => $value){

					$conexao = new mysql();
					$coisas = $conexao->Executar("SELECT * FROM servicos WHERE id='$value' ");
					$data = $coisas->fetch_object();
					
					if(isset($data->titulo)){

						$lista[$n]['id'] = $data->id;
						$lista[$n]['codigo'] = $data->codigo;
						$lista[$n]['titulo'] = $data->titulo;
						
						$n++;
					}
				}
			}		
			
			return $lista;
	}
	
}