<?php

class usuarios extends controller {
        
        protected $_modulo_nome = "Usuários";
        
        public function init(){
                $this->autenticacao();
                $this->nivel_acesso(1);
        }
        
        public function inicial(){
                $dados['_base'] = $this->base();
                $dados['_titulo'] = $this->_modulo_nome;
                $dados['_subtitulo'] = "";
                $usuarios = new model_usuarios();
                $dados['lista'] = $usuarios->lista();
                $this->view('usuarios', $dados);
        }

        public function novo(){
                $this->nivel_acesso(5);
                $dados['_base'] = $this->base();
                $dados['_titulo'] = $this->_modulo_nome;
                $dados['_subtitulo'] = "Novo";
                $setores = new model_setores();
                $lista = $setores->lista();
                $lista_org = new model_ordena_permissoes();
                $lista_org->monta(0, $lista);
                $dados['lista'] = $lista_org->_lista_certa;
                $this->view('usuarios.novo', $dados);
        }       
        
        public function novo_grv(){
                $this->nivel_acesso(5);
                $nome = $this->post('nome');
                $email = $this->post('email');
                $usuario = $this->post('usuario_sis');
                $senha = $this->post('senha_sis');
                $this->valida($nome);   
                $this->valida($usuario);
                $this->valida($senha);
                $usuarios = new model_usuarios();
                if(!$usuarios->confere_usuario($usuario)){
                        $this->msg('Este usuário já esta sendo utilizado!');
                        $this->volta(1);
                }
                $usuario_md5 = md5($usuario);
                $senha_md5 = md5($senha);               
                $codigo = $this->gera_codigo();
                $usuarios->adicionar(array($codigo, $nome, $email, $usuario_md5, $senha_md5));
                $setores = new model_setores();
                $lista_setores = $setores->lista();
                $ordem = array();
                foreach ($lista_setores as $key => $value) {
                        $id = $value['id'];
                        if( $this->post('setor_'.$id) ){
                                $usuarios->adiciona_usuario_setor($codigo, $id);
                                if( $value['id_pai'] == 0 ){
                                        if(!in_array($id, $ordem)){
                                                array_push($ordem, $id);
                                        }
                                }
                        }
                }
                $ordem = implode(",", $ordem);
                $perfil = new model_perfil();
                $perfil->alterar_ordem_menu($ordem, $codigo);
                $this->irpara(DOMINIO.$this->_controller);
        }

        public function alterar(){
                $dados['_base'] = $this->base();
                $dados['_titulo'] = $this->_modulo_nome;
                $dados['_subtitulo'] = "Alterar";
                $codigo = $this->get('codigo');
                $usuarios = new model_usuarios();
                $setores = new model_setores();
                $dados['data'] = $usuarios->selecionar($codigo);
                $lista_setores_todos = $setores->lista();
                $lista_setores = array();
                $i = 0;
                foreach ($lista_setores_todos as $key => $value) {
                        $lista_setores[$i]['id'] = $value['id'];
                        $lista_setores[$i]['id_pai'] = $value['id_pai'];
                        $lista_setores[$i]['titulo'] = $value['titulo'];                        
                        if($usuarios->confere_acesso($codigo, $value['id'])){
                                $lista_setores[$i]['check'] = true;
                        } else {
                                $lista_setores[$i]['check'] = false;
                        } 
                $i++;
                }
                $lista_org = new model_ordena_permissoes();
                $lista_org->monta(0, $lista_setores);
                $dados['permissoes'] = $lista_org->_lista_certa;
                $this->view('usuarios.alterar', $dados);
        }
        
        // -------------------------- FUNÇÃO CORRIGIDA --------------------------
        public function alterar_grv(){
                
                $this->nivel_acesso(5);

                $codigo = $this->post('codigo');
                $nome = $this->post('nome');
                $email = $this->post('email');
                $usuario = $this->post('usuario_sis');
                $senha = $this->post('senha_sis');
        
                $this->valida($nome);

                $usuarios = new model_usuarios();

                // Se um nome de usuário foi digitado, faz a verificação antes de qualquer outra coisa.
                if ($usuario) {
                        if (!$usuarios->confere_usuario($usuario, $codigo)) {
                                $this->msg('Este usuário já esta sendo utilizado!');
                                $this->volta(1);
                                exit; // Para a execução imediatamente se o usuário já existir.
                        }
                }

                // Agora, o resto da lógica para salvar os dados
                $usuario_md5 = ($usuario) ? md5($usuario) : "";
                $senha_md5 = ($senha) ? md5($senha) : "";

                // Esta forma simplificada de chamar o alterar funciona porque o seu model
                // já está preparado para ignorar campos vazios de usuário e senha.
                $usuarios->alterar(array($nome, $email, $usuario_md5, $senha_md5), $codigo);


                // --- A lógica de permissões e de ordem do menu continua a mesma ---
                $setores = new model_setores();
                $perfil = new model_perfil();
                $lista_setores_todos = $setores->lista();
                
                $ordem = $perfil->ordem($codigo);         
                if($ordem){ $ordem = explode(',', $ordem); } else { $ordem = array(); }
                
                foreach ($lista_setores_todos as $key => $value) {
                        $confere = $usuarios->confere_acesso($codigo, $value['id']);
                        if( $this->post('setor_'.$value['id']) ){
                                if(!$confere){ $usuarios->adiciona_usuario_setor($codigo, $value['id']); }
                                if( $value['id_pai'] == 0 ){ if(!in_array($value['id'], $ordem)){ array_push($ordem, $value['id']); } }
                        } else {
                                if($confere){ $usuarios->remove_usuario_setor($codigo, $value['id']); }
                                if( $value['id_pai'] == 0 ){ if(in_array($value['id'], $ordem)){ $key = array_search($value['id'], $ordem); if($key!==false){ unset($ordem[$key]); } } }
                        }
                }
                $ordem = implode(",", $ordem);
                $perfil->alterar_ordem_menu($ordem, $codigo);
                // --- Fim da lógica de permissões ---

                $this->irpara(DOMINIO.$this->_controller);
        }
        // ----------------------------------------------------------------------
        
        public function apagar_varios(){
                $this->nivel_acesso(4);
                $usuarios = new model_usuarios();
                $lista = $usuarios->lista();
                foreach ($lista as $key => $value) {
                        if($this->post('apagar_'.$value['id']) == 1){
                                $usuarios->apagar( $value['codigo'] );
                        }
                }
                $this->irpara(DOMINIO.$this->_controller);
        }
}