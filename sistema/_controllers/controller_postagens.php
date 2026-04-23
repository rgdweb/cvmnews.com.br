<?php
class postagens extends controller{
	protected $_modulo_nome="Postagens";
	public function init(){$this->autenticacao();$this->nivel_acesso(27);}
	public function inicial(){
		$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="";
		$dados['acesso_mascara']=$this->nivel_acesso(30,false);
		$dados['acesso_destaques']=$this->nivel_acesso(34,false);
		$dados['acesso_categorias']=$this->nivel_acesso(35,false);
		$dados['acesso_autores']=$this->nivel_acesso(58,false);
		$grupo=$this->get('grupo');$dados['grupo']=$grupo;
		$postagens=new model_postagens();$dados['categorias']=$postagens->grupos($grupo);
		if($grupo)$postagens->categoria=$grupo;
		$postagens->ordem='desc';$postagens_retorno=$postagens->lista();$dados['noticias']=$postagens_retorno['noticias'];
		$this->view('postagens',$dados);
	}
	public function nova(){
		$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Novo";$dados['aba_selecionada']="dados";
		$dados['hora']=date('H',time());$dados['minuto']=date('i',time());$dados['dia']=date('d',time());$dados['mes']=date('m',time());$dados['ano']=date('Y',time());
		$grupo=$this->get('grupo');$dados['grupo']=$grupo;
		$postagens=new model_postagens();$dados['categorias']=$postagens->grupos($grupo);$dados['autores']=$postagens->autores();$dados['destaques']=$postagens->destaques();
		$this->view('postagens.nova',$dados);
	}
	public function nova_grv(){
		$titulo=$this->post('titulo');$autor=$this->post('autor');$previa=$this->post('previa');$conteudo=$_POST['conteudo'];$categoria=$this->post('grupo');$destaque=$this->post('destaque');$hora=$this->post('hora');$dia=$this->post('dia');
		$arraydata=explode("/",$dia);$hora_montada=$arraydata[2]."-".$arraydata[1]."-".$arraydata[0]." ".$hora.":00";$data_final=strtotime($hora_montada);
		$this->valida($titulo);$this->valida($categoria);$codigo=$this->gera_codigo();
		$db=new mysql();$db->inserir("noticia",array("codigo"=>"$codigo","categoria"=>"$categoria","data"=>"$data_final","titulo"=>"$titulo","autor"=>"$autor","previa"=>"$previa","conteudo"=>"$conteudo","destaque"=>"$destaque"));
		$this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$codigo);
	}
	public function alterar(){
		$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Alterar";$codigo=$this->get('codigo');
		$aba=$this->get('aba');$dados['aba_selecionada']=$aba?$aba:'dados';
		$db=new mysql();$exec=$db->Executar("SELECT * FROM noticia where codigo='$codigo' ");$dados['data']=$exec->fetch_object();
		$dados['hora']=date('H',$dados['data']->data);$dados['minuto']=date('i',$dados['data']->data);$dados['dia']=date('d',$dados['data']->data);$dados['mes']=date('m',$dados['data']->data);$dados['ano']=date('Y',$dados['data']->data);
		$postagens=new model_postagens();$dados['categorias']=$postagens->grupos($dados['data']->categoria);$dados['autores']=$postagens->autores($dados['data']->autor);$dados['destaques']=$postagens->destaques($dados['data']->destaque);$dados['audios']=$postagens->audios($dados['data']->codigo);
		$imagens=$postagens->imagens($dados['data']->codigo);$dados['imagens']=$imagens['lista'];
		$this->view('postagens.alterar',$dados);
	}
	public function alterar_grv(){
		$codigo=$this->post('codigo');$titulo=$this->post('titulo');$autor=$this->post('autor');$previa=$this->post('previa');$conteudo=$_POST['conteudo'];$categoria=$this->post('grupo');$destaque=$this->post('destaque');$hora=$this->post('hora');$dia=$this->post('dia');
		$arraydata=explode("/",$dia);$hora_montada=$arraydata[2]."-".$arraydata[1]."-".$arraydata[0]." ".$hora.":00";$data_final=strtotime($hora_montada);
		$this->valida($titulo);$this->valida($categoria);
		$db=new mysql();$db->alterar("noticia",array("categoria"=>"$categoria","data"=>"$data_final","titulo"=>"$titulo","autor"=>"$autor","previa"=>"$previa","conteudo"=>"$conteudo","destaque"=>"$destaque")," codigo='$codigo' ");
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo);
	}
	public function apagar_imagem(){
		$codigo=$this->get('codigo');$id=$this->get('id');
		if($id){
			$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_imagem where id='$id' ");$data=$exec->fetch_object();
			if($data->imagem){unlink('arquivos/img_postagens_g/'.$codigo.'/'.$data->imagem);unlink('arquivos/img_postagens_p/'.$codigo.'/'.$data->imagem);}
			$conexao=new mysql();$conexao->apagar("noticia_imagem"," id='$id' ");
		}
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}
	public function ordenar_imagem(){
		$codigo=$this->post('codigo');$list=$this->post('list');$this->valida($codigo);$this->valida($list);$output=array();parse_str($list,$output);$ordem=implode(',',$output['item']);
		$db=new mysql();$db->inserir("noticia_imagem_ordem",array("codigo"=>"$codigo","data"=>"$ordem"));
	}
	public function legenda(){
		$dados['_base']=$this->base_layout();$id=$this->get('id');$codigo=$this->get('codigo');$dados['codigo']=$codigo;$dados['id']=$id;
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_imagem_legenda where id_img='$id' ");$data=$exec->fetch_object();
		$dados['legenda']=isset($data->id)?$data->legenda:'';
		$this->view('postagens.legenda',$dados);
	}
	public function legenda_grv(){
		$id=$this->post('id');$legenda=$this->post('legenda');$codigo=$this->post('codigo');
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_imagem_legenda where id_img='$id' ");$data=$exec->fetch_object();
		if(isset($data->id)){
			$db=new mysql();$db->alterar("noticia_imagem_legenda",array("legenda"=>"$legenda")," id='$data->id' ");
		}else{
			$db=new mysql();$db->inserir("noticia_imagem_legenda",array("id_img"=>"$id","legenda"=>"$legenda"));
		}
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
	}
	public function novo_audio(){
		$codigo=$this->get('codigo');$titulo=$this->post('titulo');$arquivo_original=$_FILES['arquivo'];$tmp_name=$_FILES['arquivo']['tmp_name'];
		$this->valida($titulo);$this->valida($codigo);$arquivo=new model_arquivos_imagens();$diretorio="arquivos/audios/$codigo/";
		if(!is_dir($diretorio))mkdir($diretorio);
		if(!$arquivo->filtro($arquivo_original)){$this->msg('Arquivo com formato inválido ou inexistente!');$this->volta(1);}else{
			$nome_original=$_FILES['arquivo']['name'];$nome_arquivo=$arquivo->trata_nome($nome_original);$destino=$diretorio.$nome_arquivo;
			if(copy($tmp_name,$destino)){
				$conexao=new mysql();$conexao->inserir("noticia_audio",array("codigo"=>"$codigo","arquivo"=>"$nome_arquivo","titulo"=>"$titulo"));
				$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/audios');
			}else{$this->msg('Não foi possível copiar o arquivo!');$this->volta(1);}
		}
	}
	public function apagar_audio(){
		$codigo=$this->get('codigo');$id=$this->get('id');
		if($id){
			$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_audio where id='$id' ");$data=$exec->fetch_object();
			if(isset($data->arquivo))unlink('arquivos/audios/'.$codigo.'/'.$data->arquivo);
			$conexao=new mysql();$conexao->apagar("noticia_audio"," id='$id' ");
		}
		$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/audios');
	}
	public function apagar_varios(){
		$db=new mysql();$exec=$db->Executar("SELECT * FROM noticia ");
		while($data=$exec->fetch_object()){
			if($this->post('apagar_'.$data->id)==1){
				$db=new mysql();$exec_audio=$db->executar("SELECT * FROM noticia_audio where codigo='$data->codigo' ");
				while($data_audio=$exec_audio->fetch_object()){
					if(isset($data_audio->arquivo))unlink('arquivos/audios/'.$data->codigo.'/'.$data_audio->arquivo);
				}
				$conexao=new mysql();$conexao->apagar("noticia_audio"," codigo='$data->codigo' ");
				$db=new mysql();$exec_img=$db->executar("SELECT * FROM noticia_imagem where codigo='$data->codigo' ");
				while($data_img=$exec_img->fetch_object()){
					if($data_img->imagem){unlink('arquivos/img_postagens_g/'.$data->codigo.'/'.$data_img->imagem);unlink('arquivos/img_postagens_p/'.$data->codigo.'/'.$data_img->imagem);}
				}
				$conexao=new mysql();$conexao->apagar("noticia_imagem"," codigo='$data->codigo' ");
				$conexao=new mysql();$conexao->apagar("noticia"," codigo='$data->codigo' ");
			}
		}
		$this->irpara(DOMINIO.$this->_controller);
	}
	public function grupos(){
		$this->nivel_acesso(35);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Categorias";
		$categorias=new model_postagens();$dados['categorias']=$categorias->grupos();
		$this->view('postagens.categorias',$dados);
	}
	public function novo_grupo(){
		$this->nivel_acesso(35);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Nova Categoria";
		$this->view('postagens.categorias.nova',$dados);
	}
	public function novo_grupo_grv(){
		$this->nivel_acesso(35);$titulo=$this->post('titulo');$this->valida($titulo);$codigo=$this->gera_codigo();
		$db=new mysql();$db->inserir("noticia_grupo",array("codigo"=>"$codigo","titulo"=>"$titulo"));
		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}
	public function alterar_grupo(){
		$this->nivel_acesso(35);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Alterar Grupo";$codigo=$this->get('codigo');
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_grupo WHERE codigo='$codigo' ");$dados['data']=$exec->fetch_object();
		if(!isset($dados['data']))$this->irpara(DOMINIO.$this->_controller.'/grupos');
		$this->view('postagens.categorias.alterar',$dados);
	}
	public function alterar_grupo_grv(){
		$this->nivel_acesso(35);$codigo=$this->post('codigo');$titulo=$this->post('titulo');$this->valida($codigo);$this->valida($titulo);
		$db=new mysql();$db->alterar("noticia_grupo",array("titulo"=>"$titulo")," codigo='$codigo' ");
		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}
	public function apagar_grupos(){
		$this->nivel_acesso(35);$db=new mysql();$exec=$db->Executar("SELECT * FROM noticia_grupo ");
		while($data=$exec->fetch_object()){
			if($this->post('apagar_'.$data->id)==1){
				$conexao=new mysql();$conexao->apagar("noticia_grupo"," id='$data->id' ");
			}
		}
		$this->irpara(DOMINIO.$this->_controller.'/grupos');
	}
	public function destaques(){
		$this->nivel_acesso(34);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Destaques";
		$lista=new model_postagens();$dados['destaques']=$lista->destaques();
		$this->view('postagens.destaques',$dados);
	}
	public function novo_destaque(){
		$this->nivel_acesso(34);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Nova Destaque";
		$this->view('postagens.destaques.novo',$dados);
	}
	public function novo_destaque_grv(){
		$this->nivel_acesso(34);$titulo=$this->post('titulo');$this->valida($titulo);$codigo=$this->gera_codigo();
		$db=new mysql();$db->inserir("noticia_destaque",array("codigo"=>"$codigo","titulo"=>"$titulo"));
		$this->irpara(DOMINIO.$this->_controller.'/destaques');
	}
	public function alterar_destaque(){
		$this->nivel_acesso(34);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Alterar Destaque";$codigo=$this->get('codigo');
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_destaque WHERE codigo='$codigo' ");$dados['data']=$exec->fetch_object();
		if(!isset($dados['data']))$this->irpara(DOMINIO.$this->_controller.'/destaques');
		$this->view('postagens.destaques.alterar',$dados);
	}
	public function alterar_destaque_grv(){
		$this->nivel_acesso(34);$codigo=$this->post('codigo');$titulo=$this->post('titulo');$this->valida($codigo);$this->valida($titulo);
		$db=new mysql();$db->alterar("noticia_destaque",array("titulo"=>"$titulo")," codigo='$codigo' ");
		$this->irpara(DOMINIO.$this->_controller.'/destaques');
	}
	public function apagar_destaques(){
		$this->nivel_acesso(34);$db=new mysql();$exec=$db->Executar("SELECT * FROM noticia_destaque ");
		while($data=$exec->fetch_object()){
			if($this->post('apagar_'.$data->id)==1){
				$conexao=new mysql();$conexao->apagar("noticia_destaque"," id='$data->id' ");
			}
		}
		$this->irpara(DOMINIO.$this->_controller.'/destaques');
	}
	public function mascara(){
		$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Marca d'água";
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_marcadagua WHERE id='1' ");$data_masc=$exec->fetch_object();
		$lista=new model_mascara();$dados['lista']=$lista->lista($data_masc->codigo);
		$this->view('postagens.mascara',$dados);
	}
	public function mascara_grv(){
		$codigo=$this->post('codigo');
		$db=new mysql();$db->alterar("noticia_marcadagua",array("codigo"=>"$codigo")," id='1' ");
		$this->irpara(DOMINIO.$this->_controller.'/mascara');
	}
	public function upload(){
		$dados['_base']=$this->base_layout();$codigo=$this->get('codigo');$dados['codigo']=$codigo;
		$this->view('enviar_imagens',$dados);
	}
	public function imagem_redimencionada(){
		$codigo=$this->post('codigo');$pasta="postagens";$diretorio_g="arquivos/img_".$pasta."_g/".$codigo."/";$diretorio_p="arquivos/img_".$pasta."_p/".$codigo."/";
		if(!is_dir($diretorio_g))mkdir($diretorio_g);
		if(!is_dir($diretorio_p))mkdir($diretorio_p);
		$img=new model_arquivos_imagens();$imagem=$_POST['imagem'];$nome_original=$this->post('nomeimagem');$nome_foto=$img->trata_nome($nome_original);$extensao=$img->extensao($nome_original);
		list($tipo,$dados)=explode(';',$imagem);list(,$tipo)=explode(':',$tipo);list(,$dados)=explode(',',$dados);$dados=base64_decode($dados);$nome=md5(uniqid(time()));
		if(file_put_contents($diretorio_g.$nome_foto,$dados)){
			if(($extensao=="jpg")||($extensao=="jpeg")||($extensao=="JPG")||($extensao=="JPEG")){
				copy($diretorio_g.$nome_foto,$diretorio_p.$nome_foto);
				$largura_g=1200;$altura_g=$img->calcula_altura_jpg($tmp_name,$largura_g);$largura_p=300;$altura_p=$img->calcula_altura_jpg($tmp_name,$largura_p);
				$img->jpg($diretorio_g.$nome_foto,$largura_g,$altura_g,$diretorio_g.$nome_foto);$img->jpg($diretorio_p.$nome_foto,$largura_p,$altura_p,$diretorio_p.$nome_foto);
			}else{
				copy($diretorio_g.$nome_foto,$diretorio_p.$nome_foto);
			}
			$conexao=new mysql();$coisas=$conexao->Executar("SELECT * FROM noticia_marcadagua where id='1' ");$data=$coisas->fetch_object();
			if($data->codigo){$mascara=new model_mascara();$mascara->aplicar($data->codigo,$diretorio_g.$nome_foto);}
			$db=new mysql();$db->inserir("noticia_imagem",array("codigo"=>"$codigo","imagem"=>"$nome_foto"));$ultid=$db->ultimo_id();
			$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_imagem_ordem where codigo='$codigo' order by id desc limit 1");$data=$exec->fetch_object();
			$novaordem=isset($data->data)?$data->data.",".$ultid:$ultid;
			$db=new mysql();$db->inserir("noticia_imagem_ordem",array("codigo"=>"$codigo","data"=>"$novaordem"));
		}
	}
	public function imagem_manual(){
		$arquivo=$_FILES['arquivo'];$tmp_name=$_FILES['arquivo']['tmp_name'];$codigo=$this->get('codigo');$nome_original=$_FILES['arquivo']['name'];
		$pasta="postagens";$diretorio_g="arquivos/img_".$pasta."_g/".$codigo."/";$diretorio_p="arquivos/img_".$pasta."_p/".$codigo."/";
		if(!is_dir($diretorio_g))mkdir($diretorio_g);
		if(!is_dir($diretorio_p))mkdir($diretorio_p);
		$img=new model_arquivos_imagens();
		if($tmp_name){
			$nome_foto=$img->trata_nome($nome_original);$extensao=$img->extensao($nome_original);
			if(copy($tmp_name,$diretorio_g.$nome_foto)){
				if(($extensao=="jpg")||($extensao=="jpeg")||($extensao=="JPG")||($extensao=="JPEG")){
					$largura_g=1000;$altura_g=$img->calcula_altura_jpg($tmp_name,$largura_g);$largura_p=300;$altura_p=$img->calcula_altura_jpg($tmp_name,$largura_p);
					$img->jpg($diretorio_g.$nome_foto,$largura_g,$altura_g,$diretorio_g.$nome_foto);$img->jpg($diretorio_g.$nome_foto,$largura_p,$altura_p,$diretorio_p.$nome_foto);
				}else{
					copy($diretorio_g.$nome_foto,$diretorio_p.$nome_foto);
				}
				$conexao=new mysql();$coisas=$conexao->Executar("SELECT * FROM noticia_marcadagua where id='1' ");$data=$coisas->fetch_object();
				if($data->codigo){$mascara=new model_mascara();$mascara->aplicar($data->codigo,$diretorio_g.$nome_foto);}
				$db=new mysql();$db->inserir("noticia_imagem",array("codigo"=>"$codigo","imagem"=>"$nome_foto"));$ultid=$db->ultimo_id();
				$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_imagem_ordem where codigo='$codigo' order by id desc limit 1");$data=$exec->fetch_object();
				$novaordem=isset($data->data)?$data->data.",".$ultid:$ultid;
				$db=new mysql();$db->inserir("noticia_imagem_ordem",array("codigo"=>"$codigo","data"=>"$novaordem"));
			}else{
				$this->msg('Erro ao gravar imagem!');
			}
			$this->irpara(DOMINIO.$this->_controller."/alterar/codigo/".$codigo."/aba/imagem");
		}
	}
	public function autores(){
		$this->nivel_acesso(58);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Autores";
		$lista=new model_postagens();$dados['lista']=$lista->autores();
		$this->view('postagens.autores',$dados);
	}
	public function novo_autor(){
		$this->nivel_acesso(58);$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Nova Autor";
		$this->view('postagens.autores.novo',$dados);
	}
	public function novo_autor_grv(){
		$this->nivel_acesso(58);$nome=$this->post('nome');$descricao=$_POST['descricao'];$this->valida($nome);$codigo=$this->gera_codigo();
		$db=new mysql();$db->inserir("noticia_autores",array("codigo"=>"$codigo","nome"=>"$nome","descricao"=>"$descricao"));
		$this->irpara(DOMINIO.$this->_controller.'/alterar_autor/codigo/'.$codigo.'/aba/imagem/');
	}
	public function alterar_autor(){
		$this->nivel_acesso(58);$aba=$this->get('aba');$dados['aba_selecionada']=$aba?$aba:'dados';
		$dados['_base']=$this->base_layout();$dados['_titulo']=$this->_modulo_nome;$dados['_subtitulo']="Alterar Autores";$codigo=$this->get('codigo');
		$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_autores WHERE codigo='$codigo' ");$dados['data']=$exec->fetch_object();
		if(!isset($dados['data']))$this->irpara(DOMINIO.$this->_controller.'/autores');
		$this->view('postagens.autores.alterar',$dados);
	}
	public function alterar_autor_grv(){
		$this->nivel_acesso(58);$codigo=$this->post('codigo');$nome=$this->post('nome');$descricao=$_POST['descricao'];$this->valida($codigo);$this->valida($nome);
		$db=new mysql();$db->alterar("noticia_autores",array("nome"=>"$nome","descricao"=>"$descricao")," codigo='$codigo' ");
		$this->irpara(DOMINIO.$this->_controller.'/autores');
	}
	public function alterar_autor_imagem(){
		$this->nivel_acesso(58);$arquivo_original=$_FILES['arquivo'];$tmp_name=$_FILES['arquivo']['tmp_name'];$arquivo=new model_arquivos_imagens();$codigo=$this->get('codigo');$diretorio="arquivos/imagens/";
		if(!$arquivo->filtro($arquivo_original)){$this->msg('Arquivo com formato inválido ou inexistente!');$this->volta(1);}else{
			$nome_original=$arquivo_original['name'];$extensao=$arquivo->extensao($nome_original);$nome_foto=$arquivo->trata_nome($nome_original);
			if(copy($tmp_name,$diretorio.$nome_foto)){
				$db=new mysql();$db->alterar("noticia_autores",array("imagem"=>"$nome_foto")," codigo='$codigo' ");
			}else{
				$this->msg('Erro ao gravar imagem!');
			}
			$this->irpara(DOMINIO.$this->_controller."/alterar_autor/codigo/".$codigo."/aba/imagem");
		}
	}
	public function apagar_autor_imagem(){
		$this->nivel_acesso(58);$codigo=$this->get('codigo');
		if($codigo){
			$db=new mysql();$exec=$db->executar("SELECT * FROM noticia_autores where codigo='$codigo' ");$data=$exec->fetch_object();
			if($data->imagem)unlink('arquivos/imagens/'.$data->imagem);
			$db=new mysql();$db->alterar("noticia_autores",array("imagem"=>"")," codigo='$codigo' ");
		}
		$this->irpara(DOMINIO.$this->_controller.'/alterar_autor/codigo/'.$codigo.'/aba/imagem');
	}
	public function apagar_autores(){
		$this->nivel_acesso(58);$db=new mysql();$exec=$db->Executar("SELECT * FROM noticia_autores ");
		while($data=$exec->fetch_object()){
			if($this->post('apagar_'.$data->id)==1){
				if($data->imagem)unlink('arquivos/imagens/'.$data->imagem);
				$conexao=new mysql();$conexao->apagar("noticia_autores"," id='$data->id' ");
			}
		}
		$this->irpara(DOMINIO.$this->_controller.'/autores');
	}
}