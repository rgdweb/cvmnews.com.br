<?php
//
//$mascara = new mascara();
//$mascara->aplicar($codigo_mascara, $caminho_imagem);
//
Class model_mascara{


	public function lista($codigo = null){

		$lista = array();
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM marcadagua order by titulo asc");
		$i = 0;
		while($data = $exec->fetch_object()) {
			
			$lista[$i]['codigo'] = $data->codigo;
			$lista[$i]['titulo'] = $data->titulo;

			if($data->codigo == $codigo){
				$lista[$i]['selected'] = "selected";
			} else {
				$lista[$i]['selected'] = "";
			}

		$i++;
		}

		return $lista;
	}

	public function aplicar($codigo, $imagem) {

		$conexao = new mysql();
		$coisas_mascara = $conexao->Executar("SELECT * FROM marcadagua where codigo='$codigo' ");
		$data_mascara = $coisas_mascara->fetch_object();

		//preencher a imagem
		if($data_mascara->preencher == 1){
			
			$source = imagecreatefromjpeg($imagem);
			$imagex = imagesx($source);
			$imagey = imagesy($source);

			$original = imagecreatefromjpeg($imagem);
			$mascara = imagecreatefrompng("_views/img/mascara_pontilhados.png");
			imagecopy($original, $mascara, 0, 0, 0, 0, $imagex, $imagey);
			imagejpeg($original, $imagem, 100);

		}

		//mascara imagem
		if(isset($data_mascara->imagem)){

			$logo = "arquivos/img_mascaras/$data_mascara->imagem";

			$source = imagecreatefromjpeg($imagem);
			$imagex = imagesx($source);
			$imagey = imagesy($source);

			$source_logo = imagecreatefrompng($logo);
			$imagex_logo = imagesx($source_logo);
			$imagey_logo = imagesy($source_logo);

			//centro
			if($data_mascara->posicao == 1){
				$ponto_lateral = ($imagex/2)-($imagex_logo/2);
				$ponto_altura = ($imagey/2)-($imagey_logo/2);

				imagecopyresampled($source, $source_logo, $ponto_lateral, $ponto_altura, 0, 0, $imagex_logo, $imagey_logo, $imagex_logo, $imagey_logo);
			}

			//Canto esquerdo superior
			if($data_mascara->posicao == 2){
				imagecopyresampled($source, $source_logo, 10, 10, 0, 0, $imagex_logo, $imagey_logo, $imagex_logo, $imagey_logo);
			}

			//Canto direito superior
			if($data_mascara->posicao == 3){
				$ponto_lateral = ($imagex-$imagex_logo)-10;
				$ponto_altura = 10;
				imagecopyresampled($source, $source_logo, $ponto_lateral, $ponto_altura, 0, 0, $imagex_logo, $imagey_logo, $imagex_logo, $imagey_logo);
			}

			//Canto esquerdo inferior
			if($data_mascara->posicao == 4){
				$ponto_lateral = 10;
				$ponto_altura = ($imagey-$imagey_logo)-10;

				imagecopyresampled($source, $source_logo, $ponto_lateral, $ponto_altura, 0, 0, $imagex_logo, $imagey_logo, $imagex_logo, $imagey_logo);
			}

			//Canto direito inferior
			if($data_mascara->posicao == 5){
				$ponto_lateral = ($imagex-$imagex_logo)-10;
				$ponto_altura = ($imagey-$imagey_logo)-10;

				imagecopyresampled($source, $source_logo, $ponto_lateral, $ponto_altura, 0, 0, $imagex_logo, $imagey_logo, $imagex_logo, $imagey_logo);
			}

			imagejpeg($source, $imagem, 100);

		}

	}

}