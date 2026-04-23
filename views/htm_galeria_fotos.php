<?php
// GALERIA DE FOTOS - INTEGRAÇÃO COM POPUP E NAVEGAÇÃO
$fotos_exibidas = array_slice($fotos, 0, 6);
$total_fotos = count($fotos);
?>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
.foto-section-title{display:flex;align-items:center;gap:12px;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:28px;font-weight:700;color:#00adef;max-width:1080px;margin:30px auto 15px auto;padding:0 40px;user-select:none;text-shadow:1px 1px 6px rgba(0,173,239,0.8)}
.foto-section-title i.fa-camera{font-size:32px;color:#00adef;text-shadow:1px 1px 4px rgba(0,173,239,1)}
.foto-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:25px;padding:0 40px 50px 40px;background:#111;border-radius:12px;max-width:1080px;margin:0 auto 50px auto;user-select:none}
.foto-thumb{position:relative;cursor:pointer;border-radius:12px;overflow:hidden;box-shadow:0 8px 20px rgba(0,0,0,0.9),inset 0 -40px 60px -10px rgba(0,0,0,0.9);transition:transform 0.25s ease,box-shadow 0.3s ease;background:#222;aspect-ratio:1}
.foto-thumb:hover{transform:scale(1.08);box-shadow:0 12px 28px rgba(0,173,239,0.9),inset 0 -40px 80px -5px rgba(0,173,239,0.6);z-index:5}
.foto-thumb img{width:100%;height:100%;display:block;object-fit:cover;border-radius:12px;user-select:none}
.foto-thumb .expand-icon{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:40px;color:#00adef;opacity:0;filter:drop-shadow(0 0 6px #00adef);pointer-events:none;transition:opacity 0.3s ease}
.foto-thumb:hover .expand-icon{opacity:0.9}
.foto-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);display:none;justify-content:center;align-items:center;z-index:11000}
.foto-popup-overlay.active{display:flex}
.foto-popup-content{position:relative;max-width:90%;max-height:90%;display:flex;align-items:center;justify-content:center}
.foto-popup-content img{max-width:100%;max-height:100%;border-radius:12px;box-shadow:0 0 30px #00adef;object-fit:contain}
.foto-popup-close{position:absolute;top:-45px;right:0;font-size:36px;color:#00adef;cursor:pointer;z-index:11111;font-weight:700;text-shadow:0 0 10px #00adef}
.foto-nav-arrow{position:absolute;top:50%;font-size:48px;color:#00adef;cursor:pointer;user-select:none;z-index:11111;transform:translateY(-50%);padding:12px;background:rgba(0,0,0,0.35);border-radius:50%;transition:background-color 0.3s ease}
.foto-nav-arrow:hover{background-color:#00adef;color:#000}
.foto-nav-arrow.left{left:-70px}
.foto-nav-arrow.right{right:-70px}
.foto-counter{position:absolute;top:-45px;left:50%;transform:translateX(-50%);color:#00adef;font-size:18px;font-weight:600;text-shadow:0 0 10px #00adef}
.ver-mais-container{text-align:center;margin-bottom:50px}
.botao_padrao{display:inline-block;padding:12px 28px;font-size:18px;background:linear-gradient(135deg,#00adef,#0088cc);color:white;text-decoration:none;border-radius:30px;font-weight:600;transition:all 0.3s ease;box-shadow:0 4px 15px rgba(0,173,239,0.3)}
.botao_padrao:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,173,239,0.5);background:linear-gradient(135deg,#0088cc,#00adef)}
@media (max-width:1000px){
.foto-gallery{grid-template-columns:repeat(2,1fr);max-width:680px}
.foto-nav-arrow{display:none}
.foto-popup-content{max-width:95%;max-height:85%}
}
@media (max-width:600px){
.foto-gallery{grid-template-columns:1fr;max-width:320px;padding:0 20px 40px 20px}
.foto-section-title{font-size:22px}
.foto-section-title i.fa-camera{font-size:26px}
.foto-popup-content{max-width:98%;max-height:80%}
.foto-popup-close{top:-35px;font-size:30px}
.foto-counter{top:-35px;font-size:16px}
}
</style>

<div class="foto-section-title" aria-label="Seção de Fotos da Galeria">
  <i class="fa-solid fa-camera" aria-hidden="true"></i>
  <span>GALERIA | ÚLTIMAS FOTOS</span>
</div>

<div class="foto-gallery" id="fotoGallery">
  <?php foreach ($fotos_exibidas as $index => $foto): ?>
    <div class="foto-thumb" data-index="<?=$index?>" data-src="<?=$foto?>" title="Foto <?=$index+1?>" role="button" tabindex="0" aria-label="Abrir foto <?=$index+1?>">
      <img src="<?=$foto?>" alt="Foto <?=$index+1?>">
      <div class="expand-icon"><i class="fa-solid fa-expand"></i></div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($total_fotos > 6): ?>
  <div class="ver-mais-container">
    <a href="#" onclick="openGaleriaCompleta(); return false;" class="botao_padrao">Ver mais fotos (<?=$total_fotos?>)</a>
  </div>
<?php endif; ?>

<div class="foto-popup-overlay" id="fotoPopup" role="dialog" aria-modal="true" aria-labelledby="fotoPopupTitle">
  <div class="foto-popup-content">
    <button class="foto-popup-close" id="closeFotoPopup" aria-label="Fechar foto">×</button>
    <span class="foto-nav-arrow left" id="prevFoto" role="button" tabindex="0" aria-label="Foto anterior">❮</span>
    <span class="foto-nav-arrow right" id="nextFoto" role="button" tabindex="0" aria-label="Próxima foto">❯</span>
    <img id="fotoPopupImage" src="" alt="Foto em tela cheia">
    <div class="foto-counter" id="fotoCounter">1 / 1</div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded',()=>{
const fotoThumbs=document.querySelectorAll('.foto-thumb'),fotoPopup=document.getElementById('fotoPopup'),fotoPopupImage=document.getElementById('fotoPopupImage'),closeFotoBtn=document.getElementById('closeFotoPopup'),nextFotoBtn=document.getElementById('nextFoto'),prevFotoBtn=document.getElementById('prevFoto'),fotoCounter=document.getElementById('fotoCounter');
let currentFotoIndex=-1,currentFotoArray=[],isMobile=window.innerWidth<=1000||/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

let swipeMessageShown = false;

function showSwipeMessage(){
    if(isMobile && currentFotoArray.length > 1 && !swipeMessageShown){
        const swipeMessage = document.createElement('div');
        swipeMessage.innerHTML = '<span class="swipe-hand">👆</span> Deslize para trocar de foto';
        swipeMessage.style.position = 'absolute';
        swipeMessage.style.top = '50%';
        swipeMessage.style.left = '50%';
        swipeMessage.style.transform = 'translate(-50%, -50%)';
        swipeMessage.style.color = '#00adef';
        swipeMessage.style.fontSize = '14px';
        swipeMessage.style.padding = '10px';
        swipeMessage.style.borderRadius = '10px';
        swipeMessage.style.background = 'rgba(0, 0, 0, 0.5)';
        swipeMessage.style.zIndex = '11111';
        swipeMessage.style.display = 'flex';
        swipeMessage.style.alignItems = 'center';
        swipeMessage.style.justifyContent = 'center';
        fotoPopup.appendChild(swipeMessage);
        let phase = 0;
        const intervalId = setInterval(()=>{
            switch(phase){
                case 0:
                    swipeMessage.querySelector('.swipe-hand').style.transform = 'translateX(0px)';
                    phase = 1;
                    break;
                case 1:
                    swipeMessage.querySelector('.swipe-hand').style.transform = 'translateX(10px)';
                    phase = 2;
                    break;
                case 2:
                    swipeMessage.querySelector('.swipe-hand').style.transform = 'translateX(20px)';
                    phase = 3;
                    break;
                case 3:
                    swipeMessage.querySelector('.swipe-hand').style.transform = 'translateX(10px)';
                    phase = 0;
                    break;
            }
        }, 200);
        setTimeout(()=>{
            clearInterval(intervalId);
            swipeMessage.remove();
        }, 3000);
        swipeMessageShown = true;
    }
}
function openFoto(index, fotosArray = null){
    if(fotosArray)currentFotoArray=fotosArray;
    else currentFotoArray=Array.from(fotoThumbs).map(thumb=>thumb.getAttribute('data-src'));
    if(index<0||index>=currentFotoArray.length)return;
    const fotoSrc=currentFotoArray[index];
    fotoPopupImage.src=fotoSrc;
    fotoPopup.classList.add('active');
    currentFotoIndex=index;
    updateFotoCounter();
    closeFotoBtn.focus();
    showSwipeMessage();
}

function closeFoto(){
    fotoPopupImage.src='';
    fotoPopup.classList.remove('active');
    currentFotoIndex=-1;
    currentFotoArray=[];
}

function nextFoto(){
    if(currentFotoArray.length===0)return;
    const newIndex=(currentFotoIndex+1)%currentFotoArray.length;
    openFoto(newIndex,currentFotoArray);
}

function prevFoto(){
    if(currentFotoArray.length===0)return;
    const newIndex=(currentFotoIndex-1+currentFotoArray.length)%currentFotoArray.length;
    openFoto(newIndex,currentFotoArray);
}

function updateFotoCounter(){fotoCounter.textContent=`${currentFotoIndex+1} / ${currentFotoArray.length}`;}

fotoThumbs.forEach((thumb,index)=>{
    thumb.addEventListener('click',()=>openFoto(index));
    thumb.addEventListener('keydown',(e)=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();openFoto(index);}});
});

closeFotoBtn.addEventListener('click',closeFoto);
nextFotoBtn.addEventListener('click',nextFoto);
prevFotoBtn.addEventListener('click',prevFoto);

fotoPopup.addEventListener('click',(e)=>{if(e.target===fotoPopup)closeFoto();});

document.addEventListener('keydown',(e)=>{
    if(!fotoPopup.classList.contains('active'))return;
    switch(e.key){
        case 'Escape':closeFoto();break;
        case 'ArrowLeft':prevFoto();break;
        case 'ArrowRight':nextFoto();break;
    }
});

let touchStartX=0,touchEndX=0;
fotoPopup.addEventListener('touchstart',(e)=>{
    touchStartX=e.changedTouches[0].screenX;
},{passive:true});

fotoPopup.addEventListener('touchend',(e)=>{
    touchEndX=e.changedTouches[0].screenX;
    const diff=touchStartX-touchEndX,threshold=50;
    if(Math.abs(diff)>threshold){
        if(diff>0)nextFoto();
        else prevFoto();
    }
},{passive:true});

window.openGaleriaCompleta=function(){
    const allFotos=<?php echo json_encode($fotos); ?>;
    openFoto(0,allFotos);
};

document.addEventListener('dragstart',(e)=>{if(e.target.tagName==='IMG')e.preventDefault();});
});
</script>