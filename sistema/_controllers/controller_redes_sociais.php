<?php
class redes_sociais extends controller {
protected $_modulo_nome="Redes Sociais";
public function init(){$this->autenticacao();$this->nivel_acesso(46);}
public function inicial(){
$dados['_base']=$this->base_layout();
$dados['_titulo']=$this->_modulo_nome;
$dados['_subtitulo']="";
$dados['permissao']=true;
$lista=[];
$db=new mysql();
$ordem=$db->Executar("SELECT * FROM rede_social_ordem ORDER BY id DESC LIMIT 1")->fetch_object();
if(!empty($ordem->data)){
$ids=explode(",",$ordem->data);
foreach($ids as $id){
$id=(int)$id;
if($id>0){
$item=$db->Executar("SELECT * FROM rede_social WHERE id='$id'")->fetch_object();
if(!empty($item->id)){
$lista[]=['id'=>$item->id,'codigo'=>$item->codigo,'titulo'=>$item->titulo,'imagem'=>$item->imagem];
}
}
}
}
$dados['lista']=$lista;
$this->view('redes_sociais',$dados);
}
public function novo(){
$dados['_base']=$this->base_layout();
$dados['_titulo']=$this->_modulo_nome;
$dados['_subtitulo']="Novo";
$dados['aba_selecionada']="dados";
$this->view('redes_sociais.novo',$dados);
}
public function nova_grv(){
$titulo=filter_input(INPUT_POST,'titulo',FILTER_SANITIZE_STRING);
$endereco=filter_input(INPUT_POST,'endereco',FILTER_SANITIZE_STRING);
$this->valida($titulo);
$codigo=$this->gera_codigo();
$db=new mysql();
$db->inserir("rede_social",["codigo"=>$codigo,"titulo"=>$titulo,"endereco"=>$endereco]);
$novo_id=$db->ultimo_id();
$ordem=$db->Executar("SELECT * FROM rede_social_ordem ORDER BY id DESC LIMIT 1")->fetch_object();
if(!empty($ordem->id)){
$novaordem=$ordem->data?$ordem->data.",".$novo_id:$novo_id;
$db->alterar("rede_social_ordem",["data"=>$novaordem],"id='{$ordem->id}'");
}else{
$db->inserir("rede_social_ordem",["data"=>$novo_id]);
}
$this->irpara(DOMINIO.$this->_controller."/alterar/aba/imagem/codigo/".$codigo);
}
public function alterar(){
$codigo=$this->get('codigo');
$aba=$this->get('aba');
$db=new mysql();
$data=$db->Executar("SELECT * FROM rede_social WHERE codigo='$codigo'")->fetch_object();
$dados['_base']=$this->base_layout();
$dados['_titulo']=$this->_modulo_nome;
$dados['_subtitulo']="Alterar";
$dados['aba_selecionada']=$aba?$aba:"dados";
$dados['data']=$data;
$this->view('redes_sociais.alterar',$dados);
}
public function alterar_grv(){
$codigo=$this->post('codigo');
$titulo=filter_input(INPUT_POST,'titulo',FILTER_SANITIZE_STRING);
$endereco=filter_input(INPUT_POST,'endereco',FILTER_SANITIZE_STRING);
$this->valida($codigo);
$this->valida($titulo);
$db=new mysql();
$db->alterar("rede_social",["titulo"=>$titulo,"endereco"=>$endereco],"codigo='$codigo'");
$this->irpara(DOMINIO.$this->_controller."/alterar/codigo/".$codigo);
}
public function imagem(){
$codigo=$this->get('codigo');
$arquivo_original=$_FILES['arquivo']??null;
if(!$arquivo_original||empty($arquivo_original['tmp_name'])){
$this->msg('Nenhum arquivo enviado!');
$this->volta(1);
}
$arquivo=new model_arquivos_imagens();
if(!$arquivo->filtro($arquivo_original)){
$this->msg('Arquivo inválido!');
$this->volta(1);
}
$diretorio="arquivos/img_redes_sociais/";
$nome_original=$arquivo_original['name'];
$extensao=strtolower($arquivo->extensao($nome_original));
$nome_arquivo=$arquivo->trata_nome($nome_original);
if(copy($arquivo_original['tmp_name'],$diretorio.$nome_arquivo)){
if(in_array($extensao,['jpg','jpeg'])){
$largura=800;
$altura=$arquivo->calcula_altura_jpg($diretorio.$nome_arquivo,$largura);
$arquivo->jpg($diretorio.$nome_arquivo,$largura,$altura,$diretorio.$nome_arquivo);
}
$db=new mysql();
$db->alterar("rede_social",["imagem"=>$nome_arquivo],"codigo='$codigo'");
$this->irpara(DOMINIO.$this->_controller."/alterar/codigo/".$codigo."/aba/imagem");
}else{
$this->msg('Erro ao gravar imagem!');
$this->volta(1);
}
}
public function apagar_imagem(){
$codigo=$this->get('codigo');
$db=new mysql();
$data=$db->Executar("SELECT * FROM rede_social WHERE codigo='$codigo'")->fetch_object();
if(!empty($data->imagem)&&file_exists('arquivos/img_redes_sociais/'.$data->imagem)){
unlink('arquivos/img_redes_sociais/'.$data->imagem);
}
$db->alterar("rede_social",["imagem"=>""],"codigo='$codigo'");
$this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$codigo.'/aba/imagem');
}
public function ordem(){
$ids=$this->post('ids');
if(!$ids){echo json_encode(['status'=>'error','msg'=>'Nenhum ID recebido']);exit;}
$idsArray=explode(',',$ids);
$idsLimpos=array_filter(array_map('intval',$idsArray));
if(empty($idsLimpos)){echo json_encode(['status'=>'error','msg'=>'IDs inválidos']);exit;}
$db=new mysql();
$todosIds=[];
$q=$db->Executar("SELECT id FROM rede_social ORDER BY id");
while($r=$q->fetch_object()){$todosIds[]=(int)$r->id;}
$novaOrdem=$idsLimpos;
foreach($todosIds as $id){
if(!in_array($id,$novaOrdem)){$novaOrdem[]=$id;}
}
$novaOrdemStr=implode(',',$novaOrdem);
$ordemAtual=$db->Executar("SELECT * FROM rede_social_ordem ORDER BY id DESC LIMIT 1")->fetch_object();
if(isset($ordemAtual->id)){
$db->alterar("rede_social_ordem",["data"=>$novaOrdemStr],"id='{$ordemAtual->id}'");
}else{
$db->inserir("rede_social_ordem",["data"=>$novaOrdemStr]);
}
echo json_encode(['status'=>'success','ordem'=>$novaOrdem]);
}
public function apagar_varios(){
$db=new mysql();
$exec=$db->Executar("SELECT * FROM rede_social");
$idsRemovidos=[];
while($data=$exec->fetch_object()){
if($this->post('apagar_'.$data->id)==1){
if(!empty($data->imagem)&&file_exists('arquivos/img_redes_sociais/'.$data->imagem)){
unlink('arquivos/img_redes_sociais/'.$data->imagem);
}
$db->apagar("rede_social","codigo='$data->codigo'");
$idsRemovidos[]=$data->id;
}
}
if(!empty($idsRemovidos)){
$ordemAtual=$db->Executar("SELECT * FROM rede_social_ordem ORDER BY id DESC LIMIT 1")->fetch_object();
if(!empty($ordemAtual->data)){
$idsAtuais=explode(',',$ordemAtual->data);
$novaOrdem=array_diff($idsAtuais,$idsRemovidos);
$db->alterar("rede_social_ordem",["data"=>implode(',',$novaOrdem)],"id='{$ordemAtual->id}'");
}
}
$this->irpara(DOMINIO.$this->_controller);
}
}