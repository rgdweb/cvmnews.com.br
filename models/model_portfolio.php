<?php

Class model_portfolio extends model{

	public function lista($grupo = null){
		
		$lista = array();
		
		if($grupo){

			$conexao = new mysql();
			$exec = $conexao->Executar("SELECT * FROM fotos_ordem where grupo='$grupo' ORDER BY id desc limit 1");
			$data_ordem = $exec->fetch_object();
			
			if(isset($data_ordem->data)){
				
				$order = explode(',', $data_ordem->data);
				
				$n = 0;
				foreach($order as $key => $value){
					
					$conexao = new mysql();
					$coisas = $conexao->Executar("SELECT * FROM fotos WHERE id='$value' ");
					$data = $coisas->fetch_object();
					
					if(isset($data->titulo)){
						
						$lista[$n]['id'] = $data->id;
						$lista[$n]['codigo'] = $data->codigo;
						$lista[$n]['titulo'] = $data->titulo;
						$lista[$n]['conteudo'] = $data->conteudo;
						$lista[$n]['imagens'] = $this->imagens($data->codigo);

						if(isset($lista[$n]['imagens'][0]['imagem'])){
							$lista[$n]['imagem_princial'] = $lista[$n]['imagens'][0]['imagem'];
						} else {
							$lista[$n]['imagem_princial'] = LAYOUT.'img/semimagem.png';
						}						 

						$n++;
					}
				}
			}
		}
		
		//echo "<pre>"; print_r($lista); echo "<pre>"; exit;
		return $lista;
	}
	
    public function lista_grupos($selecionado = null){
		return $this->lista_grupo_filho(0, $selecionado);
	}
	
	private function lista_grupo_filho($id_pai, $selecionado = null){
		
		$lista = array();
		
		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM fotos_grupos_ordem where id_pai='$id_pai' ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){

				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM fotos_grupos WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->titulo)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['selected'] = false;
					
					if($selecionado == $data->codigo){
						$lista[$n]['selected'] = true;
					}

					$lista[$n]['filhos'] = $this->lista_grupo_filho($data->id, $selecionado);
					
					// deixa selecionado caso algum dos filhos for selecionado
					foreach($lista[$n]['filhos'] as $key2 => $value2){
						if($value2['selected']){
							$lista[$n]['selected'] = true;
						}
					}
					
					$n++;
				}
			}
		}
	  	
		return $lista;
	}

	public function lista_inicial($grupo){
		
		$lista = array();
		$n = 0;

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT codigo FROM fotos WHERE grupo='$grupo' order by rand()");
		while($data = $coisas->fetch_object()){
			$imagens = $this->imagens($data->codigo);
			foreach ($imagens as $key => $value) {
				$lista[$n] = $value['imagem'];
			$n++;
			}
		}
		
		shuffle($lista);
		
		//echo "<pre>"; print_r($lista); echo "<pre>"; exit;
		return $lista;
	}

	public function imagens($codigo){

		//imagens
 		$conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM fotos_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $imagens = array();
        if(isset($data_ordem->data)){

        	$order = explode(',', $data_ordem->data); 

        	foreach($order as $key => $value){

                $conexao = new mysql();
                $coisas_img = $conexao->Executar("SELECT * FROM fotos_imagem WHERE id='$value'");
                $data_img = $coisas_img->fetch_object();                                

                if(isset($data_img->imagem)){

                	$conexao = new mysql();
	                $coisas_leg = $conexao->Executar("SELECT * FROM fotos_imagem_legenda WHERE id_img='$value' ");
	                $data_leg = $coisas_leg->fetch_object();
	                
	                if(isset($data_leg->legenda)){
	                	$imagens[$n]['legenda'] = $data_leg->legenda;
	                } else {
	                	$imagens[$n]['legenda'] = "";
	                }

                	$imagens[$n]['id'] = $data_img->id;
               		$imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_fotos_p/'.$codigo.'/'.$data_img->imagem;
               		$imagens[$n]['imagem'] = PASTA_CLIENTE.'img_fotos_g/'.$codigo.'/'.$data_img->imagem;

                $n++;
                }
            }
        }
        return $imagens;

	}

}