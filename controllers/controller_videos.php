<?php
class videos extends controller {
	
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
		
		//carrega texto e joga para o view
		$db = new model_texto();
		$dados['texto'] = $db->conteudo('153693597068266');	
	 	
		//carrega view e envia dados para a tela
		$this->view('videos', $dados);
	}
	
	public function enviar(){
		
		$dados = array();
		$dados['_base'] = $this->base();
		$dados['objeto'] = DOMINIO.$this->_controller.'/';
		$dados['controller'] = $this->_controller;
		
		$nome = $this->post('nome');
		$email = $this->post('email');
		$fone = $this->post('fone');
		$mensagem = $this->post('msg');
		$captcha = $this->post('g-recaptcha-response');

		if($nome AND $email AND $mensagem){
			
			if($captcha){
				
				$ip = $_SERVER['REMOTE_ADDR'];
				$key = recaptcha_secret;
				$url = 'https://www.google.com/recaptcha/api/siteverify';
				
				// RECAPTCH RESPONSE
				$recaptcha_response = file_get_contents($url.'?secret='.$key.'&response='.$captcha.'&remoteip='.$ip);
				$data = json_decode($recaptcha_response);

				if(isset($data->success) &&  $data->success === true) {
					
				// se tudo certo envia mensagem

					/* mensagem */
					$msg =  "<p style='font-family:Arial,sans-serif; font-size:12px;'><strong>Contato enviado pelo Website</strong></p>";	
					$msg .= "<p style='font-family:Arial,sans-serif; font-size:12px;'><strong>Nome:</strong> ".$nome."</p>";
					$msg .= "<p style='font-family:Arial,sans-serif; font-size:12px;'><strong>E-mail:</strong> ".$email."</p>";
					$msg .= "<p style='font-family:Arial,sans-serif; font-size:12px;'><strong>Telefone:</strong> ".$fone."</p>";
					$msg .= "<p style='font-family:Arial,sans-serif; font-size:12px;'><strong>Mensagem:</strong> ".$mensagem."</p>";
					
					$db = new mysql();
					$exec = $db->executar("select * from videos ");
					$lista_envio = array();
					$n = 0;
					while($data = $exec->fetch_object()){
						$lista_envio[$n] = $data->email;
						$n++;
					}
					
					$envio = new model_envio();
				//$retorno_envio = $envio->enviar($assunto, $msg, $emails_destino, $email_resposta);
					echo $envio->enviar("Contato Loja Virtual", $msg, $lista_envio, $email);
					exit;

				} else {
					echo "Captcha inválido, tente novamente!";
					exit;
				}
			} else {
				echo "Captcha inválido, tente novamente!";
				exit;
			}
		} else {
			echo "Preencha todos os campos para continuar";
			exit;
		}
	}
	
}