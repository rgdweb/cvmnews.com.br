<?php

Class model_produtos extends model{

	public function lista($grupo = null){
		
		$lista = array();
		$n = 0;

		if($grupo){

			$conexao = new mysql();
			$exec = $conexao->Executar("SELECT * FROM produtos_ordem where grupo='$grupo' ORDER BY id desc limit 1");
			$data_ordem = $exec->fetch_object();
			
			if(isset($data_ordem->data)){
				
				$order = explode(',', $data_ordem->data);

				foreach($order as $key => $value){
					
					$conexao = new mysql();
					$coisas = $conexao->Executar("SELECT * FROM produtos WHERE id='$value' ");
					$data = $coisas->fetch_object();
					
					if(isset($data->titulo)){
						
						$lista[$n]['id'] = $data->id;
						$lista[$n]['codigo'] = $data->codigo;
						$lista[$n]['titulo'] = $data->titulo;
						$lista[$n]['conteudo'] = $data->conteudo;
						$lista[$n]['imagens'] = $this->imagens($data->codigo);
						
						if(isset($lista[$n]['imagens'][0]['imagem'])){
							$lista[$n]['imagem_principal'] = $lista[$n]['imagens'][0]['imagem'];
						} else {
							$lista[$n]['imagem_principal'] = LAYOUT."img/semimagem.png";
						}

						$n++;
					}
				}
			}

		} else {
			
			$conexao = new mysql();
			$coisas = $conexao->Executar("SELECT * FROM produtos order by rand() limit 9");
			while($data = $coisas->fetch_object()){
					
				$lista[$n]['id'] = $data->id;
				$lista[$n]['codigo'] = $data->codigo;
				$lista[$n]['titulo'] = $data->titulo;
				$lista[$n]['conteudo'] = $data->conteudo;
				$lista[$n]['imagens'] = $this->imagens($data->codigo);
						
				if(isset($lista[$n]['imagens'][0]['imagem'])){
					$lista[$n]['imagem_principal'] = $lista[$n]['imagens'][0]['imagem'];
				} else {
					$lista[$n]['imagem_principal'] = LAYOUT."img/semimagem.png";
				}

			$n++;
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
		$exec = $conexao->Executar("SELECT * FROM produtos_grupos_ordem where id_pai='$id_pai' ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM produtos_grupos WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->titulo)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['selected'] = false;
					
					if($selecionado == $data->codigo){
						$lista[$n]['selected'] = true;
					}
					
					if($data->imagem){
						$lista[$n]['imagem'] = PASTA_CLIENTE."img_produtos_grupos/".$data->imagem;
					} else {
						$lista[$n]['imagem'] = false;
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

	public function lista_grupos_inicial($grupo = null){
    	
    	$lista = array();
		
		$conexao = new mysql();
		$exec = $conexao->Executar("SELECT * FROM produtos_grupos_ordem where id_pai='0' ORDER BY id desc limit 1");
		$data_ordem = $exec->fetch_object();
		
		if(isset($data_ordem->data)){
			
			$order = explode(',', $data_ordem->data);
			
			$n = 0;
			foreach($order as $key => $value){
				
				$conexao = new mysql();
				$coisas = $conexao->Executar("SELECT * FROM produtos_grupos WHERE id='$value' ");
				$data = $coisas->fetch_object();
				
				if(isset($data->imagem)){
					
					$lista[$n]['id'] = $data->id;
					$lista[$n]['codigo'] = $data->codigo;
					$lista[$n]['titulo'] = $data->titulo;
					$lista[$n]['imagem'] = PASTA_CLIENTE."img_produtos_grupos/".$data->imagem;
					
					$lista[$n]['selected'] = false;
					
					if( ($n == 0) AND (!$grupo) ){					 
						$lista[$n]['selected'] = true;
					} else {
						if($grupo == $data->codigo){
							$lista[$n]['selected'] = true;
						}
					}
					
					$n++;
				}
			}
		}
	  	
		return $lista;
	}


	public function imagens($codigo){

		//imagens
 		$conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM produtos_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $imagens = array();
        if(isset($data_ordem->data)){

        	$order = explode(',', $data_ordem->data); 

        	foreach($order as $key => $value){

                $conexao = new mysql();
                $coisas_img = $conexao->Executar("SELECT * FROM produtos_imagem WHERE id='$value'");
                $data_img = $coisas_img->fetch_object();                                

                if(isset($data_img->imagem)){

                	$conexao = new mysql();
	                $coisas_leg = $conexao->Executar("SELECT * FROM produtos_imagem_legenda WHERE id_img='$value' ");
	                $data_leg = $coisas_leg->fetch_object();
	                
	                if(isset($data_leg->legenda)){
	                	$imagens[$n]['legenda'] = $data_leg->legenda;
	                } else {
	                	$imagens[$n]['legenda'] = "";
	                }

                	$imagens[$n]['id'] = $data_img->id;
               		$imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_produtos_p/'.$codigo.'/'.$data_img->imagem;
               		$imagens[$n]['imagem'] = PASTA_CLIENTE.'img_produtos_g/'.$codigo.'/'.$data_img->imagem;

                $n++;
                }
            }
        }
        return $imagens;

	}

}