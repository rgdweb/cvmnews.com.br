<?php
class cadastrar_email extends controller {
	
	public function init(){		
	}
	
	public function inicial(){
		
		$nome = $this->get('nome');
		$email = $this->get('email');
		
		if(!$nome){
			echo "<div class='retorno_news'>Preencha corretamente seu nome</div>";
			exit;
		}
		if(!$email){
			echo "<div class='retorno_news'>Preencha corretamente seu e-mail</div>";
			exit;
		}
		
		$valida = new model_valida();
		if(!$valida->email($email)){					
			echo "<div class='retorno_news'>Preencha corretamente seu e-mail</div>";
			exit;		
		} else {
			
			$conexao = new mysql();
			$coisas = $conexao->Executar("select * from cadastro_email where email='$email' ");
			$linhas = $coisas->num_rows;
			
			if($linhas == 0){
				
				$conexao = new mysql();
				$conexao->inserir("cadastro_email", array(
					"nome"=>"$nome",
					"email"=>"$email",
					"interesse"=>"Receber Novidades"
				));
				
			}
			
			echo "<div class='retorno_news'>Obrigado por se cadastrar!<br><br>Em breve recebera nossas novidades por e-mail!</div>";
			exit;

		}		 

	}
	
}