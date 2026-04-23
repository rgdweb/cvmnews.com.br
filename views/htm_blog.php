<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head> 
      <meta property="og:image" content="https://cvmnews.com.br/sistema/arquivos/imagens/Image-4-1-2-1-png[30-07-25][01-56-05].png">
 <!-- /Tudo em um único pacote -->
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

  <title>Notícias - <?=$_base['titulo_pagina']?></title>
  
  <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet" type="text/css" media="all" />
  <link href="<?=LAYOUT?>css/blog.css" rel="stylesheet">
  <link href="<?=LAYOUT?>css/images.css" rel="stylesheet">

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i,800|Roboto:400,400i,500,500i,700,700i,900" rel="stylesheet">

    <?php // css alteravel pelo painel
    require_once('_css_padrao.php');
    require_once('_css_personalizado.php');
    ?>
    <style>
.blog_lista_meta {
  padding-left: 0;
  list-style: none;
}

.blog_lista_meta li {
  margin-bottom: 10px;
}

.blog_lista_meta li a {
  display: block;
  padding: 10px 15px;
  font-size: 16px;
  font-weight: 600;
  text-decoration: none;
  color: #333;
  background-color: #f9f9f9;
  border-radius: 6px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.08);
}

.blog_lista_meta li a:hover {
  background-color: #ffd700;
  color: #000;
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
  transform: translateY(-2px);
}

.blog_lista_meta li a.active {
  background-color: #00bfff;
  color: #fff;
  box-shadow: 0 4px 10px rgba(0, 191, 255, 0.3);
}
</style>
  </head>
  <body>

    <?php require_once('htm_modal.php'); ?>  

    <?php require_once('htm_topo.php'); ?>  

    <div class="wrapper">

      <div class="container">

        <div class="content_block row no-sidebar">
          <div class="fl-container">
            <div class="posts-block">
              <div class="contentarea"> 

                <?php //include_once('htm_banners.php'); ?>

                <div id="corpo" style="padding-top: 70px;">

                  <div class="col-sm-12">
                    <div class="heading-row">
                      <h1 class="titulo_padrao" >BLOG/NOTÍCIAS <i></i></h1>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-sm-8" >

                      <div style="margin-top:20px;">
                        <?php
                        
                        if($numitems == 0){
                          
                          echo "<div class='blog_nenhumresultado' >Nenhum resultado encontrado!</div>";
                          
                        } else {
                          
                          foreach ($noticias as $key => $value) {
                            
                            $endereco_not = $value['endereco'];
                            
                            echo "
                            <h2 class='blog_lista_titulo ' >
                            <a href='$endereco_not' class='blog_lista_titulo' >".$value['titulo']."</a>
                            </h2>
                            ";
                            
                            if($value['imagem']){

                              echo " 
                              <a href='$endereco_not' >
                              <div class='blog_imagem_principal' style='background-image:url(".$value['imagem'].");' ></div>                              
                              </a>
                              ";

                            }             

                            echo "
                            <div class='blog_lista_previa'>
                            ".$value['previa']."
                            </div>
                            ";

                            echo "
                            <div>
                            <input type='button' value='LEIA MAIS' class='botao_padrao' onClick=\"window.location='$endereco_not'\" style='border-radius:2px;'>
                            </div>
                            <div class='blog_lista_divisao'><hr></div>
                            ";

                          }
                        }

                        ?>
                      </div>

                      <div class="paginacao" >
                        <?=$paginacao?>
                      </div>

                    </div>

                    <div class="col-sm-4" >
                      <div class="blog_divisao_lateral" >
                        <?php include_once('htm_blog.lateral.php'); ?>
                      </div>
                    </div>

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