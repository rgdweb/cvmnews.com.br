<?php
// Certifica que o acesso à view é liberado pelo sistema
if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; }

// Define a classe do controlador
class playlists extends controller {

  // Propriedade para o nome do módulo, utilizada em títulos de página
  protected $_modulo_nome = "Playlists";

  // Método de inicialização do controlador
  public function init(){
    // Autentica o usuário (assumindo que o método autenticacao() existe na classe pai 'controller')
    $this->autenticacao();
    // Define o nível de acesso necessário para este módulo (64 é um exemplo, ajuste conforme seu sistema)
    $this->nivel_acesso(64);
  }

  // Método padrão para exibir a lista de playlists
  public function inicial(){
    // Prepara os dados básicos do layout
    $dados['_base'] = $this->base_layout();
    // Define o título da página
    $dados['_titulo'] = $this->_modulo_nome;
    $dados['_subtitulo'] = ""; // Subtítulo pode ser vazio ou específico

    // Instancia o modelo de playlists
    $model = new model_playlists();
    // Obtém a lista de playlists do modelo
    $dados['lista'] = $model->lista();

    // Carrega a view para exibir a lista de playlists, seguindo o padrão 'htm_playlists'
    $this->view('htm_playlists', $dados);
  }

  // Método para exibir o formulário de criação de nova playlist
  public function novo(){
    // Prepara os dados básicos do layout
    $dados['_base'] = $this->base_layout();
    $dados['_titulo'] = $this->_modulo_nome;
    $dados['_subtitulo'] = "Nova Playlist";

    // Carrega a view do formulário de nova playlist, seguindo o padrão 'htm_playlists_novo'
    $this->view('htm_playlists_novo', $dados);
  }

  // Método para processar a gravação de uma nova playlist
  public function novo_grv(){
    // Obtém os dados do formulário via POST
    $nome = $this->post('nome');
    $descricao = $this->post('descricao');

    // Valida se o nome foi fornecido (ajuste a validação conforme necessário)
    $this->valida($nome);

    // Instancia o modelo e cria a nova playlist
    $model = new model_playlists();
    $model->criar($nome, $descricao);

    // Redireciona de volta para a lista de playlists após a gravação
    $this->irpara(DOMINIO.$this->_controller.'/inicial');
  }

  // Método para exibir o formulário de alteração de playlist existente
  public function alterar(){
    // Prepara os dados básicos do layout
    $dados['_base'] = $this->base_layout();
    $dados['_titulo'] = $this->_modulo_nome;
    $dados['_subtitulo'] = "Alterar Playlist";

    // Obtém o ID da playlist a ser alterada via GET
    $id = $this->get('id');
    // Instancia o modelo e carrega os dados da playlist
    $model = new model_playlists();
    $dados['data'] = $model->carregar($id);

    // Se a playlist não for encontrada, redireciona para a lista inicial
    if(!$dados['data']){
      $this->irpara(DOMINIO.$this->_controller.'/inicial');
      return;
    }

    // Carrega a view do formulário de alteração, seguindo o padrão 'htm_playlists_alterar'
    $this->view('htm_playlists_alterar', $dados);
  }

  // Método para processar a gravação das alterações de uma playlist
  public function alterar_grv(){
    // Obtém os dados do formulário via POST
    $id = $this->post('id');
    $nome = $this->post('nome');
    $descricao = $this->post('descricao');

    // Valida o ID e o nome (ajuste a validação conforme necessário)
    $this->valida($id);
    $this->valida($nome);

    // Instancia o modelo e altera a playlist
    $model = new model_playlists();
    $model->alterar($id, $nome, $descricao);

    // Redireciona de volta para a lista de playlists após a gravação
    $this->irpara(DOMINIO.$this->_controller.'/inicial');
  }

  // Método para apagar múltiplas playlists selecionadas
  public function apagar_varios(){
    $db = new mysql();

    // Percorre todas as playlists para verificar quais foram selecionadas para apagar
    $exec = $db->Executar("SELECT id FROM videos_playlist");
    while($pl = $exec->fetch_object()){
      // Verifica se o checkbox de apagar para esta playlist foi marcado
      if($this->post('apagar_'.$pl->id) == 1){
        // Apaga a playlist do banco de dados
        $db->apagar("videos_playlist", "id='".$pl->id."'");
        // [IMPORTANTE] AQUI VOCÊ DEVE ADICIONAR LÓGICA PARA APAGAR OS VÍNCULOS DESSA PLAYLIST NA TABELA `videos_playlist_itens`
        // Exemplo: $db->apagar("videos_playlist_itens", "playlist_id='".$pl->id."'");
        // Isso garante a integridade dos dados e evita chaves estrangeiras órfãs
      }
    }
    // Redireciona de volta para a lista de playlists
    $this->irpara(DOMINIO.$this->_controller.'/inicial');
  }
}
