<?php
class enquete extends controller {
	
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
		
		$trata = new model_valores();

		//lista ultima enquete
		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM enquete ORDER BY id desc limit 1");
		$data = $coisas->fetch_object();
		
 		$dados['enquete']['codigo'] = $data->codigo;
		$dados['enquete']['pergunta'] = $data->enquete;
		
		//calcula total de votos
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
		
		
		//carrega view e envia dados para a tela
		$this->view('enquete', $dados);
	}
	

	public function votar(){

		$codigo_enquete = $this->post('codigo');
		$voto = $this->post('enquete');

		$this->valida($codigo_enquete);
		$this->valida($voto);

		$ip = $_SERVER["REMOTE_ADDR"];
		$time = time();

		//confere se o ip já votou nesta enquete
		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT data FROM enquete_voto WHERE codigo_enquete='$codigo_enquete' AND ip='$ip' order by id desc limit 1 ");
		$data = $coisas->fetch_object();

		//caso já exista um voto confere se foi no mesmo dia
		if($data->data){
			//se for no mesmo dia ele nao deixa votar novamente
			if(date('d/m/Y') == date('d/m/Y', $data->data)){
				$this->msg('Desculpe, é permitido apenas 1 voto por pessoa/ip!');
				$this->volta(1);
			}
		}

		// se passou nas validações grava o voto no banco
		$db = new mysql();
		$coisas = $db->inserir("enquete_voto", array(
			"data"=>"$time",
			"codigo_enquete"=>"$codigo_enquete",
			"codigo_resposta"=>"$voto",
			"ip"=>"$ip"
		));

		$this->msg('Obrigao por votar!');

		$this->irpara(DOMINIO.$this->_controller);
	}

}