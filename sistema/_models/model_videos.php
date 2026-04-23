<?php
class model_videos extends model {

  /**
   * Lista os vídeos na ordem definida em videos_ordem (se existir),
   * carregando todos com uma única consulta,
   * ou lista todos ordenados pelo id descendente se não houver ordem definida.
   */
  public function lista() {
    $lista = array();
    $db = new mysql();

    // Pega a última ordem cadastrada
    $exec_ordem = $db->Executar("SELECT data FROM videos_ordem ORDER BY id DESC LIMIT 1");
    $data_ordem = $exec_ordem->fetch_object();
    
    if (isset($data_ordem->data) && !empty($data_ordem->data)) {
        $order_ids = explode(',', $data_ordem->data);
        // Sanitiza os IDs
        $sanitized_ids = array_map('intval', $order_ids);
        
        if (!empty($sanitized_ids)) {
            $id_string = implode(',', $sanitized_ids);
            // Busca todos os vídeos numa única consulta
            $exec_videos = $db->Executar("SELECT id, codigo, titulo, video FROM videos WHERE id IN ($id_string)");
            
            // Mapear por ID para ordenar depois
            $videos_by_id = array();
            while ($video = $exec_videos->fetch_object()) {
                $videos_by_id[$video->id] = $video;
            }
            
            // Monta lista na ordem correta
            foreach ($sanitized_ids as $id) {
                if (isset($videos_by_id[$id])) {
                    $lista[] = (array)$videos_by_id[$id];
                }
            }
            
            // OPCIONAL: Adiciona vídeos novos que não estão na ordem salva
            $exec_novos = $db->Executar("SELECT id, codigo, titulo, video FROM videos WHERE id NOT IN ($id_string) ORDER BY id DESC");
            while ($video = $exec_novos->fetch_object()) {
                $lista[] = (array)$video;
            }
        }
    } else {
        // Caso não tenha ordem definida, lista todos ordenados por id desc
        $exec = $db->Executar("SELECT id, codigo, titulo, video FROM videos ORDER BY id DESC");
        while ($video = $exec->fetch_object()) {
            $lista[] = (array)$video;
        }
    }
    
    return $lista;
}

  /**
   * Carrega um vídeo pelo código único.
   *
   * @param string $codigo
   * @return object|false
   */
  public function carrega($codigo) {
    $db = new mysql();

    // Escapa o código para segurança
    $codigo_seguro = addslashes($codigo);

    $exec = $db->executar("SELECT * FROM videos WHERE codigo='$codigo_seguro' LIMIT 1");

    if ($exec->num_rows == 1) {
      return $exec->fetch_object();
    }

    return false;
  }
}
