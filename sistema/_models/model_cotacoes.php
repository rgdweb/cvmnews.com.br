<?php

Class model_cotacoes extends model{

	public function lista($grupo = null){
		
		$lista = array();
		$n = 0;

		if($grupo){

			$conexao = new mysql();
			$coisas = $conexao->Executar("SELECT * FROM cotacoes WHERE grupo='$grupo' order by titulo asc");
			while($data = $coisas->fetch_object()){

				$lista[$n]['id'] = $data->id;
				$lista[$n]['codigo'] = $data->codigo;
				$lista[$n]['titulo'] = $data->titulo;
				$lista[$n]['data'] = date('d/m/Y H:i', $data->data);
				$lista[$n]['regiao'] = $data->regiao;
				$lista[$n]['valor'] = $data->valor;

			$n++;
			}
		}
			
		return $lista;
	}
	
    public function lista_grupos($selecionado = null){
		return $this->lista_grupo_filho(0, $selecionado);
	}
	
	private function lista_grupo_filho($id_pai, $selecionado = null){
		
		$lista = array();
		
		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM cotacoes_grupos_ordem where id_pai='$id_pai' ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();

		if(isset($data_ordem->data)){

			$order = explode(',', $data_ordem->data);

			$n = 0;
			foreach($order as $key => $value){

				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM cotacoes_grupos WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->titulo)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['selected'] = '';
					
					if($selecionado == $data->codigo){
						$lista[$n]['selected'] = "selected";
					}

					$lista[$n]['filhos'] = $this->lista_grupo_filho($data->id, $selecionado);
					
					$n++;
				}
			}
		}
	  	
		return $lista;
	}

}