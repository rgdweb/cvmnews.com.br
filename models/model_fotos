<?php

class model_fotos extends model {

    /**
     * Lista os grupos (álbuns) de fotos, pegando a primeira imagem de cada um como capa.
     */
    public function lista_albuns() {
        $lista = array();
        $db = new mysql();
        
        // Busca todos os grupos/álbuns ativos
        $exec_grupos = $db->executar("SELECT codigo, titulo FROM fotos_grupos ORDER BY titulo ASC");
        
        while ($grupo = $exec_grupos->fetch_object()) {
            
            // Para cada grupo, busca a primeira imagem para usar como capa
            $exec_capa = $db->executar("SELECT imagem FROM fotos_imagem WHERE codigo_grupo = '{$grupo->codigo}' ORDER BY id ASC LIMIT 1");
            $capa = $exec_capa->fetch_object();
            
            $lista[] = [
                'codigo' => $grupo->codigo,
                'titulo' => $grupo->titulo,
                'capa' => ($capa) ? PASTA_CLIENTE . 'img_fotos_g/' . $grupo->codigo . '/' . $capa->imagem : LAYOUT . 'img/sem_foto.png'
            ];
        }
        
        return $lista;
    }

    /**
     * Carrega todas as imagens de um álbum específico.
     */
    public function lista_fotos_do_album($codigo_grupo) {
        $lista = array();
        $db = new mysql();
        
        $exec = $db->executar("SELECT * FROM fotos_imagem WHERE codigo_grupo = '$codigo_grupo' ORDER BY id ASC");
        
        while ($data = $exec->fetch_object()) {
            $lista[] = [
                'imagem_url' => PASTA_CLIENTE . 'img_fotos_g/' . $codigo_grupo . '/' . $data->imagem,
                'legenda' => $data->legenda ?? ''
            ];
        }
        
        return $lista;
    }
    
    // Mantenha seus outros métodos aqui (carrega, etc.)
}