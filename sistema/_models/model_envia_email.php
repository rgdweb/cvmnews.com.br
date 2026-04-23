<?php

Class model_envia_email extends model{

	protected $config_destino = '';
	protected $config_assunto = '';
	protected $config_retorno = '';
	protected $config_conteudo = '';
	protected $config_sucesso = 'Mensagem enviada com sucesso';

	public function __construct(){
		require_once("_api/phpmailer/class.phpmailer.php");
	}

	public function destino($var){
		$this->config_destino = $var;
	}
	public function assunto($var){
		$this->config_assunto = $var;
	}
	public function retorno($var){
		$this->config_retorno = $var;
	}
	public function conteudo($var){
		$this->config_conteudo = $var;
	}	 

	public function enviar(){

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM adm_config WHERE id='1' ");
		$data_config = $exec->fetch_object();

		$destino = $this->config_destino;

		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->Host = $data_config->email_host;
		$mail->Port = $data_config->email_porta;
		$mail->SMTPAuth = true;
		$mail->Username = $data_config->email_usuario;
		$mail->Password = $data_config->email_senha;
		$mail->From = $data_config->email_origem;
		$mail->FromName = $data_config->email_nome;
		$mail->AddAddress($destino, "");
		$mail->WordWrap = 50;
		$mail->IsHTML(true); //enviar em HTML
		$mail->AddReplyTo($this->config_retorno, "");
		$mail->Subject = $this->config_assunto;
		$mail->Body = utf8_decode($this->config_conteudo);
		
		if($mail->Send()){
			return true;
		} else {
			return false;
		}

	}
}