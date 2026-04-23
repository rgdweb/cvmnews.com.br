<?php

class index extends controller {
	
	public function init(){		
	}
	
	public function inicial(){
		
		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;
		
		//banner
		$banners = new model_banners();
		$dados['banners'] = $banners->lista('149601285477607');
		
		//textos
		$texto = new model_texto();



		$dados['texto_subbanner1'] = $texto->conteudo_simples('152447459593957');
		$dados['texto_subbanner2'] = $texto->conteudo_simples('152219083141687');
		$dados['texto_subbanner3'] = $texto->conteudo_simples('152219087174400');
		$dados['texto_subbanner4'] = $texto->conteudo_simples('152219099191480');
 		
		$dados['apresentacao'] = $texto->conteudo_url('apresentacao');

		$dados['apres_subtxt1'] = $texto->conteudo_simples('153686334393447'); 
		$dados['apres_subtxt2'] = $texto->conteudo_simples('153686338910405'); 
		$dados['apres_subtxt3'] = $texto->conteudo_simples('153686343939723'); 
		$dados['apres_subtxt4'] = $texto->conteudo_simples('153686347359034'); 
		
		$dados['numero_1'] = $texto->conteudo_simples('152450163057455');
		$dados['numero_2'] = $texto->conteudo_simples('152450161073363');
		$dados['numero_3'] = $texto->conteudo_simples('152447456913899');
		
		$dados['comotrabalhamos'] = $texto->conteudo_url('comotrabalhamos');

		$dados['sanfona1'] = $texto->conteudo_simples('153692417088315');
		$dados['sanfona2'] = $texto->conteudo_simples('153692705366094');
		$dados['sanfona3'] = $texto->conteudo_simples('153692709067198');
		$dados['sanfona4'] = $texto->conteudo_simples('153692712151770');
		$dados['sanfona5'] = $texto->conteudo_url('153692427392228');
		
		$dados['galeria_texto'] = $texto->conteudo_url('galeria');
		$dados['equipe_texto'] = $texto->conteudo_url('153692984161677');

		$dados['enquete_texto'] = $texto->conteudo_url('enquete');

		$dados['radio_texto'] = $texto->conteudo_simples('152218928730810');

		$dados['contato_texto'] = $texto->conteudo_url('faleconosco');
			
		$dados['programacao_texto'] = $texto->conteudo_url('programacao');

		//carrega modulo de noticias/bog
		$blog = new model_postagens();
		$blog->perpage = 2;
		$blog->destaque = '148895720854403';
	 	//retorno do blog pra variavel
		$blogarray = $blog->lista();
		$dados['noticias'] = $blogarray['noticias'];
		
			// fotos
		$fotos = new model_portfolio();
		$dados['fotos'] = $fotos->lista_inicial('152459334482807');
		
		// equipe
		$equipe = new model_equipe();
		$dados['equipe'] = $equipe->lista_inicial();
		
		


			// lista ultima enquete
		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM enquete WHERE status='1' ORDER BY id desc limit 1");
		$data = $coisas->fetch_object();

		$dados['enquete']['codigo'] = $data->codigo;
		$dados['enquete']['pergunta'] = $data->enquete;

			//calcula total de votos
		$trata = new model_valores();	

		$conexao = new mysql();
		$coisas_vot_total = $conexao->Executar("SELECT id FROM enquete_voto WHERE codigo_enquete='".$dados['enquete']['codigo']."' ");
		$linhas_vot_total = $coisas_vot_total->num_rows;

			//lisa respostas
		$respostas = array();
		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM enquete_resposta WHERE enquete_codigo='".$dados['enquete']['codigo']."' ");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$respostas[$n]['texto'] = $data->resposta;
			$respostas[$n]['codigo'] = $data->codigo;

				//calula numero de votos
			$conexao = new mysql();
			$coisas_vot = $conexao->Executar("SELECT id FROM enquete_voto WHERE codigo_enquete='".$dados['enquete']['codigo']."' AND codigo_resposta='$data->codigo' ");
			$linhas_vot = $coisas_vot->num_rows;

			$respostas[$n]['votos'] = $linhas_vot;

				//calula porcentagem de votos
			if($linhas_vot != 0){
				$porcento = ($linhas_vot / $linhas_vot_total) * 100;
				$porcento = $trata->trata_valor_calculo($porcento);
			} else {
				$porcento = 0;
			}
			$respostas[$n]['votos_porc'] = $porcento;

			$n++;
		}
		$dados['enquete_respostas'] = $respostas;


		$programacao = new model_programacao();
		$dados['proximo'] = $programacao->proximo();
		$dados['programacao'] = $programacao->atual();
		$dia = date('w')+1;
		$dados['dia'] = $dia;
 		$dados['lista_dia'] = $programacao->lista_dia($dia-1);

		
		//carrega view e envia dados para a tela
		$this->view('index', $dados);
	}
	
}