<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="shortcut icon" href="<?=$_base['imagem']['146955550242195']?>">
  <title>Contato - <?=$_base['titulo_pagina']?></title>

  <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800|Roboto:400,500,700,900" rel="stylesheet">

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
<?php include_once('views/htm_galeria_fotos_exibir.php'); ?>
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
      jQuery('.testimonials-info').slick({ fade: true, arrows: false, asNavFor: '.testimonials-nav', adaptiveHeight: true });
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
