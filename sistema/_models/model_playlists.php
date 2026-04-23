<?php
class model_playlists extends model {

  public function carregar($id){
    $db = new mysql();
    $exec = $db->Executar("SELECT * FROM videos_playlist WHERE id = '".$id."' LIMIT 1");
    return $exec->fetch_object();
  }

  public function get_videos_playlist_ids($playlist_id) {
    $db = new mysql();
    $exec = $db->Executar("SELECT video_id FROM videos_playlist_itens WHERE playlist_id = '".$playlist_id."' ORDER BY ordem");
    $ids = [];
    while($row = $exec->fetch_object()){
      $ids[] = $row->video_id;
    }
    return $ids;
  }

  public function adicionar_videos($playlist_id, array $videos_ids) {
    $db = new mysql();

    // Apaga vínculos atuais da playlist
    $db->apagar("videos_playlist_itens", "playlist_id = ?", [$playlist_id], "i");

    $ordem = 1;
    foreach ($videos_ids as $video_id) {
      $dados = [
        'playlist_id' => $playlist_id,
        'video_id' => $video_id,
        'ordem' => $ordem
      ];
      $db->inserir('videos_playlist_itens', $dados);
      $ordem++;
    }
  }
}
