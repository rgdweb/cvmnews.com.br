<?php
class fotos extends controller {
    protected $_modulo_nome = "Galeria de Fotos";
    private $img_sizes = ['g' => [800, 600], 'p' => [300, 300]];

    public function init(){ $this->autenticacao(); $this->nivel_acesso(60); }

    private function redimensionarImagem($origem, $destino, $w, $h, $qualidade = 85) {
        $info = getimagesize($origem);
        if(!$info) return false;
        
        $mime = $info['mime'];
        $src_w = $info[0]; $src_h = $info[1];
        
        // Cálculo para manter proporção e centralizar
        $ratio_orig = $src_w / $src_h;
        $ratio_dest = $w / $h;
        
        if($ratio_dest > $ratio_orig) {
            $new_h = $h; $new_w = $h * $ratio_orig;
        } else {
            $new_w = $w; $new_h = $w / $ratio_orig;
        }
        
        $x = ($w - $new_w) / 2; $y = ($h - $new_h) / 2;
        
        $canvas = imagecreatetruecolor($w, $h);
        $bg = imagecolorallocate($canvas, 255, 255, 255); // Fundo branco
        imagefill($canvas, 0, 0, $bg);
        
        switch($mime) {
            case 'image/jpeg': $src = imagecreatefromjpeg($origem); break;
            case 'image/png': $src = imagecreatefrompng($origem); break;
            case 'image/gif': $src = imagecreatefromgif($origem); break;
            default: return false;
        }
        
        imagecopyresampled($canvas, $src, $x, $y, 0, 0, $new_w, $new_h, $src_w, $src_h);
        imagejpeg($canvas, $destino, $qualidade);
        imagedestroy($canvas); imagedestroy($src);
        return true;
    }

    private function processarImagem($cod, $arquivo_temp, $nome) {
        $dirs = [];
        foreach($this->img_sizes as $tipo => $size) {
            $dirs[$tipo] = "arquivos/img_fotos_{$tipo}/{$cod}/";
            if(!is_dir($dirs[$tipo])) mkdir($dirs[$tipo], 0755, true);
        }
        
        $ext = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
        if(!in_array($ext, ['jpg','jpeg','png','gif'])) return false;
        
        $nome_final = md5(uniqid()) . '.jpg';
        
        // Processa cada tamanho
        foreach($this->img_sizes as $tipo => $size) {
            $destino = $dirs[$tipo] . $nome_final;
            if(!$this->redimensionarImagem($arquivo_temp, $destino, $size[0], $size[1])) return false;
        }
        
        return $nome_final;
    }

    public function inicial(){
        $d = ['_base' => $this->base_layout(), '_titulo' => $this->_modulo_nome, '_subtitulo' => ""];
        $g = $this->get('grupo');
        $f = new model_fotos();
        $d['grupo'] = $g;
        $d['lista_grupos'] = $f->lista_grupos($g);
        if(!$g) $g = $d['lista_grupos'][0]['codigo'] ?? false;
        $d['lista'] = $f->lista($g);
        $this->view('fotos', $d);
    }

    public function ordem(){
        $o = []; parse_str($this->post('list'), $o);
        $db = new mysql();
        $db->inserir("fotos_ordem", ["grupo"=>$this->post('grupo'), "data"=>implode(',', $o['item'])]);
    }

    public function novo(){
        $d = ['_base' => $this->base_layout(), '_titulo' => $this->_modulo_nome, '_subtitulo' => "Novo", 'aba_selecionada' => "dados"];
        $g = $this->get('grupo');
        $d['grupo'] = $g;
        $d['lista_grupos'] = (new model_fotos())->lista_grupos($g);
        $this->view('fotos.novo', $d);
    }

    public function novo_grv(){
        $t = $this->post('titulo'); $g = $this->post('grupo');
        $this->valida($t); $this->valida($g);
        $cod = $this->gera_codigo();
        
        $db = new mysql();
        $db->inserir("fotos", ["codigo"=>$cod, "grupo"=>$g, "titulo"=>$t, "conteudo"=>$_POST['conteudo']]);
        $uid = $db->ultimo_id();
        
        $ex = $db->executar("SELECT data FROM fotos_ordem WHERE grupo='$g' ORDER BY id DESC LIMIT 1");
        $d = $ex->fetch_object();
        $ordem = ($d->data ?? '') ? $d->data.','.$uid : $uid;
        $db->inserir("fotos_ordem", ["grupo"=>$g, "data"=>$ordem]);
        
        $this->irpara(DOMINIO.$this->_controller.'/alterar/aba/imagem/codigo/'.$cod);
    }

    public function alterar(){
        $cod = $this->get('codigo');
        $d = ['_base' => $this->base_layout(), '_titulo' => $this->_modulo_nome, '_subtitulo' => "Alterar"];
        $d['aba_selecionada'] = $this->get('aba') ?: 'dados';
        
        $db = new mysql();
        $d['data'] = $db->executar("SELECT * FROM fotos WHERE codigo='$cod'")->fetch_object();
        
        // Buscar imagens ordenadas
        $ex = $db->executar("SELECT data FROM fotos_imagem_ordem WHERE codigo='$cod' ORDER BY id DESC LIMIT 1");
        $ordem = $ex->fetch_object();
        $img = [];
        
        if($ordem->data ?? false) {
            foreach(explode(',', $ordem->data) as $id) {
                $res = $db->executar("SELECT * FROM fotos_imagem WHERE id='$id'")->fetch_object();
                if($res->imagem ?? false) {
                    $leg = $db->executar("SELECT legenda FROM fotos_imagem_legenda WHERE id_img='$id'")->fetch_object();
                    $img[] = [
                        'id' => $res->id,
                        'legenda' => $leg->legenda ?? '',
                        'imagem_p' => PASTA_CLIENTE."img_fotos_p/{$cod}/{$res->imagem}",
                        'imagem_g' => PASTA_CLIENTE."img_fotos_g/{$cod}/{$res->imagem}"
                    ];
                }
            }
        }
        $d['imagens'] = $img;
        $this->view('fotos.alterar', $d);
    }

    public function alterar_grv(){
        $cod = $this->post('codigo');
        $this->valida($this->post('titulo'));
        $db = new mysql();
        $db->alterar("fotos", ["titulo"=>$this->post('titulo'), "conteudo"=>$_POST['conteudo']], "codigo='$cod'");
        $this->irpara(DOMINIO.$this->_controller.'/alterar/codigo/'.$cod);
    }

    public function upload(){
        $d = ['_base' => $this->base_layout(), 'codigo' => $this->get('codigo')];
        $this->view('enviar_imagens', $d);
    }

    public function imagem_redimencionada(){
        $cod = $this->post('codigo');
        $im = $_POST['imagem']; $no = $this->post('nomeimagem');
        
        list($t, $d) = explode(';', $im);
        list(, $d) = explode(',', $d);
        $dados = base64_decode($d);
        
        $temp = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($temp, $dados);
        
        $nome_final = $this->processarImagem($cod, $temp, $no);
        unlink($temp);
        
        if($nome_final) {
            $db = new mysql();
            $db->inserir("fotos_imagem", ["codigo"=>$cod, "imagem"=>$nome_final]);
            $uid = $db->ultimo_id();
            
            $ex = $db->executar("SELECT data FROM fotos_imagem_ordem WHERE codigo='$cod' ORDER BY id DESC LIMIT 1");
            $dt = $ex->fetch_object();
            $ordem = ($dt->data ?? '') ? $dt->data.','.$uid : $uid;
            $db->inserir("fotos_imagem_ordem", ["codigo"=>$cod, "data"=>$ordem]);
        }
    }

    public function imagem_manual(){
        if(!$_FILES['arquivo']['tmp_name']) return;
        
        $cod = $this->get('codigo');
        $nome_final = $this->processarImagem($cod, $_FILES['arquivo']['tmp_name'], $_FILES['arquivo']['name']);
        
        if($nome_final) {
            $db = new mysql();
            $db->inserir("fotos_imagem", ["codigo"=>$cod, "imagem"=>$nome_final]);
            $uid = $db->ultimo_id();
            
            $ex = $db->executar("SELECT data FROM fotos_imagem_ordem WHERE codigo='$cod' ORDER BY id DESC LIMIT 1");
            $dt = $ex->fetch_object();
            $ordem = ($dt->data ?? '') ? $dt->data.','.$uid : $uid;
            $db->inserir("fotos_imagem_ordem", ["codigo"=>$cod, "data"=>$ordem]);
        } else {
            $this->msg('Erro ao processar imagem!');
        }
        
        $this->irpara(DOMINIO.$this->_controller."/alterar/codigo/{$cod}/aba/imagem");
    }

    public function ordenar_imagem(){
        $o = []; parse_str($this->post('list'), $o);
        $db = new mysql();
        $db->inserir("fotos_imagem_ordem", ["codigo"=>$this->post('codigo'), "data"=>implode(',', $o['item'])]);
    }

    public function apagar_imagem(){
        $cod = $this->get('codigo'); $id = $this->get('id');
        if($id) {
            $db = new mysql();
            $d = $db->executar("SELECT imagem FROM fotos_imagem WHERE id='$id'")->fetch_object();
            if($d->imagem) {
                foreach(['g','p'] as $tipo) unlink("arquivos/img_fotos_{$tipo}/{$cod}/{$d->imagem}");
                $db->apagar("fotos_imagem", "id='$id'");
            }
        }
        $this->irpara(DOMINIO.$this->_controller."/alterar/codigo/{$cod}/aba/imagem");
    }

    public function legenda(){
        $id = $this->get('id'); $cod = $this->get('codigo');
        $db = new mysql();
        $dt = $db->executar("SELECT legenda FROM fotos_imagem_legenda WHERE id_img='$id'")->fetch_object();
        $d = ['_base' => $this->base_layout(), 'codigo' => $cod, 'id' => $id, 'legenda' => $dt->legenda ?? ''];
        $this->view('fotos.legenda', $d);
    }

    public function legenda_grv(){
        $id = $this->post('id'); $leg = $this->post('legenda'); $cod = $this->post('codigo');
        $db = new mysql();
        $ex = $db->executar("SELECT id FROM fotos_imagem_legenda WHERE id_img='$id'");
        
        if($ex->num_rows) {
            $db->alterar("fotos_imagem_legenda", ["legenda"=>$leg], "id_img='$id'");
        } else {
            $db->inserir("fotos_imagem_legenda", ["id_img"=>$id, "legenda"=>$leg]);
        }
        $this->irpara(DOMINIO.$this->_controller."/alterar/codigo/{$cod}/aba/imagem");
    }

    // Métodos de grupos simplificados
    public function grupos(){ 
        $d = ['_base' => $this->base_layout(), '_titulo' => $this->_modulo_nome, '_subtitulo' => "Grupos"];
        $d['lista_grupos'] = (new model_fotos())->lista_grupos();
        $this->view('fotos.grupos', $d);
    }

    public function novo_grupo(){ 
        $d = ['_base' => $this->base_layout(), '_titulo' => $this->_modulo_nome, '_subtitulo' => "Novo Grupo"];
        $this->view('fotos.grupos.novo', $d);
    }

    public function apagar_varios(){
        $db = new mysql();
        $ex = $db->executar("SELECT * FROM fotos");
        while($d = $ex->fetch_object()) {
            if($this->post('apagar_'.$d->id) == 1) {
                $ei = $db->executar("SELECT imagem FROM fotos_imagem WHERE codigo='$d->codigo'");
                while($di = $ei->fetch_object()) {
                    if($di->imagem) {
                        foreach(['g','p'] as $tipo) unlink("arquivos/img_fotos_{$tipo}/{$d->codigo}/{$di->imagem}");
                    }
                }
                $db->apagar("fotos_imagem", "codigo='$d->codigo'");
                $db->apagar("fotos", "codigo='$d->codigo'");
            }
        }
        $this->irpara(DOMINIO.$this->_controller.'/inicial');
    }
}