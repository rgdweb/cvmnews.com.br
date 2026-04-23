<?php

class enquetes extends controller {
	
	protected $_modulo_nome = "Enquetes";

	public function init(){
		$this->autenticacao();
		$this->nivel_acesso(47);
	}
	
	public function inicial(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
		$dados['_subtitulo'] = "";
		 
 		
		$lista = array();

		$conexao = new mysql();
		$coisas = $conexao->executar("SELECT * FROM enquete ORDER BY id desc");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$lista[$n]['id'] = $data->id;
			$lista[$n]['codigo'] = $data->codigo;
			$lista[$n]['enquete'] = $data->enquete;
					 
			if($data->status == 1){
				$status = "Ativo";
			} else {
				$status = "Finalizado";
			}
			
			$lista[$n]['status'] = $status;

		$n++;
		}
		$dados['lista'] = $lista;

		
		$this->view('enquetes', $dados);
	}
	

	public function novo(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Novo";

 		$dados['aba_selecionada'] = "dados";


		$this->view('enquetes.novo', $dados);
	}


	public function novo_grv(){
		
		$enquete = $this->post('enquete'); 

		$this->valida($enquete); 

		$codigo = $this->gera_codigo();

		$db = new mysql();
		$db->inserir("enquete", array(
			"codigo"=>"$codigo", 
			"enquete"=>"$enquete",
			"status"=>"1"	
		));
	 	
		$this->irpara(DOMINIO.$this->_controller.'/alterar/aba/respostas/codigo/'.$codigo);
	}
	

	public function alterar(){
		
		$dados['_base'] = $this->base_layout();
		$dados['_titulo'] = $this->_modulo_nome;
 		$dados['_subtitulo'] = "Alterar";

 		$codigo = $this->get('codigo');

 		$aba = $this->get('aba');
 		if($aba){
 			$dados['aba_selecionada'] = $aba;
 		} else {
 			$dados['aba_selecionada'] = 'dados';
 		}

 		$db = new mysql();
 		$exec = $db->Executar("SELECT * FROM enquete where codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();
    	

		$trata = new model_valores();

		//relatorio
		$relatorio = array();

		$conexao = new mysql();
		$coisas_vot_total = $conexao->Executar("SELECT id FROM enquete_voto WHERE codigo_enquete='$codigo' ");
		$linhas_vot_total = $coisas_vot_total->num_rows;

		$conexao = new mysql();
		$coisas = $conexao->Executar("SELECT * FROM enquete_resposta WHERE enquete_codigo='$codigo'");
		$n = 0;
		while($data = $coisas->fetch_object()){

			$relatorio[$n]['resposta'] = $data->resposta;
			$relatorio[$n]['codigo'] = $data->codigo;

			$conexao = new mysql();
			$coisas_vot = $conexao->Executar("SELECT id FROM enquete_voto WHERE codigo_enquete='$codigo' AND codigo_resposta='$data->codigo' ");
			$linhas_vot = $coisas_vot->num_rows;

			$relatorio[$n]['votos'] = $linhas_vot;

			if($linhas_vot != 0){
				$porcento = ($linhas_vot / $linhas_vot_total) * 100;
				$porcento = $trata->trata_valor_calculo($porcento);
			} else {
				$porcento = 0;
			}

			$relatorio[$n]['votos_porc'] = $porcento;

		$n++;
		}
		$dados['relatorio'] = $relatorio;



 		
		$this->view('enquetes.alterar', $dados);
	}


	public function alterar_grv(){
		
		$codigo = $this->post('codigo');

		$enquete = $this->post('enquete');
		$status = $this->post('status');

		$this->valida($enquete);

		$db = new mysql();
		$db->alterar("enquete", array(
			"enquete"=>"$enquete",
			"status"=>"$status"
		), " codigo='$codigo' ");
	 	

		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);		
	}
  	

  	public function nova_resposta(){
		
		$dados['_base'] = $this->base_layout();

		$codigo = $this->get('codigo');

		$dados['codigo'] = $codigo;

		$this->view('enquetes.resposta.nova', $dados);
	}


 	public function nova_resposta_grv(){

 		$codigo_enquete = $this->post('codigo');
 		$resposta = $this->post('resposta');

 		$codigo = $this->gera_codigo();

		$this->valida($resposta);
		$this->valida($codigo);

		// Grava informações no banco
		$conexao = new mysql();
		$conexao->inserir("enquete_resposta", array(			 
			"codigo"=>"$codigo",
			"enquete_codigo"=>"$codigo_enquete",
			"resposta"=>"$resposta"
		));

 		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo_enquete.'/aba/respostas');
 	} 
 	 

 	public function alterar_resposta(){ 
		
		$dados['_base'] = $this->base_layout();

 		$codigo_enquete = $this->get('codigo_enquete');
 		$codigo = $this->get('codigo');

 		$dados['codigo_enquete'] = $codigo_enquete;
 		$dados['codigo'] = $codigo;

 		$db = new mysql();
		$exec = $db->executar("SELECT * FROM enquete_resposta WHERE codigo='$codigo' ");
		$dados['data'] = $exec->fetch_object();		 


		$this->view('enquetes.resposta.alterar', $dados);
	}


	public function alterar_resposta_grv(){

 		$codigo_enquete = $this->post('codigo_enquete');
 		$codigo = $this->post('codigo');
 		$resposta = $this->post('resposta');

		$this->valida($resposta);
		$this->valida($codigo);
		$this->valida($codigo_enquete);

		// Grava informações no banco
		$conexao = new mysql();
		$conexao->alterar("enquete_resposta", array(
			"resposta"=>"$resposta"
		), " codigo='$codigo' ");

 		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo_enquete.'/aba/respostas');
 	}


 	public function apagar_resposta(){

 		$codigo_enquete = $this->get('codigo_enquete');
 		$codigo = $this->get('codigo'); 

		$this->valida($codigo);
		$this->valida($codigo_enquete);
 		
		$conexao = new mysql();
		$conexao->apagar("enquete_resposta", " codigo='$codigo' ");

 		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo_enquete.'/aba/respostas');
 	}


	public function apagar_varios(){
		
		$db = new mysql();
		$exec = $db->Executar("SELECT * FROM enquete ");
		while($data = $exec->fetch_object()){
			
			if($this->post('apagar_'.$data->id) == 1){

				$conexao = new mysql();
				$conexao->apagar("enquete_resposta", " codigo_enquete='$data->codigo' ");

				$conexao = new mysql();
				$conexao->apagar("enquete", " codigo='$data->codigo' ");
				
			}
		}

		$this->irpara(DOMINIO.$this->_controller);
		
	}


}