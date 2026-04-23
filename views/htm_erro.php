<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <link rel="shortcut icon" href="<?=$_base['imagem']['146955550242195'];?>">   
  <title>Erro - <?=$_base['titulo_pagina']?></title>

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

                <?php include_once('htm_banners.php'); ?>                

                <div id="quemsomos" style="padding-top: 80px;">
                  <div class="row">

                    <div class="col-sm-12" >
 
                      <div style="font-size:22px; text-align: center; color:#666; font-weight: 500; padding-top:100px; padding-bottom:200px;">Página não encontrada.</div>

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

      <script type="text/javascript" src="<?=LAYOUT?>js/jquery.min.js"></script>	
      <script type="text/javascript" src="<?=LAYOUT?>js/jquery-ui.min.js"></script>    
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