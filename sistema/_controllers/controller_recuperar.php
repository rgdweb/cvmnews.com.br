<?php

class recuperar extends controller {

    public function init(){
        // Inicialização, se precisar faça aqui
    }

    // Método valida compatível com o controller pai
    protected function valida($var, $msg = null){
        if(!$var){
            if($msg){
                $this->msg($msg);
                $this->volta(1);
            } else {
                $this->msg('Parâmetro inválido!');
                $this->volta(1);
            }
            exit;
        }
        return trim($var);
    }

    protected function inicial(){
        $logo = new model_logo();
        $dados['_logo'] = $logo->endereco;
        $dados['_logo_b'] = $logo->endereco2;
        $this->view('recuperar', $dados);
    }

    protected function enviar(){
        $valida = new model_valida();

        $email = $this->post('email');
        if(!$valida->email($email)){
            $this->msg('E-mail inválido!');
            $this->volta(1);
            exit;
        }

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM adm_usuario WHERE email_recuperacao = ?", "s", [$email]);

        if($exec && $exec->num_rows != 0){
            $lista_de_contas = '';
            $conta_n = 0;

            while($data_users = $exec->fetch_object()){
                $rand = rand(1000, 10000);
                $cod_recuperacao = base64_encode($data_users->codigo . $rand);

                $db->alterar("adm_usuario", [
                    "recuperacao" => $cod_recuperacao
                ], "id = ?", "i", [$data_users->id]);

                $conta_n++;
                $link_recuperacao = DOMINIO . "recuperar/alterar_senha/key/" . $cod_recuperacao;

                $lista_de_contas .= "
                <div style='font-size:13px; color:#000;'><p></p></div>
                <div style='font-size:13px; color:#000;'><p>---</p></div>
                <div style='font-size:13px; color:#000;'><p><strong>Link Para Recuperação Conta $conta_n:</strong> $link_recuperacao</p></div>
                ";
            }

            $msg = "
            <div style='font-size:13px; color:#000;'><p><strong>Solicitação de recuperação de senha</strong></p></div>
            <div style='font-size:13px; color:#000;'><p></p></div>
            <div style='font-size:13px; color:#000;'><p>Foram encontradas $conta_n conta(s) vinculadas a este e-mail!</p></div>
            $lista_de_contas
            <div style='font-size:13px; color:#000;'><p>-</p></div>
            <div style='font-size:13px; color:#000;'><p>Caso não tenha solicitado esta recuperação de conta, ignore este e-mail.</p></div>
            <div style='font-size:13px; color:#000;'><p>-</p></div>
            <div style='font-size:13px; color:#000;'><p>Este e-mail foi gerado automaticamente, por favor não responda.</p></div>
            ";

            $enviar = new model_envia_email();
            $enviar->destino($email);
            $enviar->assunto("Alterar Senha");
            $enviar->conteudo($msg);

            if($enviar->enviar()){
                $this->msg('O email de recuperação de conta foi enviado com sucesso!');
            } else {
                $this->msg('Ocorreu um erro ao enviar email, tente novamente mais tarde!');
            }

            $this->irpara(DOMINIO . "autenticacao");

        } else {
            $this->msg('Não encontramos nenhuma conta vinculada a este e-mail!');
            $this->volta(1);
        }
    }

    public function alterar_senha(){
        $key = $this->get('key');
        $this->valida($key);

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM adm_usuario WHERE recuperacao = ?", "s", [$key]);

        if($exec && $exec->num_rows != 0){
            $dados['key'] = $key;
            $this->view('recuperar.alterar.senha', $dados);
        } else {
            $this->msg('Endereço inválido!');
            $this->irpara(DOMINIO);
        }
    }

    public function alterar_senha_grv(){
        $key = $this->post('key');
        $usuario = $this->post('usuario');
        $senha1 = $this->post('senha1');
        $senha2 = $this->post('senha2');

        $this->valida($key);
        $this->valida($usuario);
        $this->valida($senha1);
        $this->valida($senha2);

        if($senha1 !== $senha2){
            $this->msg('As senhas não coincidem!');
            $this->volta(1);
            exit;
        }

        $db = new mysql();
        $exec = $db->executar("SELECT * FROM adm_usuario WHERE recuperacao = ?", "s", [$key]);

        if($exec && $exec->num_rows != 0){
            $usuario_md5 = md5($usuario); // Pode adaptar conforme seu sistema
            $senha_hash = password_hash($senha1, PASSWORD_DEFAULT);

            $exec_usuario = $db->executar("SELECT * FROM adm_usuario WHERE usuario = ?", "s", [$usuario_md5]);

            if($exec_usuario && $exec_usuario->num_rows == 0){
                $db->alterar("adm_usuario", [
                    "usuario" => $usuario_md5,
                    "senha" => $senha_hash,
                    "recuperacao" => ""
                ], "recuperacao = ?", "s", [$key]);

                $this->msg('Senha alterada com sucesso!');
                $this->irpara(DOMINIO);
            } else {
                $this->msg('Este usuário já está sendo utilizado!');
                $this->volta(1);
            }
        } else {
            $this->msg('Algo deu errado!');
            $this->irpara(DOMINIO);
        }
    }
}
