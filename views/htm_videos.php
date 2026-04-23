<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- /Tudo em um único pacote -->
  <title>GALERIA VIDEOS - <?=$_base['titulo_pagina']?></title>
  <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478'];?>">   
  <title><?=$_base['titulo_pagina']?></title>
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
    
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* CSS Minificado */
*{box-sizing:border-box}
.video-gallery-container{background:linear-gradient(135deg,#0c0c0c 0%,#1a1a1a 100%);min-height:100vh;padding:40px 0;font-family:'Inter',sans-serif}
.video-section-header{text-align:center;margin-bottom:50px;padding:0 20px}.video-main-title{font-size:clamp(32px,5vw,48px);font-weight:800;color:#ffffff;margin-bottom:15px;text-shadow:0 4px 20px rgba(0,173,239,0.6);letter-spacing:-0.5px;background:linear-gradient(135deg,#ffffff,#00adef);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}.video-main-title i{margin-right:20px;color:#00adef;filter:drop-shadow(0 0 10px #00adef)}.video-subtitle{font-size:18px;color:#b3b3b3;font-weight:400;max-width:600px;margin:0 auto;line-height:1.6}
.videos-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:2rem;max-width:1400px;margin:0 auto;padding:0 20px}
.video-card{background:rgba(255,255,255,0.05);border-radius:20px;overflow:hidden;transition:all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(10px);position:relative;cursor:pointer}.video-card:hover{transform:translateY(-10px) scale(1.02);box-shadow:0 25px 50px rgba(0,212,255,0.2);border-color:rgba(0,212,255,0.3)}
.video-thumbnail{position:relative;width:100%;height:220px;background:linear-gradient(45deg,#1a1a1a,#2a2a2a);display:flex;align-items:center;justify-content:center;overflow:hidden}.video-thumbnail img{width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease}.video-card:hover .video-thumbnail img{transform:scale(1.05)}
.video-thumbnail::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background:linear-gradient(135deg,rgba(0,212,255,0.1),rgba(0,173,239,0.1));z-index:1}
.play-button{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:70px;height:70px;background:linear-gradient(135deg,#00d4ff,#00adef);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s ease;z-index:2;box-shadow:0 10px 30px rgba(0,212,255,0.4)}.play-button:hover{transform:translate(-50%,-50%) scale(1.1);box-shadow:0 15px 40px rgba(0,212,255,0.6)}.play-button i{font-size:24px;color:white;margin-left:3px}
.video-info{padding:1.5rem}.video-title{font-size:1.3rem;font-weight:700;color:#ffffff;margin-bottom:0.5rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}.video-meta{margin-top:8px;color:#888;font-size:14px;font-weight:400}
.load-more-container{text-align:center;margin:60px 0;padding:0 20px}.load-more-btn{background:linear-gradient(135deg,#00adef,#0099cc);color:white;border:none;padding:16px 40px;border-radius:30px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s ease;box-shadow:0 6px 20px rgba(0,173,239,0.3)}.load-more-btn:hover{transform:translateY(-3px)}.loading-text{color:#00adef;font-size:16px;font-weight:500}.all-loaded{color:#666;font-size:14px}
.video-modal{position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.95);display:none;align-items:center;justify-content:center;z-index:99999}.video-modal.active{display:flex}.modal-content{position:relative;width:90%;max-width:1200px;aspect-ratio:16/9;background:#000;border-radius:20px;overflow:hidden;box-shadow:0 0 50px rgba(0,173,239,0.5)}.modal-content iframe{width:100%;height:100%;border:none}
.close-modal{position:absolute;top:-60px;right:0;color:#ffffff;font-size:36px;cursor:pointer;width:50px;height:50px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(255,255,255,0.1);transition:all 0.3s ease;z-index:99999}.close-modal:hover{background:rgba(255,255,255,0.2)}
.modal-nav{position:absolute;top:50%;transform:translateY(-50%);color:#ffffff;font-size:24px;cursor:pointer;width:50px;height:50px;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);border-radius:50%;transition:all 0.3s ease;z-index:99999}.modal-nav:hover{background:rgba(0,173,239,0.8)}.modal-nav.prev{left:-70px}.modal-nav.next{right:-70px}
.sound-button{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,173,239,0.9);color:white;border:none;padding:12px 24px;border-radius:25px;font-size:14px;font-weight:600;cursor:pointer;display:none;transition:all 0.3s ease;z-index:99999}
.gb{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#00d4ff,#00adef);border:none;border-radius:25px;color:#fff;font-weight:600;font-size:16px;text-decoration:none;box-shadow:0 4px 15px rgba(0,173,239,.3);transition:all .3s ease;cursor:pointer}.gb:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,173,239,.4);color:#fff;text-decoration:none}
.share-container{display:flex;gap:10px;align-items:center;margin:30px 0;padding:20px;background:rgba(255,255,255,0.05);border-radius:15px;backdrop-filter:blur(10px)}.share-btn{display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:12px;text-decoration:none;color:white;font-size:18px;transition:all 0.3s ease;box-shadow:0 4px 12px rgba(0,0,0,0.3)}.share-btn:hover{transform:translateY(-3px);box-shadow:0 8px 25px rgba(0,0,0,0.4)}.facebook{background:linear-gradient(135deg,#1877f2,#42a5f5)}.whatsapp{background:linear-gradient(135deg,#25d366,#4caf50)}.instagram{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}.x-twitter{background:linear-gradient(135deg,#000000,#333333)}.share-label2{margin-right:15px;font-weight:700;font-size:16px;color:#000000;text-shadow:none}

@media(max-width:768px){.videos-grid{grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;padding:0 20px}.modal-nav{display:none}.modal-content{width:95%}.close-modal{top:-50px;font-size:30px}.gb{padding:10px 20px;font-size:14px}}
@media(max-width:480px){.videos-grid{grid-template-columns:1fr;padding:0 15px}.share-container{margin:20px 0;padding:15px}.share-btn{width:40px;height:40px;font-size:16px}}
</style>

  <?php require_once('_css_padrao.php'); ?>
  <?php require_once('_css_personalizado.php'); ?>
</head>
<body>
  <?php require_once('htm_modal.php'); ?>  
  <?php require_once('htm_topo2.php'); ?>

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
                    <div class="mt-4 pb-5">
                      
<?php
$model_videos = new model_videos();
$lista_videos = $model_videos->lista();
if (!$lista_videos) { 
    echo "<div class='video-gallery-container'><div class='video-section-header'><h1 class='video-main-title'><i class='fas fa-video'></i>GALERIA DE VÍDEOS</h1><p class='video-subtitle'>Nenhum vídeo disponível no momento.</p></div></div>"; 
    return; 
}
usort($lista_videos, function($a,$b) { return ($b['id'] ?? 0) - ($a['id'] ?? 0); });
?>

<div class="video-gallery-container">
  <div class="video-section-header">
    <h1 class="video-main-title">
      <i class="fas fa-play-circle"></i>GALERIA DE VÍDEOS CVM
    </h1>
    <p class="video-subtitle">Descubra uma coleção exclusiva de conteúdos em alta qualidade</p>
  </div>

  <div class="videos-grid" id="videosGrid"></div>
  
  <div class="load-more-container" id="loadMoreContainer">
    <button class="load-more-btn" onclick="loadMoreVideos()">
      <i class="fas fa-plus-circle" style="margin-right:8px"></i>
      Carregar Mais Vídeos
    </button>
  </div>
</div>

<!-- Modal do Player -->
<div class="video-modal" id="videoModal">
  <div class="modal-content">
    <div class="close-modal" onclick="closeVideoModal()" title="Fechar (ESC)">
      <i class="fas fa-times"></i>
    </div>
    <div class="modal-nav prev" onclick="previousVideo()" title="Vídeo Anterior (←)">
      <i class="fas fa-chevron-left"></i>
    </div>
    <div class="modal-nav next" onclick="nextVideo()" title="Próximo Vídeo (→)">
      <i class="fas fa-chevron-right"></i>
    </div>
    <iframe id="videoFrame" allow="autoplay; encrypted-media" sandbox="allow-scripts allow-same-origin allow-presentation"></iframe>
    <button class="sound-button" id="soundButton" onclick="enableSound()">
      <i class="fas fa-volume-up"></i> Ativar Som
    </button>
  </div>
</div>

<script>
const videos = <?=json_encode(array_values($lista_videos))?>;
let currentVideoIndex = -1, displayedVideos = 0;
const videosPerPage = 6;

function getYouTubeEmbedUrl(videoId, muted = 1) {
  return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=${muted}&controls=1&rel=0&modestbranding=1&playsinline=1`;
}

function createVideoCard(video, index) {
  const title = video.titulo.replace(/&quot;/g, '"').replace(/&#39;/g, "'").replace(/&amp;/g, '&');
  return `
    <div class="video-card" onclick="openVideo(${index})">
      <div class="video-thumbnail">
        <img src="https://img.youtube.com/vi/${video.video}/maxresdefault.jpg" alt="${title}" loading="lazy" onerror="this.src='https://img.youtube.com/vi/${video.video}/hqdefault.jpg'">
        <div class="play-button">
          <i class="fas fa-play"></i>
        </div>
      </div>
      <div class="video-info">
        <h3 class="video-title">${title}</h3>
        <div class="video-meta">
          <i class="fas fa-play-circle" style="margin-right:5px;color:#00adef"></i>
          Assistir agora
        </div>
      </div>
    </div>
  `;
}

function loadMoreVideos() {
  const container = document.getElementById('videosGrid');
  const loadMoreContainer = document.getElementById('loadMoreContainer');
  
  loadMoreContainer.innerHTML = '<div class="loading-text"><i class="fas fa-spinner fa-spin" style="margin-right:8px"></i>Carregando...</div>';
  
  setTimeout(() => {
    const endIndex = Math.min(displayedVideos + videosPerPage, videos.length);
    
    for (let i = displayedVideos; i < endIndex; i++) {
      container.innerHTML += createVideoCard(videos[i], i);
    }
    
    displayedVideos = endIndex;
    
    if (displayedVideos >= videos.length) {
      loadMoreContainer.innerHTML = '<div class="all-loaded"><i class="fas fa-check-circle" style="margin-right:8px;color:#4caf50"></i>Todos os vídeos carregados</div>';
    } else {
      loadMoreContainer.innerHTML = `
        <button class="load-more-btn" onclick="loadMoreVideos()">
          <i class="fas fa-plus-circle" style="margin-right:8px"></i>
          Carregar Mais (${videos.length - displayedVideos} restantes)
        </button>
      `;
    }
  }, 500);
}

function openVideo(index) {
  currentVideoIndex = index;
  const videoFrame = document.getElementById('videoFrame');
  const modal = document.getElementById('videoModal');
  const soundButton = document.getElementById('soundButton');
  
  videoFrame.src = getYouTubeEmbedUrl(videos[index].video);
  modal.classList.add('active');
  soundButton.style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeVideoModal() {
  const videoFrame = document.getElementById('videoFrame');
  const modal = document.getElementById('videoModal');
  
  videoFrame.src = '';
  modal.classList.remove('active');
  document.getElementById('soundButton').style.display = 'none';
  currentVideoIndex = -1;
  document.body.style.overflow = 'auto';
}

function nextVideo() {
  if (currentVideoIndex < videos.length - 1) openVideo(currentVideoIndex + 1);
}

function previousVideo() {
  if (currentVideoIndex > 0) openVideo(currentVideoIndex - 1);
}

function enableSound() {
  if (currentVideoIndex >= 0) {
    document.getElementById('videoFrame').src = getYouTubeEmbedUrl(videos[currentVideoIndex].video, 0);
    document.getElementById('soundButton').style.display = 'none';
  }
}

// Event Listeners otimizados
document.addEventListener('keydown', (e) => {
  if (document.getElementById('videoModal').classList.contains('active')) {
    if (e.key === 'Escape') closeVideoModal();
    else if (e.key === 'ArrowLeft') previousVideo();
    else if (e.key === 'ArrowRight') nextVideo();
  }
});

document.getElementById('videoModal').onclick = (e) => {
  if (e.target.id === 'videoModal') closeVideoModal();
};

// Inicializar
loadMoreVideos();
</script>

<div class="share-container">
  <span class="share-label2">
    <i class="fas fa-share-alt" style="margin-right:8px"></i>
    Compartilhe esta galeria:
  </span>
  <a href="#" class="share-btn facebook" onclick="shareFacebook()" title="Compartilhar no Facebook">
    <i class="fab fa-facebook"></i>
  </a>
  <a href="#" class="share-btn whatsapp" onclick="shareWhatsApp()" title="Compartilhar no WhatsApp">
    <i class="fab fa-whatsapp"></i>
  </a>
  <a href="#" class="share-btn instagram" onclick="shareInstagram()" title="Compartilhar no Instagram">
    <i class="fab fa-instagram"></i>
  </a>
  <a href="#" class="share-btn x-twitter" onclick="shareTwitter()" title="Compartilhar no X">
    <i class="fab fa-x"></i>
  </a>
</div>

<div style="text-align:center;margin:40px 0">
  <a href="https://cvmnews.com.br/galeria" class="gb">
    <i class="fas fa-camera"></i>
    GALERIA DE FOTOS
  </a>
</div>

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

  <?php include_once('htm_rodape.php'); ?>
  <div class="fixed-menu"></div>

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
  <script src="https://www.google.com/recaptcha/api.js"></script>
  
  <script>
    function dominio(){ return "<?=DOMINIO?>"; }
    
    // Compartilhamento
    const shareConfig = { url: window.location.href, title: document.title };

    function shareFacebook() {
      window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareConfig.url)}`, '_blank', 'width=600,height=400');
    }

    function shareWhatsApp() {
      window.open(`https://wa.me/?text=${encodeURIComponent(shareConfig.title + ' ' + shareConfig.url)}`, '_blank');
    }

    function shareTwitter() {
      window.open(`https://x.com/intent/tweet?url=${encodeURIComponent(shareConfig.url)}&text=${encodeURIComponent(shareConfig.title)}`, '_blank', 'width=600,height=400');
    }

    function shareInstagram() {
      navigator.clipboard.writeText(shareConfig.url).then(() => {
        alert('Link copiado! Cole no Instagram para compartilhar.');
      });
    }

    // jQuery Inicialização
    jQuery(document).ready(function() {
      jQuery('.fullscreen_slider').show().revolution({
        delay: 5000,
        startwidth: 1366,
        startheight: 650,
        fullWidth:"on",
        fullScreen:"off",
        navigationType:"bullet",
        fullScreenOffsetContainer: ".main_header",
        fullScreenOffset: ""
      });
      
      jQuery('.testimonials-info').slick({ 
        fade: true, 
        arrows: false, 
        asNavFor: '.testimonials-nav', 
        adaptiveHeight: true 
      });
      
      let visibl_show = 5;
      let count_els = jQuery('.testimonials-nav .nav_item_wrap').length;      
      let center_true = count_els > visibl_show && visibl_show % 2 == 1;
      
      jQuery('.testimonials-nav').slick({       
        slidesToShow: Math.min(visibl_show, count_els),
        asNavFor: '.testimonials-info',
        centerMode: center_true,
        focusOnSelect: true,
        autoplay: true,
        responsive: [
          { breakpoint: 980, settings: { slidesToShow: 3 } },
          { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } }
        ]
      });
    });

    $('a.scrollSuave').on('click', function(event) {
      let target = $($(this).attr('href'));
      if (target.length) {
        event.preventDefault();
        $('html, body').animate({ scrollTop: target.offset().top }, 500);
      }
    });

    function envia_contato(){
      $('#modal_conteudo').html("<div class='text-center'><img src='<?=LAYOUT?>img/loading.gif' width='25'></div>");
      $('#janela_modal').modal('show');
      let dados = $("#formcontato").serialize();
      $.post('<?=DOMINIO?>faleconosco/enviar', dados, function(data){
        if(data){ $('#modal_conteudo').html(data); }
      });
    }
  </script>
</body>
</html>