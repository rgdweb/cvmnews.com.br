<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- /Tudo em um único pacote -->
  <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478'];?>">   
  <title>GALERIA FOTOS - <?=$_base['titulo_pagina']?></title>
  <meta name="title" content="<?=$_base['titulo_pagina']?>">
  <meta name="description" content="<?=$_base['descricao']?>">
  <meta http-equiv="content-language" content="pt_BR">
  <meta name="keywords" content="<?=$_base['keywords1']?>">
  <!-- SEO Marcação do Open Graph -->
  <link rel="canonical" href="<?=DOMINIO?>" />
  <meta property="og:locale" content="pt_BR" />
  <meta property="og:type" content="article" />
  <meta property="og:title" content="<?=$_base['titulo_pagina']?>" />
  <meta property="og:description" content="<?=$_base['descricao']?>" />
  <meta property="og:url" content="<?=DOMINIO?>" />
  <meta property="og:site_name" content="<?=$_base['titulo_pagina']?>" />
  <meta property="article:publisher" content="<?=DOMINIO?>" />
  <meta property="og:image" content="<?=$_base['keywords2']?>" />
  <meta property="og:image:secure_url" content="<?=$_base['keywords2']?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <!-- Facebook / Twitter -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?=$_base['titulo_pagina']?>" />
  <meta name="twitter:description" content="<?=$_base['descricao']?>" />
  <meta name="twitter:site" content="@radio-online" />
  <meta name="twitter:image" content="<?=$_base['keywords2']?>" />
  <meta name="twitter:creator" content="@radio-online" />
  <!-- / SEO Marcação do Open Graph // Schema-->
  <meta itemprop="name" content="<?=$_base['titulo_pagina']?>" />
  <meta itemprop="description" content="<?=$_base['descricao']?>" />
  <meta itemprop="image" content="<?=$_base['keywords2']?>" />




<link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet"/>
<link href="<?=LAYOUT?>css/theme.css" rel="stylesheet"/>
<link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet"/>
<link href="<?=LAYOUT?>css/custom.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet"/>
<?php require_once('_css_padrao.php'); require_once('_css_personalizado.php'); ?>

<style>
/* Reset e Base */
* { box-sizing: border-box; }
body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); color: #ffffff; line-height: 1.6; }

/* Premium Gallery Container */
.premium-gallery {
    background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(10,10,10,0.98) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 24px;
    padding: 0;
    margin: 40px auto;
    max-width: 1400px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.02);
}

/* Header Premium */
.gallery-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.gallery-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.02)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.02)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.gallery-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    position: relative;
    z-index: 2;
    text-shadow: 0 4px 20px rgba(0,0,0,0.3);
    letter-spacing: -0.02em;
}

.gallery-subtitle {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.8);
    margin-top: 15px;
    font-weight: 300;
}

/* Group Navigation - BOTÕES MAIORES E MAIS VISÍVEIS */
.group-navigation {
    display: flex;
    justify-content: center;
    padding: 30px;
    gap: 15px;
    flex-wrap: wrap;
    background: rgba(255,255,255,0.02);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.group-nav-btn {
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    border: 1px solid rgba(255,255,255,0.1);
    color: #ffffff;
    padding: 18px 35px;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 700;
    font-size: 1.3rem;
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.group-nav-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    transition: left 0.5s;
}

.group-nav-btn:hover::before { left: 100%; }

.group-nav-btn:hover,
.group-nav-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    border-color: rgba(255,255,255,0.2);
}

/* Album Carousel */
.album-carousel-container {
    padding: 50px 40px;
    position: relative;
}

.group-section {
    display: none;
}

.group-section.active {
    display: block;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* TÍTULOS DOS GRUPOS MUITO MAIORES E MAIS VISÍVEIS */
.group-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(3.5rem, 6vw, 5.5rem);
    font-weight: 800;
    color: #ffffff;
    text-align: center;
    margin-bottom: 50px;
    position: relative;
    text-shadow: 0 6px 20px rgba(0,0,0,0.5);
    letter-spacing: -0.02em;
    line-height: 1.1;
}

.group-title::after {
    content: '';
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

/* Premium Album Grid */
.album-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.album-card {
    position: relative;
    background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.02) 100%);
    border-radius: 20px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    aspect-ratio: 4/3;
}

.album-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.album-card:hover::before { opacity: 1; }

.album-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.15);
}

.album-image {
    width: 100%;
    height: 70%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.album-card:hover .album-image {
    transform: scale(1.05);
}

.album-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.9));
    padding: 40px 25px 25px;
    z-index: 2;
}

/* TÍTULOS DOS ÁLBUNS MAIORES E MAIS VISÍVEIS */
.album-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    text-shadow: 0 3px 15px rgba(0,0,0,0.8);
    letter-spacing: 0.5px;
    line-height: 1.2;
}

.album-count {
    font-size: 1.1rem;
    color: rgba(255,255,255,0.9);
    margin-top: 8px;
    font-weight: 600;
    text-shadow: 0 2px 8px rgba(0,0,0,0.6);
}

.album-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 3;
}

.album-card:hover .album-overlay {
    opacity: 1;
}

.play-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 2rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); }
    50% { box-shadow: 0 10px 40px rgba(102, 126, 234, 0.6); }
    100% { box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); }
}

/* Modal Premium */
.photo-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.97);
    backdrop-filter: blur(20px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-modal.active {
    display: flex;
    opacity: 1;
}

.modal-content {
    position: relative;
    max-width: 95vw;
    max-height: 95vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}

.modal-close {
    position: fixed;
    top: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: rgba(0,0,0,0.7);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    color: #ffffff;
    font-size: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.modal-close:hover {
    background: rgba(255,255,255,0.1);
    transform: scale(1.1);
}

.modal-nav {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    width: 60px;
    height: 60px;
    background: rgba(0,0,0,0.7);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    color: #ffffff;
    font-size: 1.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.modal-nav:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: translateY(-50%) scale(1.1);
}

.modal-nav.prev { left: 30px; }
.modal-nav.next { right: 30px; }

.modal-counter {
    position: fixed;
    top: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.7);
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 500;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

/* Share Section Premium - ÍCONES MELHORADOS */
.premium-share {
    background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
    border-radius: 20px;
    padding: 40px;
    margin: 60px auto;
    max-width: 800px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.08);
}

/* Share Title - ALTERADO PARA PRETO CONFORME SOLICITADO */
.share-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #1a1a1a; /* Alterado de #ffffff para preto */
    margin-bottom: 30px;
    font-family: 'Playfair Display', serif;
    /* A sombra foi removida pois não funciona bem com texto escuro */
}

.share-buttons {
    display: flex;
    justify-content: center;
    gap: 25px;
    flex-wrap: wrap;
}

/* Share Buttons - ÍCONES MAIORES */
.share-btn {
    width: 80px; /* Aumentado de 70px */
    height: 80px; /* Aumentado de 70px */
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-size: 2.2rem; /* Aumentado de 1.8rem */
    border: 2px solid rgba(255,255,255,0.1);
}

.share-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    transform: scale(0);
    transition: transform 0.3s ease;
}

.share-btn:hover::before {
    transform: scale(1);
}

.share-btn:hover {
    transform: translateY(-8px) scale(1.1);
    box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    border-color: rgba(255,255,255,0.3);
}

.facebook { 
    background: linear-gradient(135deg, #1877f2, #42a5f5); 
}

.whatsapp { 
    background: linear-gradient(135deg, #25d366, #4caf50); 
}

.instagram { 
    background: linear-gradient(135deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); 
}

/* X (TWITTER) COM ÍCONE CORRETO */
.x-twitter { 
    background: linear-gradient(135deg, #000000, #333333); 
    border: 2px solid #ffffff;
}

.x-twitter:hover {
    background: linear-gradient(135deg, #1a1a1a, #444444);
}

/* Video Link Premium - TEXTO MAIOR E MAIS ESCURO */
.video-section {
    text-align: center;
    margin: 80px auto;
    max-width: 600px;
}

.video-link {
    display: inline-flex;
    align-items: center;
    gap: 18px;
    padding: 25px 50px;
    background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(20,20,20,0.9) 100%);
    border: 3px solid #ffffff;
    border-radius: 50px;
    color: #ffffff;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.4rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
}

.video-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s;
}

.video-link:hover::before { left: 100%; }

.video-link:hover {
    transform: translateY(-5px) scale(1.05);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #ffffff;
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.4);
    color: #ffffff;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .album-grid { grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
    .gallery-header { padding: 50px 30px; }
    .album-carousel-container { padding: 40px 30px; }
    .group-title { font-size: clamp(3rem, 5vw, 4.5rem); }
}

@media (max-width: 768px) {
    .gallery-header { padding: 40px 20px; }
    .album-carousel-container { padding: 30px 20px; }
    .album-grid { grid-template-columns: 1fr; gap: 20px; }
    .group-navigation { padding: 20px; gap: 10px; }
    .group-nav-btn { padding: 15px 30px; font-size: 1.1rem; }
    .group-title { font-size: clamp(2.5rem, 6vw, 3.5rem); }
    .modal-nav { width: 50px; height: 50px; font-size: 1.2rem; }
    .modal-nav.prev { left: 15px; }
    .modal-nav.next { right: 15px; }
    .modal-close { top: 15px; right: 15px; width: 45px; height: 45px; }
    .share-buttons { gap: 20px; }
    .share-btn { width: 60px; height: 60px; font-size: 1.5rem; }
    .premium-share { padding: 30px 20px; margin: 40px 20px; }
    .video-link { padding: 20px 40px; font-size: 1.2rem; }
    .album-title { font-size: 1.5rem; }
}

@media (max-width: 480px) {
    .gallery-title { font-size: 2rem; }
    .group-title { font-size: clamp(2rem, 6vw, 2.8rem); }
    .album-card { aspect-ratio: 3/2; }
    .video-link { padding: 18px 35px; font-size: 1.1rem; }
    .album-title { font-size: 1.3rem; }
    .group-nav-btn { padding: 12px 25px; font-size: 1rem; }
}

/* Loading Animation */
.loading {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #667eea;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
</head>

<body>
<?php require_once('htm_modal.php'); require_once('htm_topo2.php'); ?>

<div class="wrapper">
<div class="container">
<div class="content_block row no-sidebar">
<div class="fl-container">
<div class="posts-block">
<div class="contentarea">
<div id="conteudo" class="pt-5">
<div class="row">
<div class="col-sm-2"></div>
<div class="col-sm-8">
<div class="titulo_padrao"></div>
<div class="mt-4"><?=$texto?></div>

<?php
$grupos = [];
$db = new mysql();
$sql = "SELECT g.id AS grupo_db_id, g.codigo AS grupo_codigo, g.titulo AS grupo_titulo, 
               a.id AS album_db_id, a.codigo AS album_codigo, a.titulo AS album_titulo, 
               i.imagem 
        FROM fotos_grupos g 
        LEFT JOIN fotos a ON a.grupo = g.codigo 
        LEFT JOIN fotos_imagem i ON i.codigo = a.codigo 
        WHERE a.codigo IS NOT NULL AND i.imagem IS NOT NULL 
        ORDER BY g.id DESC, a.id DESC, i.id ASC";

$exec = $db->Executar($sql);
while($row = $exec->fetch_object()) {
    $grupo_codigo = $row->grupo_codigo;
    $album_codigo = $row->album_codigo;
    
    if(!isset($grupos[$grupo_codigo])) {
        $grupos[$grupo_codigo] = [
            'codigo' => $grupo_codigo,
            'titulo' => $row->grupo_titulo,
            'albuns' => []
        ];
    }
    
    if(!isset($grupos[$grupo_codigo]['albuns'][$album_codigo])) {
        $grupos[$grupo_codigo]['albuns'][$album_codigo] = [
            'codigo' => $album_codigo,
            'titulo' => $row->album_titulo,
            'capa' => PASTA_CLIENTE . 'img_fotos_p/' . $album_codigo . '/' . $row->imagem,
            'imagens' => []
        ];
    }
    
    $grupos[$grupo_codigo]['albuns'][$album_codigo]['imagens'][] = 
        PASTA_CLIENTE . 'img_fotos_g/' . $album_codigo . '/' . $row->imagem;
}

foreach($grupos as &$grupo) {
    $grupo['albuns'] = array_values($grupo['albuns']);
}
unset($grupo);
?>

<div class="premium-gallery" data-aos="fade-up">
    <div class="gallery-header">
        <h1 class="gallery-title">
            <i class="fas fa-camera-retro" style="margin-right: 20px; opacity: 0.8;"></i>
            GALERIA DE FOTOS
        </h1>
        <p class="gallery-subtitle">Momentos únicos capturados com excelência</p>
    </div>

    <?php if(!empty($grupos)): ?>
    <div class="group-navigation">
        <?php $first = true; foreach($grupos as $grupo): ?>
        <button class="group-nav-btn <?= $first ? 'active' : '' ?>" 
                data-group="<?= $grupo['codigo'] ?>">
            <i class="fas fa-folder-open" style="margin-right: 10px;"></i>
            <?= htmlspecialchars($grupo['titulo']) ?>
        </button>
        <?php $first = false; endforeach; ?>
    </div>

    <div class="album-carousel-container">
        <?php $first = true; foreach($grupos as $grupo): ?>
        <div class="group-section <?= $first ? 'active' : '' ?>" 
             id="group-<?= $grupo['codigo'] ?>">
             
            <h2 class="group-title" data-aos="fade-up">
                <?= htmlspecialchars($grupo['titulo']) ?>
            </h2>

            <?php if(!empty($grupo['albuns'])): ?>
            <div class="album-grid">
                <?php foreach($grupo['albuns'] as $index => $album): ?>
                <div class="album-card" 
                     data-aos="fade-up" 
                     data-aos-delay="<?= $index * 100 ?>"
                     data-images="<?= htmlspecialchars(json_encode($album['imagens']), ENT_QUOTES, 'UTF-8') ?>"
                     data-album-title="<?= htmlspecialchars($album['titulo']) ?>">
                     
                    <img src="<?= $album['capa'] ?>" 
                         alt="<?= htmlspecialchars($album['titulo']) ?>" 
                         class="album-image"
                         loading="lazy">
                         
                    <div class="album-info">
                        <h3 class="album-title"><?= htmlspecialchars($album['titulo']) ?></h3>
                        <p class="album-count"><?= count($album['imagens']) ?> fotos</p>
                    </div>
                    
                    <div class="album-overlay">
                        <div class="play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="text-align: center; color: rgba(255,255,255,0.6); font-size: 1.2rem;">
                Nenhum álbum disponível neste grupo.
            </p>
            <?php endif; ?>
        </div>
        <?php $first = false; endforeach; ?>
    </div>
    <?php else: ?>
    <div style="padding: 100px; text-align: center;">
        <i class="fas fa-images" style="font-size: 4rem; color: rgba(255,255,255,0.3); margin-bottom: 20px;"></i>
        <p style="color: rgba(255,255,255,0.6); font-size: 1.3rem;">
            Nenhuma galeria encontrada no momento.
        </p>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Premium -->
<div class="photo-modal" id="photoModal">
    <button class="modal-close" id="modalClose">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="modal-counter" id="modalCounter">1 / 1</div>
    
    <button class="modal-nav prev" id="modalPrev">
        <i class="fas fa-chevron-left"></i>
    </button>
    
    <button class="modal-nav next" id="modalNext">
        <i class="fas fa-chevron-right"></i>
    </button>
    
    <div class="modal-content">
        <img id="modalImage" src="" alt="" class="modal-image">
    </div>
</div>

<!-- Share Section Premium -->
<div class="premium-share" data-aos="fade-up">
    <h3 class="share-title">Compartilhe esta galeria</h3>
    <div class="share-buttons">
        <a href="#" class="share-btn facebook" onclick="shareFB()" title="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" class="share-btn whatsapp" onclick="shareWA()" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="#" class="share-btn instagram" onclick="shareIG()" title="Instagram">
            <i class="fab fa-instagram"></i>
        </a>
        <!-- Ícone do X (Twitter) já estava correto com a classe fa-brands fa-x-twitter -->
        <a href="https://x.com/intent/tweet?url=<?=urlencode('http://'.$_SERVER['HTTP_HOST'])?>&text=<?=urlencode('Confira este site!')?>" target="_blank" class="share-btn x-twitter" title="X">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
            </a>
    </div>
</div>

<!-- Video Section Premium -->
<div class="video-section" data-aos="fade-up">
    <a href="https://cvmnews.com.br/videos" class="video-link">
        <i class="fas fa-play-circle"></i>
        Galeria de Vídeos
    </a>
</div>

</div>
<div class="col-sm-2"></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<?php include_once('htm_rodape2.php'); ?>

<script src="<?=LAYOUT?>js/jquery.min.js"></script>
<script src="<?=LAYOUT?>js/jquery-ui.min.js"></script>
<script src="<?=LAYOUT?>js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>js/modules.js"></script>
<script src="<?=LAYOUT?>js/theme.js"></script>
<script src="<?=LAYOUT?>js/jquery.themepunch.plugins.min.js"></script>
<script src="<?=LAYOUT?>js/jquery.themepunch.revolution.min.js"></script>
<script src="<?=LAYOUT?>js/jquery.isotope.min.js"></script>
<script src="<?=LAYOUT?>js/sorting.js"></script>
<script src="<?=LAYOUT?>js/slick.js"></script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
// Initialize AOS (Animate On Scroll)
AOS.init({
    duration: 800,
    easing: 'ease-out-cubic',
    once: true,
    offset: 50
});

document.addEventListener('DOMContentLoaded', function() {
    // Premium Gallery Controller
    class PremiumGallery {
        constructor() {
            this.currentImages = [];
            this.currentIndex = 0;
            this.modal = document.getElementById('photoModal');
            this.modalImage = document.getElementById('modalImage');
            this.modalCounter = document.getElementById('modalCounter');
            this.init();
        }

        init() {
            this.bindEvents();
            this.initGroupNavigation();
        }

        bindEvents() {
            // Album cards click
            document.addEventListener('click', (e) => {
                const albumCard = e.target.closest('.album-card');
                if (albumCard) {
                    this.openAlbum(albumCard);
                }
            });

            // Modal controls
            document.getElementById('modalClose').addEventListener('click', () => this.closeModal());
            document.getElementById('modalPrev').addEventListener('click', () => this.prevImage());
            document.getElementById('modalNext').addEventListener('click', () => this.nextImage());

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (!this.modal.classList.contains('active')) return;
                
                switch(e.key) {
                    case 'Escape': this.closeModal(); break;
                    case 'ArrowLeft': this.prevImage(); break;
                    case 'ArrowRight': this.nextImage(); break;
                }
            });

            // Touch/swipe support
            let touchStartX = 0;
            let touchEndX = 0;

            this.modal.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            });

            this.modal.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe(touchStartX, touchEndX);
            });

            // Click outside to close
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });
        }

        initGroupNavigation() {
            const navButtons = document.querySelectorAll('.group-nav-btn');
            const groupSections = document.querySelectorAll('.group-section');

            navButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetGroup = btn.dataset.group;
                    
                    // Update active button
                    navButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    // Show target group
                    groupSections.forEach(section => {
                        section.classList.remove('active');
                        if (section.id === `group-${targetGroup}`) {
                            section.classList.add('active');
                        }
                    });

                    // Refresh AOS animations
                    AOS.refresh();
                });
            });
        }

        openAlbum(albumCard) {
            try {
                this.currentImages = JSON.parse(albumCard.dataset.images);
                this.currentIndex = 0;
                
                if (this.currentImages.length > 0) {
                    this.showModal();
                }
            } catch (error) {
                console.error('Erro ao abrir álbum:', error);
            }
        }

        showModal() {
            if (this.currentImages.length === 0) return;
            
            this.updateModalImage();
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        closeModal() {
            this.modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            this.currentImages = [];
            this.currentIndex = 0;
        }

        updateModalImage() {
            if (this.currentIndex >= 0 && this.currentIndex < this.currentImages.length) {
                const imagePath = this.currentImages[this.currentIndex];
                
                // Preload image
                const img = new Image();
                img.onload = () => {
                    this.modalImage.src = img.src;
                    this.modalCounter.textContent = `${this.currentIndex + 1} / ${this.currentImages.length}`;
                };
                img.onerror = () => {
                    console.error('Erro ao carregar imagem:', imagePath);
                    if (this.currentImages.length > 1) {
                        this.nextImage();
                    } else {
                        this.closeModal();
                    }
                };
                img.src = imagePath;
            }
        }

        nextImage() {
            if (this.currentImages.length > 1) {
                this.currentIndex = (this.currentIndex + 1) % this.currentImages.length;
                this.updateModalImage();
            }
        }

        prevImage() {
            if (this.currentImages.length > 1) {
                this.currentIndex = (this.currentIndex - 1 + this.currentImages.length) % this.currentImages.length;
                this.updateModalImage();
            }
        }

        handleSwipe(startX, endX) {
            const threshold = 50;
            const diff = startX - endX;
            
            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    this.nextImage();
                } else {
                    this.prevImage();
                }
            }
        }
    }

    // Initialize Premium Gallery
    new PremiumGallery();

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Share functions
const shareConfig = {
    url: window.location.href,
    title: document.title
};

function shareFB() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareConfig.url)}`, '_blank', 'width=600,height=400');
}

function shareWA() {
    window.open(`https://wa.me/?text=${encodeURIComponent(shareConfig.title + ' ' + shareConfig.url)}`, '_blank');
}

function shareIG() {
    // Instagram doesn't have direct URL sharing, so we copy to clipboard
    navigator.clipboard.writeText(shareConfig.url).then(() => {
        alert('Link copiado! Cole no Instagram.');
    });
}

function shareX() {
    window.open(`https://x.com/intent/tweet?url=${encodeURIComponent(shareConfig.url)}&text=${encodeURIComponent(shareConfig.title)}`, '_blank', 'width=600,height=400');
}

// Performance optimizations
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lozad.js/1.16.0/lozad.min.js';
    document.head.appendChild(script);
    script.onload = () => {
        const observer = lozad();
        observer.observe();
    };
}
</script>

</body>
</html>