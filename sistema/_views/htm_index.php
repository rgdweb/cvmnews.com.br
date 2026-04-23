<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title><?=$_base['titulo_pagina']?></title>
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=LAYOUT?>assets/ico/apple-touch-icon-144-precomposed.png">
        <link rel="shortcut icon" href="<?=$_base['imagem']['146955550242195'];?>">
        
        <meta name="description" content="<?=$_base['descricao']?>" />
        <meta property="og:description" content="<?=$_base['descricao']?>">
        <meta name="author" content="publiquesites.com.br">
        <meta name="classification" content="Website" />
        <meta name="robots" content="index, follow">
        <meta name="Indentifier-URL" content="<?=DOMINIO?>" />
        
        <!-- CSS Global -->
        <link href="<?=LAYOUT?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        
        <?php require_once('_css_padrao.php'); ?>

        <link href="<?=LAYOUT?>assets/css/media.css" rel="stylesheet" type="text/css">        
        <link href="<?=LAYOUT?>assets/plugins/owl-carousel/owl.carousel.css" rel="stylesheet" type="text/css">        
        <link href="<?=LAYOUT?>assets/plugins/owl-carousel/owl.theme.css" rel="stylesheet" type="text/css"> 
        <link href="<?=LAYOUT?>assets/plugins/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet" type="text/css">       
        <link href="<?=LAYOUT?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?=LAYOUT?>assets/css/multicolors/theme-color.css" rel="stylesheet" id="css-switcher-link">
        
        <!--[if lt IE 9]>
        <script src="assets/plugins/iesupport/html5shiv.js"></script>
        <script src="assets/plugins/iesupport/respond.js"></script>
        <![endif]-->

        <link href="<?=LAYOUT?>api/hover-master/css/hover-min.css" rel="stylesheet">

        <?php require_once('_css_personalizado.php'); ?>

    </head>

    <body id="home" class="wide">

        <?php include_once('htm_modal.php'); ?>

        <!-- PRELOADER -->
        <div id="loading">          
            <div id="loading-center-absolute">
                <div class="object" id="object_one"></div>
                <div class="object" id="object_two"></div>
                <div class="object" id="object_three"></div>
            </div>        
        </div>
        <!-- /PRELOADER -->

        <!-- WRAPPER -->
        <main class="wrapper">
             
            <?php include_once('htm_topo.php'); ?>
            
            <!-- CONTENT AREA -->
            <?php include_once('htm_banners.php'); ?>
            


            <!-- Theme Features Start -->
            <section id="produtos" class="space-80 white-bg animate-effect">
                <div class="container theme-container">
                    <div class="title-wrap space-bottom-45">
                        <h2 class="section-title inicialtitulo"><?=$produtos_texto_titulo?></h2>
                        <p><?=$produtos_texto_desc?></p>
                    </div>
                    <div class="row">

                        <div class="col-sm-6 col-xs-12 text-center feature-list">
                            <div class="feature-wrap space-bottom-30">
                                <div class="inicial_logo_produtos" >
                                    <a href="<?=DOMINIO?>pignus" >
                                        <img src="<?=$_base['imagem']['152218952731150']?>" >
                                    </a>
                                </div>
                                <h2 class="title-2" style="margin-top: 20px;" ><?=$produto1_titulo?></h2>
                                <p><?=$produto1_desc?></p>
                                <div style="margin-top: 20px;">
                                    <a href="<?=DOMINIO?>pignus" class="theme-btn-1 btn botao_produtos">LEIA MAIS</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-xs-12 text-center feature-list">
                            <div class="feature-wrap space-bottom-30">
                                <div class="inicial_logo_produtos" >
                                    <a href="<?=DOMINIO?>exitum" >
                                        <img src="<?=$_base['imagem']['152218977620269']?>" >
                                    </a>
                                </div>
                                <h2 class="title-2" style="margin-top: 20px;" ><?=$produto2_titulo?></h2>
                                <p><?=$produto2_desc?></p>
                                <div style="margin-top: 20px;">
                                    <a href="<?=DOMINIO?>exitum" class="theme-btn-1 btn botao_produtos">LEIA MAIS</a>
                                </div>
                            </div>
                        </div>

                    </div>                     
                </div>
            </section>
            <!-- / Theme Features Ends -->



            <!-- Created for Us Start -->
            <section id="created-for-us" class="white-bg animate-effect " >
                <div class="container theme-container">
                    <?php

                        foreach ($noticias as $key => $value) {
                            
                            echo "
                            <div class='row' >
                                <div class='col-xs-12 col-sm-12 col-md-12' >

                                    <div class='noticia_inicial_imagem' style='background-image:url(".$value['imagem'].");' onClick=\"window.location='".$value['endereco']."';\" ></div>
                                    <div class='noticia_inicial_titulo' onClick=\"window.location='".$value['endereco']."';\" >".$value['titulo']."
                                    </div>

                                </div>
                                <div style='clear:both;'></div>
                            </div>
                            ";

                        }

                    ?>
                </div>                    
            </section>
            <!-- / Created for Us Ends -->

            

            <!-- Stratup Ready For Devices Start -->
            <section id="startup-ready" class="space-70 light-bg animate-effect " style="background-image:url(<?=$_base['imagem']['152219483856264']?>); background-repeat:no-repeat; background-size: cover;" >          
                <div class="container theme-container">
                    <div class="row">

                        <div class="col-xs-12 col-sm-8 col-md-9" >

                            <div class="depoimentos_inicial ">

                                <div class="depoimentos_inicial_titulo">Depoimento</div>
                                
                                <div class="depoimentos_inicial_texto" >
                                    <?=$depoimentos[0]['conteudo']?>
                                </div>
                                <div class="depoimentos_inicial_nome" >
                                    <?=$depoimentos[0]['nome']?>
                                </div>

                                <?php if($depoimentos[0]['imagem']){ ?>

                                <div class="depoimentos_inicial_imagem" ><img src="<?=$depoimentos[0]['imagem']?>"></div>
                                
                                <?php } ?>
                                
                            </div>

                        </div>

                        <div class="col-xs-12 col-sm-4 col-md-3" >

                            <a href="<?=DOMINIO?>blog" class="blog_inicial" >
                                <img src="<?=$_base['imagem']['152219437515559']?>">
                            </a>

                        </div>
                        
                    </div>
                </div>               
            </section>
            
            <section id="downloads" class="space-50 light-bg">                
                <div class="brand-slider  theme-slider">
                  <p>
                    <?php

                    foreach ($downloads as $key => $value) {
                        
                        echo "
                        <div class='item'><a href='".$value['endereco']."'> <img src='".$value['imagem']."' alt='' ></a></div>
                        ";

                    }

                ?>
                  </p>
              </div>
          </section>
            
            <?php include_once('htm_rodape.php'); ?>             
            
        </main>
        <!-- /WRAPPER --> 
        

        <!-- JS Global -->
        <script src="<?=LAYOUT?>assets/plugins/jquery/jquery-1.11.3.min.js"></script>   
        <script src="<?=LAYOUT?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?=LAYOUT?>assets/js/jquery.easing.js"></script>
        <script src="<?=LAYOUT?>assets/plugins/owl-carousel/owl.carousel.min.js"></script>
        <script src="<?=LAYOUT?>assets/plugins/isotope-master/dist/isotope.pkgd.min.js"></script>

        <!-- JS Page Level --> 
        <script src="<?=LAYOUT?>assets/js/theme.js"></script>
        <script src="<?=LAYOUT?>assets/js/theme-ajax-mail.js"></script>
        <script src="<?=LAYOUT?>js/funcoes.js"></script>
        <script src="<?=LAYOUT?>js/site.js"></script>
        <script>function dominio(){ return '<?=DOMINIO?>'; }</script>

    </body>
</html>

<script>
$(document).ready(function () {

    $("#banner_principal").owlCarousel({
        pagination: false,
        navigation: true,
        autoPlay: true,
        singleItem: true,
        navigationText: [
            "<i class='fa fa-long-arrow-left'></i>",
            "<i class='fa fa-long-arrow-right'></i>"
        ]
    });
});
</script>