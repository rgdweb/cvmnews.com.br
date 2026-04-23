<?php
if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; }

define("PASTA_CLIENTE", $config_dominio."sistema/arquivos/");

$ultimas_fotos = [];
$db = new mysql();

$sql = "SELECT g.codigo AS grupo_codigo, g.titulo AS grupo_titulo, a.codigo AS album_codigo, a.titulo AS album_titulo, i.imagem FROM fotos_grupos g LEFT JOIN fotos a ON a.grupo = g.codigo LEFT JOIN fotos_imagem i ON i.codigo = a.codigo WHERE a.codigo IS NOT NULL AND i.imagem IS NOT NULL ORDER BY g.id DESC, a.id DESC, i.id DESC LIMIT 6";

$exec = $db->Executar($sql);
while ($row = $exec->fetch_object()) {
    $ultimas_fotos[] = [
        'src' => PASTA_CLIENTE . 'img_fotos_g/' . $row->album_codigo . '/' . $row->imagem,
        'album_titulo' => $row->album_titulo,
        'grupo_titulo' => $row->grupo_titulo
    ];
}
?>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
.foto-section-title{display:flex;align-items:center;gap:12px;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:28px;font-weight:700;color:#00adef;max-width:1080px;margin:40px auto 15px auto;padding:0 20px;user-select:none;text-shadow:1px 1px 6px rgba(0,173,239,0.8)}
.foto-section-title i.fa-camera{font-size:32px;color:#00adef;text-shadow:1px 1px 4px rgba(0,173,239,1)}
.foto-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;padding:0 20px 40px 20px;max-width:1080px;margin:0 auto 50px auto;user-select:none}
.foto-thumb{position:relative;cursor:pointer;border-radius:12px;overflow:hidden;box-shadow:0 8px 20px rgba(0,0,0,0.9),inset 0 -40px 60px -10px rgba(0,0,0,0.9);transition:transform 0.25s ease,box-shadow 0.3s ease;background:#222;aspect-ratio:1/1}
.foto-thumb:hover{transform:scale(1.06);box-shadow:0 12px 28px rgba(0,173,239,0.9),inset 0 -40px 80px -5px rgba(0,173,239,0.6);z-index:5}
.foto-thumb img{width:100%;height:100%;display:block;object-fit:cover;border-radius:12px;user-select:none;pointer-events:none}
.foto-thumb .expand-icon{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:38px;color:#00adef;opacity:0;filter:drop-shadow(0 0 6px #00adef);pointer-events:none;transition:opacity 0.3s ease}
.foto-thumb:hover .expand-icon{opacity:0.9}
.foto-popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);display:none;justify-content:center;align-items:center;z-index:12000}
.foto-popup-overlay.active{display:flex}
.foto-popup-content{position:relative;max-width:90%;max-height:90%;display:flex;align-items:center;justify-content:center}
.foto-popup-content img{max-width:100%;max-height:100%;border-radius:12px;box-shadow:0 0 30px #00adef;object-fit:contain}
.foto-popup-close{position:absolute;top:-45px;right:0;font-size:36px;color:#00adef;cursor:pointer;z-index:12100;font-weight:700;text-shadow:0 0 10px #00adef;background:transparent;border:none}
.foto-nav-arrow{position:absolute;top:50%;font-size:48px;color:#00adef;cursor:pointer;user-select:none;z-index:12100;transform:translateY(-50%);padding:12px;background:rgba(0,0,0,0.35);border-radius:50%;transition:background-color 0.3s ease}
.foto-nav-arrow:hover{background-color:#00adef;color:#000}
.foto-nav-arrow.left{left:-70px}
.foto-nav-arrow.right{right:-70px}
.foto-counter{position:absolute;top:-45px;left:50%;transform:translateX(-50%);color:#00adef;font-size:18px;font-weight:600;text-shadow:0 0 10px #00adef}
.botao_ver_mais{max-width:1080px;margin:0 auto 50px auto;padding:0 20px;text-align:center}
.botao_ver_mais a{display:inline-block;background:linear-gradient(135deg,#00adef,#0088cc);color:white;padding:12px 30px;border-radius:30px;font-weight:600;font-size:18px;box-shadow:0 4px 15px rgba(0,173,239,0.4);text-decoration:none;transition:all 0.3s ease}
.botao_ver_mais a:hover{background:linear-gradient(135deg,#0088cc,#00adef);box-shadow:0 6px 20px rgba(0,173,239,0.6);transform:translateY(-2px)}
@media(max-width:1000px){.foto-gallery{grid-template-columns:repeat(2,1fr)}.foto-nav-arrow{display:none}}
@media(max-width:600px){.foto-gallery{grid-template-columns:1fr}.foto-section-title{font-size:22px}.foto-section-title i.fa-camera{font-size:26px}}
.foto-popup-overlay {
    z-index: 9999999999 !important;
}
</style>

<div class="foto-section-title" aria-label="Galeria das últimas fotos">
  <i class="fa-solid fa-camera" aria-hidden="true"></i>
  <span>FOTOS RECENTES</span>
</div>

<div class="foto-gallery" aria-live="polite">
  <?php if (!empty($ultimas_fotos)): ?>
    <?php foreach ($ultimas_fotos as $index => $foto): ?>
      <div class="foto-thumb" 
           role="button" 
           tabindex="0" 
           aria-label="Abrir foto <?= $index + 1 ?>" 
           data-index="<?= $index ?>">
        <img src="<?= htmlspecialchars($foto['src'], ENT_QUOTES) ?>" 
             alt="Foto <?= $index +1 ?> da galeria <?= htmlspecialchars($foto['album_titulo']) ?>">
        <div class="expand-icon"><i class="fa-solid fa-expand"></i></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="color:#ccc; text-align:center;">Nenhuma foto disponível.</p>
  <?php endif; ?>
</div>

<div class="botao_ver_mais" aria-label="Ver galeria completa de fotos">
  <a href="#" id="linkGaleriaCompleta" aria-haspopup="true" aria-controls="galeria" title="Ver galeria completa de fotos">
    Ver todas as fotos
  </a>
</div>

<div class="foto-popup-overlay" id="fotoPopup" role="dialog" aria-modal="true" aria-labelledby="popupLabel" aria-describedby="fotoCounter">
  <div class="foto-popup-content">
    <button class="foto-popup-close" id="closeFotoPopup" aria-label="Fechar visualização">×</button>
    <span class="foto-nav-arrow left" id="prevFoto" role="button" tabindex="0" aria-label="Foto anterior">❮</span>
    <span class="foto-nav-arrow right" id="nextFoto" role="button" tabindex="0" aria-label="Próxima foto">❯</span>
    <img id="fotoPopupImage" src="" alt="" />
    <div class="foto-counter" id="fotoCounter">0 / 0</div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded",()=>{
  const fotos=<?=json_encode(array_column($ultimas_fotos,"src"))?>,
        popup=document.getElementById("fotoPopup"),
        popupImg=document.getElementById("fotoPopupImage"),
        btnClose=document.getElementById("closeFotoPopup"),
        btnPrev=document.getElementById("prevFoto"),
        btnNext=document.getElementById("nextFoto"),
        contador=document.getElementById("fotoCounter");
  let currentIndex=0;

  function abrir(index){
    if(index>=0&&index<fotos.length){
      currentIndex=index;
      const img=new Image();
      img.onload=()=>{
        popupImg.src=img.src;
        popupImg.alt=`Foto ${currentIndex+1} de ${fotos.length}`;
        contador.textContent=`${currentIndex+1} / ${fotos.length}`;
        popup.classList.add("active");
        document.body.style.overflow="hidden";
        btnClose.focus();
      };
      img.src=fotos[currentIndex];
    }
  }
  function fechar(){
    popup.classList.remove("active");
    popupImg.src="";
    currentIndex=0;
    document.body.style.overflow="auto";
  }
  function proximo(){
    if(fotos.length>1){
      currentIndex=(currentIndex+1)%fotos.length;
      abrir(currentIndex);
    }
  }
  function anterior(){
    if(fotos.length>1){
      currentIndex=(currentIndex-1+fotos.length)%fotos.length;
      abrir(currentIndex);
    }
  }

  document.querySelectorAll(".foto-thumb").forEach((el,idx)=>{
    el.addEventListener("click",()=>abrir(idx));
    el.addEventListener("keydown",e=>{
      if(e.key==="Enter"||e.key===" "){
        e.preventDefault();
        abrir(idx);
      }
    });
  });

  btnClose.addEventListener("click",fechar);
  btnNext.addEventListener("click",proximo);
  btnPrev.addEventListener("click",anterior);
  popup.addEventListener("click",e=>{
    if(e.target===popup) fechar();
  });

  document.addEventListener("keydown",e=>{
    if(!popup.classList.contains("active"))return;
    if(e.key==="Escape") fechar();
    if(e.key==="ArrowRight") proximo();
    if(e.key==="ArrowLeft") anterior();
  });

  // Swipe para mobile
  let touchStartX=0;
  popup.addEventListener("touchstart",e=>{
    if(e.touches.length===1 && popup.classList.contains("active"))
      touchStartX=e.touches[0].clientX;
  });
  popup.addEventListener("touchend",e=>{
    if(!popup.classList.contains("active"))return;
    let touchEndX=e.changedTouches[0].clientX, diff=touchEndX-touchStartX;
    if(Math.abs(diff)>50)
      diff>0 ? anterior() : proximo();
  });

  // Define href da galeria completa
  document.getElementById("linkGaleriaCompleta").href="https://cvmnews.com.br/galeria";
});
</script>
