<?php

Class model_arquivos_imagens {

    public function calcula_altura_jpg($imagem, $largura){
        $source = @imagecreatefromjpeg($imagem);
        if(!$source){ return 0; }
        $imagex = imagesx($source);
        $imagey = imagesy($source);
        return round(($largura * $imagey) / $imagex);
    }

    // =====================================================
    // Aceita arquivos, mas faz um filtro geral (não só imagem)
    // =====================================================
    public function filtro($arquivo){
        if(!empty($arquivo['tmp_name'])){
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

            // extensões proibidas
            $proibidas = ['exe','php','php3','php4','php5','phtml'];
            if(in_array($ext, $proibidas)) return false;

            // tipo MIME proibido
            $tipo = explode('/', $arquivo['type']);
            if($tipo[0] == 'application') return false;

            // aqui aceitamos tudo que não seja proibido
            return true;
        } else {
            return false;
        }
    }

    // =====================================================
    // Filtro específico para imagens
    // =====================================================
    public function filtro_imagem($arquivo){
        if(!empty($arquivo['tmp_name'])){
            $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

            // extensões permitidas
            $permitidas = ['jpg','jpeg','png','gif','webp'];
            if(!in_array($ext, $permitidas)) return false;

            // bloqueio extensões perigosas
            $proibidas = ['exe','php','php3','php4','php5','phtml'];
            if(in_array($ext, $proibidas)) return false;

            $tipo = explode('/', $arquivo['type']);
            if($tipo[0] != 'image') return false;

            return true;
        } else {
            return false;
        }
    }

    public function trata_nome($nome){
        $extensao = $this->extensao($nome);

        //remove acentos
        $nome_arquivo = preg_replace(
            array(
                "/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/","/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/",
                "/(ñ)/","/(Ñ)/"
            ),
            explode(" ","a A e E i I o O u U n N"),
            $nome
        );

        //remove caracteres indesejados
        $nome_arquivo = str_replace(
            array("?","+",",","'","/","(",")","&","%","#","@","!","=","<",">",";",":","|","*","$"),
            "",
            $nome_arquivo
        );

        //coloca ifen para separar palavras
        $nome_arquivo = str_replace(array(".", " ", "_", "+"), "-", $nome_arquivo);
        //certifica que não tem ifens repetidos
        $nome_arquivo = preg_replace('/-+/', '-', $nome_arquivo);
        //coloca data ao final para não repetir
        $nome_arquivo = $nome_arquivo.'['.date('d-m-y').']['.date('H-i-s').']';

        return $nome_arquivo.".".$extensao;
    }

    public function extensao($nome){
        $array = explode(".", $nome);
        return strtolower(end($array)); // força para minúsculo
    }

    public function jpg($img, $max_x, $max_y, $nome_foto){
        list($width, $height) = getimagesize($img);
        $original_x = $width;
        $original_y = $height;

        if(($max_x < $original_x) || ($original_y > $max_y)){
            // calcula porcentagem
            if($original_x > $original_y) {
                $porcentagem = (100 * $max_x) / $original_x;
            } else {
                $porcentagem = (100 * $max_y) / $original_y;
            }

            $tamanho_x = $original_x * ($porcentagem / 100);
            $tamanho_y = $original_y * ($porcentagem / 100);

            $image_p = imagecreatetruecolor($tamanho_x, $tamanho_y);
            $image   = imagecreatefromjpeg($img);

            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $tamanho_x, $tamanho_y, $width, $height);

            return imagejpeg($image_p, $nome_foto, 100);
        }
    }

}
