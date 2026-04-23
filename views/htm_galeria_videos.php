<?php
// GALERIA DE VÍDEOS DO YOUTUBE - INTEGRAÇÃO COM model_videos.php

// Conexão real com o banco de dados através da sua classe
$model_videos = new model_videos();
$lista_videos = $model_videos->lista();

// Verifica se a busca no banco de dados retornou algum resultado
if (!$lista_videos) {
  echo "<p class='text-center'>Nenhum vídeo disponível no momento.</p>";
  return;
}

// Limitar a exibição a um máximo de 9 vídeos
$lista_videos = array_slice($lista_videos, 0, 300);
?>

<!-- CSS -->
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.video-section-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 28px;
  font-weight: 700;
  color: #00adef;
  margin: 30px 40px 15px 40px;
  user-select: none;
  text-shadow: 1px 1px 6px rgba(0, 173, 239, 0.8);
}
.video-section-title i.fa-tv {
  font-size: 34px;
  color: #00adef;
  text-shadow: 1px 1px 4px rgba(0, 173, 239, 1);
}

.video-gallery {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 25px;
  padding: 0 40px 50px 40px;
  background: #111;
  border-radius: 12px;
  max-width: 1080px;
  margin: 0 auto 50px auto;
  user-select: none;
}

.video-thumb {
  position: relative;
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  box-shadow:
    0 8px 20px rgba(0,0,0,0.9),
    inset 0 -40px 60px -10px rgba(0,0,0,0.9);
  transition: transform 0.25s ease, box-shadow 0.3s ease;
  background: #222;
}
.video-thumb:hover {
  transform: scale(1.08);
  box-shadow:
    0 12px 28px rgba(0,173,239,0.9),
    inset 0 -40px 80px -5px rgba(0,173,239,0.6);
  z-index: 5;
}

.video-thumb img {
  width: 100%;
  height: auto;
  display: block;
  object-fit: cover;
  border-radius: 12px 12px 0 0;
  user-select: none;
}

.video-thumb .play-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 50px;
  color: #00adef;
  opacity: 0.9;
  filter: drop-shadow(0 0 6px #00adef);
  pointer-events: none;
}

.video-thumb .video-title-overlay {
  position: absolute;
  bottom: 0;
  width: 100%;
  padding: 10px 12px;
  background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.85) 90%);
  color: #fff;
  font-weight: 600;
  font-size: 16px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  user-select: text;
  border-radius: 0 0 12px 12px;
  pointer-events: none;
}

.video-popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.95);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 11000;
}
.video-popup-overlay.active {
  display: flex;
}
.video-popup-content {
  position: relative;
  width: 90%;
  max-width: 960px;
  padding-bottom: 56.25%; /* 16:9 */
  height: 0;
  border-radius: 12px;
  box-shadow: 0 0 30px #00adef;
  background-color: #000;
}
.video-popup-content iframe {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  border: none;
  border-radius: 12px;
}
.video-popup-close {
  position: absolute;
  top: -45px;
  right: 0;
  font-size: 36px;
  color: #00adef;
  cursor: pointer;
  z-index: 11111;
  font-weight: 700;
  text-shadow: 0 0 10px #00adef;
}
.nav-arrow {
  position: absolute;
  top: 50%;
  font-size: 48px;
  color: #00adef;
  cursor: pointer;
  user-select: none;
  z-index: 11111;
  transform: translateY(-50%);
  padding: 12px;
  background: rgba(0,0,0,0.35);
  border-radius: 50%;
  transition: background-color 0.3s ease;
}
.nav-arrow:hover {
  background-color: #00adef;
  color: #000;
}
.nav-arrow.left {
  left: -70px;
}
.nav-arrow.right {
  right: -70px;
}

#soundToggleBtn {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(0, 173, 239, 0.85);
  border: none;
  color: white;
  padding: 14px 28px;
  border-radius: 30px;
  font-weight: 700;
  cursor: pointer;
  z-index: 11112;
  box-shadow: 0 0 14px rgba(0, 173, 239, 0.85);
  font-size: 18px;
  user-select: none;
  transition: background-color 0.3s ease, opacity 0.3s ease;
  display: none;
}
#soundToggleBtn:hover {
  background: #009ddc;
}

@media (max-width: 1000px) {
  .video-gallery {
    grid-template-columns: repeat(2, 1fr);
    max-width: 680px;
  }
  .nav-arrow.left, .nav-arrow.right {
    display: none;
  }
}
@media (max-width: 600px) {
  .video-gallery {
    grid-template-columns: 1fr;
    max-width: 320px;
    padding: 0 20px 40px 20px;
  }
  .video-section-title {
    font-size: 22px;
  }
  .video-section-title i.fa-tv {
    font-size: 26px;
  }
}
</style>

<!-- TÍTULO COM ÍCONE -->
<div class="video-section-title" aria-label="Seção de Vídeos TV">
  <i class="fa-solid fa-tv" aria-hidden="true"></i>
  <span>TV | VÍDEOS</span>
</div>

<!-- GALERIA DE MINIATURAS (COM A LÓGICA CORRETA PARA DADOS DO BANCO) -->
<div class="video-gallery" id="videoGallery">
  <?php foreach ($lista_videos as $i => $video): 
    $id = trim($video['video']);
    $raw_title = $video['titulo'];

    // Decodifica entidades HTML para exibição correta (Ex: í vira í)
    $titulo_display = html_entity_decode($raw_title, ENT_QUOTES, 'UTF-8');
    
    // Escapa a versão já decodificada para segurança nos atributos HTML
    $titulo_attr = htmlspecialchars($titulo_display, ENT_QUOTES, 'UTF-8');
    
    $thumb = "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
  ?>
    <div class="video-thumb" data-index="<?=$i?>" data-id="<?=$id?>" title="<?=$titulo_attr?>" role="button" tabindex="0" aria-label="Abrir vídeo <?=$titulo_attr?>">
      <img src="<?=$thumb?>" alt="Miniatura do vídeo <?=$titulo_attr?>">
      <div class="play-icon"><i class="fa-solid fa-circle-play"></i></div>
      
      <!-- Usa a versão decodificada para o texto visual -->
      <div class="video-title-overlay"><?=$titulo_display?></div>

    </div>
  <?php endforeach; ?>
</div>

<!-- POPUP -->
<div class="video-popup-overlay" id="videoPopup" role="dialog" aria-modal="true" aria-labelledby="popupTitle">
  <div class="video-popup-content">
    <button class="video-popup-close" id="closePopup" aria-label="Fechar vídeo">×</button>
    <span class="nav-arrow left" id="prevVideo" role="button" tabindex="0" aria-label="Vídeo anterior">❮</span>
    <span class="nav-arrow right" id="nextVideo" role="button" tabindex="0" aria-label="Próximo vídeo">❯</span>
    
    <iframe id="videoFrame" src="" 
      allow="autoplay; encrypted-media"
      sandbox="allow-scripts allow-same-origin allow-presentation"
      title="Player de vídeo do YouTube bloqueado">
    </iframe>

    <button id="soundToggleBtn" aria-pressed="false" aria-label="Ativar som do vídeo">
      <i class="fa-solid fa-volume-high"></i> Ativar Som
    </button>
  </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const thumbs = document.querySelectorAll('.video-thumb');
  const popup = document.getElementById('videoPopup');
  const iframe = document.getElementById('videoFrame');
  const closeBtn = document.getElementById('closePopup');
  const nextBtn = document.getElementById('nextVideo');
  const prevBtn = document.getElementById('prevVideo');
  const soundToggleBtn = document.getElementById('soundToggleBtn');

  let currentIndex = -1;
  const thumbsArray = Array.from(thumbs);

  function getEmbedUrl(id, mute = true) {
    const muteParam = mute ? 1 : 0;
    return `https://www.youtube.com/embed/${id}?autoplay=1&mute=${muteParam}&controls=0&rel=0&modestbranding=1&disablekb=1&fs=0&iv_load_policy=3&playsinline=1`;
  }

  function openVideo(index) {
    if (index < 0 || index >= thumbsArray.length) return;

    const thumb = thumbsArray[index];
    const id = thumb.getAttribute('data-id');
    
    iframe.src = getEmbedUrl(id, true);
    popup.classList.add('active');
    currentIndex = index;

    soundToggleBtn.style.display = 'flex';
    soundToggleBtn.setAttribute('aria-pressed', 'false');

    closeBtn.focus();
  }

  function closeVideo() {
    iframe.src = '';
    popup.classList.remove('active');
    currentIndex = -1;
    soundToggleBtn.style.display = 'none';
  }

  function nextVideo() {
    const newIndex = (currentIndex + 1) % thumbsArray.length;
    openVideo(newIndex);
  }

  function prevVideo() {
    const newIndex = (currentIndex - 1 + thumbsArray.length) % thumbsArray.length;
    openVideo(newIndex);
  }

  function activateSound() {
    if (currentIndex === -1) return;
    const id = thumbsArray[currentIndex].getAttribute('data-id');
    iframe.src = getEmbedUrl(id, false);
    
    soundToggleBtn.style.display = 'none';
    soundToggleBtn.setAttribute('aria-pressed', 'true');
  }

  thumbsArray.forEach((thumb, i) => {
    thumb.addEventListener('click', () => openVideo(i));
    thumb.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openVideo(i);
      }
    });
  });

  closeBtn.addEventListener('click', closeVideo);
  popup.addEventListener('click', (e) => {
    if (e.target === popup) closeVideo();
  });

  nextBtn.addEventListener('click', nextVideo);
  prevBtn.addEventListener('click', prevVideo);
  soundToggleBtn.addEventListener('click', activateSound);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && popup.classList.contains('active')) {
      closeVideo();
    }
  });
});
</script>