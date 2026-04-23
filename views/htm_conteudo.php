<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- SEO e Open Graph -->
    <meta property="og:title" content="CVMNEWS - PORTAL DE NOTÍCIAS e WEB RÁDIO/TV">
    <meta property="og:description" content="TV-NOTICIAS-POLITICA-SEGURANÇA-ENTRETENIMENTO ">
    <meta property="og:url" content="<?= 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
    <meta property="og:type" content="website">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478'];?>">
    <title><?=$_base['titulo_pagina']?></title>
    <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto:400,500,700,900" rel="stylesheet">
    <?php require_once('_css_padrao.php'); ?>
    <?php require_once('_css_personalizado.php'); ?>
    <style>.style2{color:#009966}.style3{color:#FFCC00;font-weight:bold}.share-container{display:flex;gap:10px;align-items:center;margin:20px 0}.share-btn{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:8px;text-decoration:none;color:white;font-size:16px;transition:all .3s ease;box-shadow:0 2px 4px rgba(0,0,0,0.1)}.share-btn:hover{transform:translateY(-2px);box-shadow:0 4px 8px rgba(0,0,0,0.2)}.facebook{background:#1877f2}.whatsapp{background:#25d366}.instagram{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}.x-twitter{background:#000000}.share-label{margin-right:10px;font-weight:bold;color:white}.share-compact{gap:5px}.share-compact .share-btn{width:32px;height:32px;font-size:14px}#topBtn{display:none;position:fixed;bottom:250px;right:20px;z-index:1000;border:none;outline:0;background:#007bff;color:#fff;cursor:pointer;padding:15px;border-radius:50%;font-size:18px;width:50px;height:50px;transition:all .3s ease;box-shadow:0 4px 12px rgba(0,123,255,.3)}#topBtn:hover{background:#0056b3;transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,123,255,.4)}#topBtn.show{display:block;animation:fadeIn .3s ease}@keyframes fadeIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}.highlight-blink{animation:blink 2s ease-in-out 3 alternate}@keyframes blink{0%{background-color:transparent}50%{background-color:#ffff00;box-shadow:0 0 20px rgba(255,255,0,0.8)}100%{background-color:transparent}}#progress-bar{position:fixed;top:0;left:0;width:0%;height:4px;background:linear-gradient(90deg,#007bff,#00d4ff);z-index:9999;transition:width 0.1s ease;box-shadow:0 2px 4px rgba(0,123,255,0.2);pointer-events:none}body{margin:0}.fade-in{opacity:0;transform:translateY(20px);transition:opacity 0.6s ease,transform 0.6s ease}.fade-in.visible{opacity:1;transform:translateY(0)}</style>

    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478']?>">

    <!-- Título da Página -->
    <title><?=$pagina['titulo']?> - <?=$_base['titulo_pagina']?></title>

  <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet" type="text/css" media="all" />

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i,800|Roboto:400,400i,500,500i,700,700i,900" rel="stylesheet">

  <?php // css alteravel pelo painel
  require_once('_css_padrao.php');
  require_once('_css_personalizado.php');
  ?>

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

              <?php //include_once('htm_banners.php'); ?>

              <div id="conteudo" style="padding-top: 80px;">

                <div class="row">
                   <h1><?= $pagina['titulo'] ?? 'Título não disponível' ?></h1>

                    <div style="margin-top: 30px; padding-bottom: 80px;">
                      <?php
                        // CÓDIGO CORRIGIDO
                        // Apenas exibe o conteúdo principal da página.
                        echo $pagina['conteudo'] ?? 'Conteúdo não encontrado.';
                      ?>
                    </div>

                  </div>
                  <div class="col-sm-2" ></div>

                </div> 

              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php include_once('htm_rodape2.php'); ?>

  <div class="fixed-menu"></div>
  <script type="text/javascript" src="<?=LAYOUT?>js/jquery.min.js"></script>	
  <script type="text/javascript" src="<?=LAYOUT?>js/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?=LAYOUT?>js/modules.js"></script>	
  <script type="text/javascript" src="<?=LAYOUT?>js/theme.js"></script>
  <script type="text/javascript" src="<?=LAYOUT?>js/jquery.themepunch.plugins.min.js"></script>
  <script type="text/javascript" src="<?=LAYOUT?>js/jquery.themepunch.revolution.min.js"></script>
  <!-- Portfolio -->
  <script type="text/javascript" src="<?=LAYOUT?>js/jquery.isotope.min.js"></script>
  <script type="text/javascript" src="<?=LAYOUT?>js/sorting.js"></script>    
  <!-- Testimonials -->
  <script type="text/javascript" src="<?=LAYOUT?>js/slick.js"></script>
  <script type="text/javascript" src="<?=LAYOUT?>js/funcoes.js"></script>
  <script type="text/javascript">function dominio(){ return "<?=DOMINIO?>"; }</script>
  <script type="text/javascript">

    jQuery(document).ready(function() {
     "use strict";                    
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

      // Testimonials
      jQuery('.testimonials-info').slick({
        fade: true,
        arrows: false,
        asNavFor: '.testimonials-nav',
        adaptiveHeight: true
      });
      
      var visibl_show = 5; // Value = 1..5 (Max value - 5)
      
      var count_els = jQuery('.testimonials-nav .nav_item_wrap').length;      
      if (count_els > visibl_show && visibl_show % 2 == 1) {
        var center_true = true; 
      } else if (count_els <= visibl_show) {
        var visibl_show = count_els;
        var center_true = false;
      }
      else {
        var center_true = false;        
      }
      jQuery('.testimonials-nav').slick({       
        slidesToShow: visibl_show,
        asNavFor: '.testimonials-info',
        centerMode: center_true,
        focusOnSelect: true,
        autoplay: true,
        responsive: [
        {
          breakpoint: 980,
          settings: {
            slidesToShow: 3
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
        ]
      });   

    });

  </script>

  <script type="text/javascript">
    $('a.scrollSuave').on('click', function(event) {

      var target = $( $(this).attr('href') );

      if( target.length ) {
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 500);
      }

    }); 
  </script>
  
</body>
</html>