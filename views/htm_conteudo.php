<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- SEO e Open Graph -->
    <meta property="og:title" content="CVMNEWS - PORTAL DE NOTÍCIAS e WEB RÁDIO/TV">
    <meta property="og:description" content="TV-NOTICIAS-POLITICA-SEGURANÇA-ENTRETENIMENTO">
    <meta property="og:url" content="<?= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
    <meta property="og:type" content="website">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478'];?>">
    <title><?=$pagina['titulo']?> - <?=$_base['titulo_pagina']?></title>

    <!-- CSS ESSENCIAIS (sem duplicação) -->
    <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto:400,500,700,900" rel="stylesheet">
    <?php require_once('_css_padrao.php'); ?>
    <?php require_once('_css_personalizado.php'); ?>

    <style>
    .style2 {color: #009966}
    .style3 {color: #FFCC00; font-weight: bold;}

    /* Webmail flutuante nas páginas internas - ícone que expande ao hover */
    .link-webmail-flutuante {
        position: fixed;
        top: 50%;
        right: 0;
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        font-weight: bold;
        border-radius: 8px 0 0 8px;
        text-decoration: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        z-index: 10000;
        transition: all 0.3s ease;
        font-size: 0;
        overflow: hidden;
        white-space: nowrap;
    }
    .link-webmail-flutuante:before {
        content: '\f0e0';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 18px;
        margin-right: 0;
        display: inline-block;
        transition: margin 0.3s ease;
    }
    .link-webmail-flutuante:hover {
        background-color: #0056b3;
        font-size: 14px;
        padding-right: 15px;
    }
    .link-webmail-flutuante:hover:before {
        margin-right: 8px;
    }

    /* Voltar ao topo */
    #topBtn{display:none;position:fixed;bottom:250px;right:20px;z-index:1000;border:none;outline:0;background:#007bff;color:#fff;cursor:pointer;padding:15px;border-radius:50%;font-size:18px;width:50px;height:50px;transition:all .3s ease;box-shadow:0 4px 12px rgba(0,123,255,.3)}
    #topBtn:hover{background:#0056b3;transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,123,255,.4)}
    #topBtn.show{display:block;animation:fadeIn .3s ease}
    @keyframes fadeIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}

    /* Barra de progresso */
    #progress-bar{position:fixed;top:0;left:0;width:0%;height:3px;background:#007bff;z-index:1001;transition:none;box-shadow:0 2px 4px rgba(0,123,255,.2)}

    /* Conteúdo responsivo */
    .conteudo-pagina {
        padding: 20px 15px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    .conteudo-pagina img {
        max-width: 100%;
        height: auto;
    }
    .conteudo-pagina h1 {
        font-size: 24px;
        word-break: break-word;
    }
    @media (max-width: 767px) {
        .conteudo-pagina h1 {
            font-size: 20px;
        }
        .conteudo-pagina {
            padding: 15px 10px;
        }
    }

    /* DESATIVAR popup de video e botao ATIVAR SOM */
    .video-modal,
    .video-popup,
    #videoPopup,
    #videoModal,
    .sound-button,
    #soundButton,
    #soundToggleBtn {
        display: none !important;
    }
    </style>
</head>
<body>

  <?php require_once('htm_modal.php'); ?>
  <?php require_once('htm_topo2.php'); ?>

  <!-- Webmail flutuante -->
  <a href="https://titan.hostgator.com.br/mail/" target="_blank" class="link-webmail-flutuante">
      Webmail
  </a>

  <!-- BARRA DE PROGRESSO -->
  <div id="progress-bar"></div>

  <div class="wrapper">
    <div class="container">
      <div class="content_block row no-sidebar">
        <div class="fl-container">
          <div class="posts-block">
            <div class="contentarea">
              <div id="conteudo" class="conteudo-pagina">
                <h1><?= $pagina['titulo'] ?? 'Título não disponível' ?></h1>
                <div style="margin-top: 30px; padding-bottom: 80px;">
                  <?php
                    echo $pagina['conteudo'] ?? 'Conteúdo não encontrado.';
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('htm_rodape2.php'); ?>

  <button onclick="topFunction()" id="topBtn" title="Voltar ao topo">↑</button>

  <div class="fixed-menu"></div>
  <!-- SCRIPTS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="<?=LAYOUT?>js/bootstrap.min.js"></script>
  <script src="<?=LAYOUT?>js/modules.js"></script>
  <script src="<?=LAYOUT?>js/theme.js"></script>
  <script src="<?=LAYOUT?>js/jquery.themepunch.plugins.min.js"></script>
  <script src="<?=LAYOUT?>js/jquery.themepunch.revolution.min.js"></script>
  <script src="<?=LAYOUT?>js/funcoes.js"></script>
  <script type="text/javascript">function dominio(){ return "<?=DOMINIO?>"; }</script>
  <script>
    // Voltar ao topo
    let topBtn=document.getElementById("topBtn");
    window.onscroll=function(){
      if(document.body.scrollTop>1000||document.documentElement.scrollTop>1000){
        topBtn.classList.add("show");
      }else{
        topBtn.classList.remove("show");
      }
    };
    function topFunction(){
      document.body.scrollTop=0;
      document.documentElement.scrollTop=0;
    }

    // Scroll suave
    $(document).ready(function(){
      $('a.scrollSuave').on('click', function(event) {
        var target = $( $(this).attr('href') );
        if( target.length ) {
          event.preventDefault();
          $('html, body').animate({ scrollTop: target.offset().top }, 500);
        }
      });
    });

    // Barra de progresso
    (function(){
      var bar=document.getElementById("progress-bar");
      if(bar){
        window.addEventListener("scroll",function(){
          var h=document.documentElement.scrollHeight-window.innerHeight;
          var s=window.scrollY;
          bar.style.width=Math.min(s/h*100,100)+"%";
        });
      }
    })();

    // DESATIVAR popup de video e botao ATIVAR SOM
    // Esconder popups de video e ativar som nos videos da pagina
    (function(){
      // Remover botao ATIVAR SOM
      var soundBtns = document.querySelectorAll('#soundButton, #soundToggleBtn, .sound-button, .sound-button *');
      soundBtns.forEach(function(btn){ btn.remove(); });

      // Fechar e remover popup de video se estiver aberto
      var videoModals = document.querySelectorAll('.video-modal, .video-popup, #videoPopup, #videoModal');
      videoModals.forEach(function(modal){
        modal.classList.remove('active');
        modal.style.display = 'none';
        // Parar qualquer iframe dentro do popup
        var iframes = modal.querySelectorAll('iframe');
        iframes.forEach(function(iframe){ iframe.src = ''; });
      });

      // Ativar som nos videos da pagina principal (remover mute)
      var pageIframes = document.querySelectorAll('.conteudo-pagina iframe');
      pageIframes.forEach(function(iframe){
        var src = iframe.getAttribute('src') || '';
        if(src.indexOf('youtube.com') !== -1 || src.indexOf('youtu.be') !== -1){
          // Remover mute=1 e adicionar mute=0, autoplay=1
          src = src.replace(/mute=1/gi, 'mute=0');
          src = src.replace(/muted=1/gi, 'muted=0');
          if(src.indexOf('mute=') === -1){
            src += (src.indexOf('?') !== -1 ? '&' : '?') + 'mute=0';
          }
          iframe.setAttribute('src', src);
          iframe.setAttribute('allow', 'autoplay; encrypted-media');
        }
      });

      // Observer para remover popups que aparecem dinamicamente
      var observer = new MutationObserver(function(mutations){
        mutations.forEach(function(mutation){
          mutation.addedNodes.forEach(function(node){
            if(node.nodeType === 1){
              // Remover botoes de som
              if(node.id === 'soundButton' || node.id === 'soundToggleBtn' || 
                 node.classList && (node.classList.contains('sound-button') || node.classList.contains('soundToggleBtn'))){
                node.remove();
              }
              // Fechar popups de video
              if(node.id === 'videoPopup' || node.id === 'videoModal' ||
                 node.classList && (node.classList.contains('video-modal') || node.classList.contains('video-popup'))){
                node.classList.remove('active');
                node.style.display = 'none';
              }
            }
          });
        });
      });
      observer.observe(document.body, { childList: true, subtree: true });
    })();
  </script>
</body>
</html>
