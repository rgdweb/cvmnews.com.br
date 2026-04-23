<?php
class autenticacao extends controller {
	
	public function init(){
		
	}

	public function inicial(){
		
		$dados = array();
		$dados['_base'] = $this->base_layout();
		
		$this->view('entrar', $dados);
	}
	
	protected function login(){
		
		$usuario = $this->post('usuario');
		$senha = $this->post('senha');
		
		$this->valida($usuario, 'Digite o usuário!');
		$this->valida($senha, 'Digite a senha!');		
		
		$username = md5($usuario);
		$password = md5($senha);
		
		$db = new mysql();
		$exec = $db->executar("SELECT codigo, usuario FROM adm_usuario WHERE usuario='$username' AND senha='$password' ");
		
		if($exec->num_rows != 1){
			
			$this->msg('Usuário ou senha incorretos!');
			$this->volta(1);
			exit;
			
		} else {
			
			$data = $exec->fetch_object();
			
			$_SESSION['adm_acesso'] = md5(TOKEN1.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
			$_SESSION['adm_sessao'] = $this->gera_codigo();
			$_SESSION['adm_cod_usuario'] = $data->codigo;
			$_SESSION['adm_usuario'] = $data->usuario;
			
			$this->irpara( DOMINIO );
		}
		
	}
	
	protected function logout(){
		session_destroy();
		$this->irpara( DOMINIO );
	}
		
}