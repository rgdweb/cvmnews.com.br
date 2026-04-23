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
						$lista[$n]['descricao'] = $data->conteudo;
						$lista[$n]['imagens'] = $this->imagens($data->codigo);
						
						$n++;
					}
				}
			}		
			
			return $lista;
	}

	public function imagens($codigo){


		//imagens
 		$conexao = new mysql();
        $coisas_ordem = $conexao->Executar("SELECT * FROM servicos_imagem_ordem WHERE codigo='$codigo' ORDER BY id desc limit 1");
        $data_ordem = $coisas_ordem->fetch_object();

        $n = 0;
        $imagens = array();
        if(isset($data_ordem->data)){

        	$order = explode(',', $data_ordem->data); 

        	foreach($order as $key => $value){
               	
                $conexao = new mysql();
                $coisas_img = $conexao->Executar("SELECT * FROM servicos_imagem WHERE id='$value'");
                $data_img = $coisas_img->fetch_object();                                

                if(isset($data_img->imagem)){

                	$conexao = new mysql();
	                $coisas_leg = $conexao->Executar("SELECT * FROM servicos_imagem_legenda WHERE id_img='$value' ");
	                $data_leg = $coisas_leg->fetch_object();
	                
	                if(isset($data_leg->legenda)){
	                	$imagens[$n]['legenda'] = $data_leg->legenda;
	                } else {
	                	$imagens[$n]['legenda'] = "";
	                }
	                
                	$imagens[$n]['id'] = $data_img->id;
               		$imagens[$n]['imagem_p'] = PASTA_CLIENTE.'img_servicos_p/'.$codigo.'/'.$data_img->imagem;
               		$imagens[$n]['imagem_g'] = PASTA_CLIENTE.'img_servicos_g/'.$codigo.'/'.$data_img->imagem;

                $n++;
                }
            }
        }

        return $imagens;
	}

	
}