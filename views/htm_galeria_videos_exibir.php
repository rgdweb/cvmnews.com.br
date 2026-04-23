<?php
// GALERIA DE VÍDEOS DO YOUTUBE - VERSÃO OTIMIZADA E CORRIGIDA
$model_videos = new model_videos();
$lista_videos = $model_videos->lista();
if (!$lista_videos) { echo "<p class='text-center'>Nenhum vídeo disponível no momento.</p>"; return; }
usort($lista_videos, function($a, $b) { return $b['id'] - $a['id']; });
$videos_exibidos = array_slice($lista_videos, 0, 6);
$total_videos = count($lista_videos);
?>

<style>
/* Font Awesome já carregado no topo, sem @import */

/* Título da seção de vídeos */
.video-section-title {
  display:flex;
  align-items:center;
  gap:12px;
  font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
  font-size:28px;
  font-weight:700;
  color:#00adef;
  max-width:1080px;
  margin:30px auto 15px auto;
  padding:0 40px;
  user-select:none;
  text-shadow:1px 1px 6px rgba(0,173,239,0.8);
}
.video-section-title i.fa-tv {
  font-size:34px;
  color:#00adef;
  text-shadow:1px 1px 4px rgba(0,173,239,1);
}

/* Grade da galeria */
.video-gallery {
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:25px;
  padding:40px;
  background:#000; /* fundo preto */
  border-radius:12px;
  max-width:1080px;
  margin:0 auto 50px auto;
  user-select:none;
  justify-items:center; /* centraliza thumbs */
}

/* Miniatura */
.video-thumb {
  position:relative;
  cursor:pointer;
  border-radius:12px;
  overflow:hidden;
  background:#000; /* fundo preto interno */
  width:100%;
  max-width:320px;
  aspect-ratio:16/9;
  display:flex;
  justify-content:center;
  align-items:center;
  box-shadow:0 8px 20px rgba(0,0,0,0.9),
             inset 0 -40px 60px -10px rgba(0,0,0,0.9);
  transition:transform 0.25s ease, box-shadow 0.3s ease;
}
.video-thumb:hover {
  transform:scale(1.08);
  box-shadow:0 12px 28px rgba(0,173,239,0.9),
             inset 0 -40px 80px -5px rgba(0,173,239,0.6);
  z-index:5;
}
.video-thumb img {
  width:100%;
  height:100%;
  display:block;
  object-fit:cover;
  border-radius:12px;
  user-select:none;
}

/* Ícone de play */
.video-thumb .play-icon {
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  font-size:50px;
  color:#00adef;
  opacity:0.9;
  filter:drop-shadow(0 0 6px #00adef);
  pointer-events:none;
  transition:opacity 0.3s ease,transform 0.3s ease;
}
.video-thumb:hover .play-icon {
  opacity:1;
  transform:translate(-50%,-50%) scale(1.1);
}

/* Legenda dentro da miniatura */
.video-thumb .video-title-overlay {
  position:absolute;
  bottom:0;
  width:100%;
  max-height:60%; /* até 60% da altura */
  padding:10px 12px;
  background:linear-gradient(180deg,transparent 0%,rgba(0,0,0,0.85) 100%);
  color:#fff;
  font-weight:600;
  font-size:16px;
  line-height:1.3em;
  white-space:normal;       /* quebra de linha */
  word-wrap:break-word;
  overflow-y:auto;          /* scroll se passar muito */
  display:flex;
  align-items:flex-end;     /* texto apoiado embaixo */
  border-radius:0 0 12px 12px;
  pointer-events:none;
}

/* Popup */
.video-popup-overlay {
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.95);
  display:none;
  justify-content:center;
  align-items:center;
  z-index:11000;
}
.video-popup-overlay.active {display:flex;}
.video-popup-content {
  position:relative;
  width:90%;
  max-width:960px;
  padding-bottom:56.25%;
  height:0;
  border-radius:12px;
  box-shadow:0 0 30px #00adef;
  background:#000;
}
.video-popup-content iframe {
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:100%;
  border:none;
  border-radius:12px;
}

/* Fechar */
.video-popup-close {
  position:absolute;
  top:-45px;
  right:0;
  font-size:36px;
  color:#00adef;
  cursor:pointer;
  z-index:11111;
  font-weight:700;
  text-shadow:0 0 10px #00adef;
}

/* Setas */
.video-nav-arrow {
  position:absolute;
  top:50%;
  font-size:48px;
  color:#00adef;
  cursor:pointer;
  user-select:none;
  z-index:11111;
  transform:translateY(-50%);
  padding:12px;
  background:rgba(0,0,0,0.6);
  border-radius:50%;
  transition:all 0.3s ease;
  opacity:0;
  pointer-events:none;
}
.video-nav-arrow:hover {background:#00adef;color:#000;}
.video-nav-arrow.left {left:-70px;}
.video-nav-arrow.right {right:-70px;}
.video-nav-arrow.visible {opacity:1;pointer-events:auto;}

/* Contadores */
.video-counter {
  position:absolute;
  top:-45px;
  left:50%;
  transform:translateX(-50%);
  color:#00adef;
  font-size:18px;
  font-weight:600;
  text-shadow:0 0 10px #00adef;
}
.video-title-popup {
  position:absolute;
  bottom:-75px;
  left:50%;
  transform:translateX(-50%);
  color:#fff;
  font-size:16px;
  font-weight:600;
  text-align:center;
  max-width:100%;
  text-shadow:0 0 10px rgba(0,173,239,0.8);
}

/* Som */
#soundToggleBtn {
  position:absolute;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  background:rgba(0,173,239,0.85);
  border:none;
  color:white;
  padding:14px 28px;
  border-radius:30px;
  font-weight:700;
  cursor:pointer;
  z-index:11112;
  box-shadow:0 0 14px rgba(0,173,239,0.85);
  font-size:18px;
  user-select:none;
  transition:all 0.3s ease;
  display:none;
}
#soundToggleBtn:hover {background:#009ddc;}

/* Botão ver mais */
.ver-mais-container {text-align:center;margin-bottom:50px;}
.botao_padrao {
  display:inline-block;
  padding:12px 28px;
  font-size:18px;
  background:linear-gradient(135deg,#00adef,#0088cc);
  color:white;
  text-decoration:none;
  border-radius:30px;
  font-weight:600;
  transition:all 0.3s ease;
  box-shadow:0 4px 15px rgba(0,173,239,0.3);
}
.botao_padrao:hover {
  transform:translateY(-2px);
  box-shadow:0 6px 20px rgba(0,173,239,0.5);
  background:linear-gradient(135deg,#0088cc,#00adef);
}

/* Tela cheia */
.fullscreen-toggle {
  position:absolute;
  top:20px;
  right:20px;
  font-size:24px;
  color:#00adef;
  cursor:pointer;
  z-index:11113;
  background:rgba(0,0,0,0.6);
  padding:8px;
  border-radius:50%;
  transition:all 0.3s ease;
  opacity:0;
  pointer-events:none;
}
.fullscreen-toggle:hover {background:rgba(0,173,239,0.8);color:#000;}
.fullscreen-toggle.visible {opacity:1;pointer-events:auto;}

/* Swipe mobile */
.swipe-hint {
  position:absolute;
  bottom:-45px;
  left:50%;
  transform:translateX(-50%);
  color:#00adef;
  font-size:14px;
  opacity:0;
  transition:opacity 0.3s ease;
  text-shadow:0 0 5px rgba(0,173,239,0.8);
  display:none;
  align-items:center;
  gap:8px;
}
.swipe-hint.visible {opacity:1;}
.swipe-hand {font-size:18px;animation:swipeAnimation 2s ease-in-out infinite;}
@keyframes swipeAnimation {
  0%{transform:translateX(-5px);}
  50%{transform:translateX(5px);}
  100%{transform:translateX(-5px);}
}

.controls-visible .video-nav-arrow,
.controls-visible .fullscreen-toggle {
  opacity:1;
  pointer-events:auto;
}

/* Responsividade */
@media (max-width:1000px){
  .video-gallery{grid-template-columns:repeat(2,1fr);max-width:680px;}
  .video-nav-arrow{display:none;}
  .video-popup-content{width:95%;max-width:680px;}
  .swipe-hint{display:flex;}
}
@media (max-width:600px){
  .video-gallery{grid-template-columns:1fr;max-width:320px;padding:0 20px 40px 20px;}
  .video-section-title{font-size:22px;padding:0 20px;}
  .video-section-title i.fa-tv{font-size:26px;}
  .video-popup-content{width:98%;max-width:400px;}
  .video-popup-close{top:-35px;font-size:30px;}
  .video-counter{top:-35px;font-size:16px;}
  .video-title-popup{bottom:-60px;font-size:14px;}
  .swipe-hint{bottom:-30px;font-size:12px;}
  #soundToggleBtn{padding:10px 20px;font-size:16px;}
  .video-nav-arrow{display:none;}
  .fullscreen-toggle{font-size:20px;top:15px;right:15px;}
}

html { scroll-behavior:smooth; }
</style>


<div class="foto-section-title" aria-label="Seção de Fotos da Galeria">
  <i class="fa-solid fa-camera" aria-hidden="true"></i>
  <span>VÍDEOS RECENTES</span>
</div>

<div class="video-gallery" id="videoGallery">
  <?php foreach ($videos_exibidos as $i => $video): 
    $id = trim($video['video']);
    $raw_title = $video['titulo'];
    $titulo_display = html_entity_decode($raw_title, ENT_QUOTES, 'UTF-8');
    $titulo_attr = htmlspecialchars($titulo_display, ENT_QUOTES, 'UTF-8');
    $thumb = "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
  ?>
    <div class="video-thumb" data-index="<?=$i?>" data-id="<?=$id?>" data-title="<?=$titulo_attr?>" title="<?=$titulo_attr?>" role="button" tabindex="0" aria-label="Abrir vídeo <?=$titulo_attr?>">
      <img src="<?=$thumb?>" alt="Miniatura do vídeo <?=$titulo_attr?>">
      <div class="play-icon"><i class="fa-solid fa-circle-play"></i></div>
      <div class="video-title-overlay"><?=$titulo_display?></div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($total_videos > 6): ?>
  <div class="ver-mais-container">
    <a href="#" onclick="openGaleriaVideoCompleta(); return false;" class="botao_padrao">Ver mais vídeos (<?=$total_videos?>)</a>
  </div>
<?php endif; ?>

<div class="video-popup-overlay" id="videoPopup" role="dialog" aria-modal="true" aria-labelledby="popupTitle">
  <div class="video-popup-content" id="videoPopupContent">
    <div class="video-counter" id="videoCounter">1 / 1</div>
    <button class="video-popup-close" id="closePopup" aria-label="Fechar vídeo">×</button>
    <div class="fullscreen-toggle" id="fullscreenToggle" aria-label="Tela cheia" title="Tela cheia"><i class="fa-solid fa-expand"></i></div>
    <span class="video-nav-arrow left" id="prevVideo" role="button" tabindex="0" aria-label="Vídeo anterior">❮</span>
    <span class="video-nav-arrow right" id="nextVideo" role="button" tabindex="0" aria-label="Próximo vídeo">❯</span>
    <iframe id="videoFrame" src="" allow="autoplay; encrypted-media" sandbox="allow-scripts allow-same-origin allow-presentation" title="Player de vídeo do YouTube"></iframe>
    <button id="soundToggleBtn" aria-pressed="false" aria-label="Ativar som do vídeo"><i class="fa-solid fa-volume-high"></i> Ativar Som</button>
    <div class="video-title-popup" id="videoTitlePopup">Título do Vídeo</div>
    <div class="swipe-hint" id="swipeHint">
      <span class="swipe-hand">👆</span>
      <span>Deslize para trocar de vídeo</span>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
const videoThumbs=document.querySelectorAll('.video-thumb'),popup=document.getElementById('videoPopup'),iframe=document.getElementById('videoFrame'),closeBtn=document.getElementById('closePopup'),nextBtn=document.getElementById('nextVideo'),prevBtn=document.getElementById('prevVideo'),soundToggleBtn=document.getElementById('soundToggleBtn'),videoCounter=document.getElementById('videoCounter'),videoTitlePopup=document.getElementById('videoTitlePopup'),fullscreenToggle=document.getElementById('fullscreenToggle'),popupContent=document.getElementById('videoPopupContent'),swipeHint=document.getElementById('swipeHint');
let currentIndex=-1,currentVideoArray=[],hideControlsTimeout,hintTimeout,isMobile=/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

function getEmbedUrl(id,mute=true){return `https://www.youtube.com/embed/${id}?autoplay=1&mute=${mute?1:0}&controls=1&rel=0&modestbranding=1&fs=1&iv_load_policy=3&playsinline=1`;}

function showControls(){
if(isMobile){
popupContent.classList.add('controls-visible');
clearTimeout(hideControlsTimeout);
hideControlsTimeout=setTimeout(()=>{popupContent.classList.remove('controls-visible');},3000);
}
}

function hideControls(){if(isMobile)popupContent.classList.remove('controls-visible');}

function showSwipeHint(){
if(isMobile && currentVideoArray.length > 1){
swipeHint.classList.add('visible');
clearTimeout(hintTimeout);
hintTimeout=setTimeout(()=>{swipeHint.classList.remove('visible');},4000);
}
}

function openVideo(index,videoArray=null){
if(videoArray)currentVideoArray=videoArray;
else currentVideoArray=Array.from(videoThumbs).map(thumb=>({id:thumb.getAttribute('data-id'),title:thumb.getAttribute('data-title')}));
if(index<0||index>=currentVideoArray.length)return;
const video=currentVideoArray[index];
iframe.src=getEmbedUrl(video.id,true);
popup.classList.add('active');
currentIndex=index;
soundToggleBtn.style.display='flex';
soundToggleBtn.setAttribute('aria-pressed','false');
videoTitlePopup.textContent=video.title;
updateVideoCounter();
closeBtn.focus();
if(!isMobile){
nextBtn.classList.add('visible');
prevBtn.classList.add('visible');
fullscreenToggle.classList.add('visible');
}else{
showControls();
showSwipeHint();
}
}

function closeVideo(){iframe.src='';popup.classList.remove('active');currentIndex=-1;currentVideoArray=[];soundToggleBtn.style.display='none';hideControls();clearTimeout(hideControlsTimeout);clearTimeout(hintTimeout);swipeHint.classList.remove('visible');}
function nextVideo(){if(currentVideoArray.length===0)return;openVideo((currentIndex+1)%currentVideoArray.length,currentVideoArray);}
function prevVideo(){if(currentVideoArray.length===0)return;openVideo((currentIndex-1+currentVideoArray.length)%currentVideoArray.length,currentVideoArray);}
function activateSound(){if(currentIndex===-1||currentVideoArray.length===0)return;iframe.src=getEmbedUrl(currentVideoArray[currentIndex].id,false);soundToggleBtn.style.display='none';soundToggleBtn.setAttribute('aria-pressed','true');}
function updateVideoCounter(){videoCounter.textContent=`${currentIndex+1} / ${currentVideoArray.length}`;}

function toggleFullscreen(){
if(document.fullscreenElement){
document.exitFullscreen();
}else{
document.documentElement.requestFullscreen().catch(()=>{});
}
}

videoThumbs.forEach((thumb,index)=>{
thumb.addEventListener('click',()=>openVideo(index));
thumb.addEventListener('keydown',(e)=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();openVideo(index);}});
});

closeBtn.addEventListener('click',closeVideo);
nextBtn.addEventListener('click',nextVideo);
prevBtn.addEventListener('click',prevVideo);
soundToggleBtn.addEventListener('click',activateSound);
fullscreenToggle.addEventListener('click',toggleFullscreen);

popup.addEventListener('click',(e)=>{if(e.target===popup)closeVideo();else if(isMobile)showControls();});

if(isMobile){
popupContent.addEventListener('touchstart',showControls);
popupContent.addEventListener('touchend',showControls);
}

document.addEventListener('keydown',(e)=>{
if(!popup.classList.contains('active'))return;
switch(e.key){
case 'Escape':closeVideo();break;
case 'ArrowLeft':prevVideo();break;
case 'ArrowRight':nextVideo();break;
}
});

let touchStartX=0,touchEndX=0,touchStartY=0,touchEndY=0;
popup.addEventListener('touchstart',(e)=>{
touchStartX=e.changedTouches[0].screenX;
touchStartY=e.changedTouches[0].screenY;
},{passive:true});

popup.addEventListener('touchend',(e)=>{
touchEndX=e.changedTouches[0].screenX;
touchEndY=e.changedTouches[0].screenY;
const diffX=touchStartX-touchEndX,diffY=touchStartY-touchEndY,threshold=50;
if(Math.abs(diffX)>Math.abs(diffY)&&Math.abs(diffX)>threshold){
if(diffX>0)nextVideo();
else prevVideo();
swipeHint.classList.remove('visible');
}
},{passive:true});

window.openGaleriaVideoCompleta=function(){
const allVideos=<?php echo json_encode(array_map(function($video){return ['id'=>trim($video['video']),'title'=>html_entity_decode($video['titulo'],ENT_QUOTES,'UTF-8')];}, $lista_videos)); ?>;
openVideo(0,allVideos);
};

document.addEventListener('dragstart',(e)=>{if(e.target.tagName==='IMG')e.preventDefault();});
});
</script>