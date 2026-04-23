<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<?php
// Cache moderado para conteúdo dinâmico
header("Cache-Control: max-age=3600, public");
header("Expires: ".gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
?>
<!DOCTYPE html>
<html>
<head>
    <meta property="og:title" content="CVMNEWS - PORTAL DE NOTÍCIAS e WEB RÁDIO/TV">
    <meta property="og:description" content="TV-NOTICIAS-POLITICA-SEGURANÇA-ENTRETENIMENTO">
    <meta property="og:url" content="<?= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>">
    <meta property="og:type" content="website">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Metadados SEO adicionais -->
    <meta name="description" content="Portal de notícias e web rádio/TV com cobertura em política, segurança e entretenimento.">
    <meta name="keywords" content="notícias, política, segurança, entretenimento, rádio, tv">
    <meta name="author" content="CVMNEWS">
    <link rel="shortcut icon" href="<?=$_base['imagem']['147129831543478'];?>">
    <title><?=$_base['titulo_pagina']?></title>
    <!-- CSS ESSENCIAIS -->
    <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/revslider.css" rel="stylesheet">
    <link href="<?=LAYOUT?>css/custom.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto:400,500,700,900" rel="stylesheet">
    <?php require_once('_css_padrao.php'); ?>
    <?php require_once('_css_personalizado.php'); ?>
    <!-- ESTILOS ADICIONADOS DO CÓDIGO ANTIGO -->
    <style type="text/css">
        .style2 {color: #009966}
        .style3 { color: #FFCC00; font-weight: bold; }
    </style>
    <style>
        .share-container{display:flex;gap:10px;align-items:center;margin:20px 0}
        .share-btn{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:8px;text-decoration:none;color:white;font-size:16px;transition:all 0.3s ease;box-shadow:0 2px 4px rgba(0,0,0,0.1)}
        .share-btn:hover{transform:translateY(-2px);box-shadow:0 4px 8px rgba(0,0,0,0.2)}
        .facebook{background:#1877f2}
        .whatsapp{background:#25d366}
        .instagram{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}
        .x-twitter{background:#000000}
        .share-label{margin-right:10px;font-weight:bold; color: white;}
        .share-compact{gap:5px}
        .share-compact .share-btn{width:32px;height:32px;font-size:14px}
        .link-webmail-flutuante {
    position: fixed;
    top: 50%; /* posição vertical */
    right: 0; /* gruda na lateral direita */
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    font-weight: bold;
    border-radius: 8px 0 0 8px;
    text-decoration: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 10000;
    transition: all 0.3s ease;
}

.link-webmail-flutuante:hover {
    background-color: #0056b3;
    transform: translateX(-3px);
}
    </style>
    <style>
#topBtn{display:none;position:fixed;bottom:250px;right:20px;z-index:1000;border:none;outline:0;background:#007bff;color:#fff;cursor:pointer;padding:15px;border-radius:50%;font-size:18px;width:50px;height:50px;transition:all .3s ease;box-shadow:0 4px 12px rgba(0,123,255,.3)}#topBtn:hover{background:#0056b3;transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,123,255,.4)}#topBtn.show{display:block;animation:fadeIn .3s ease}@keyframes fadeIn{from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)}}
</style>
<!-- BARRA DE PROGRESSO NO TOPO -->
<style>#progress-bar{position:fixed;top:0;left:0;width:0%;height:3px;background:#007bff;z-index:1001;transition:none;box-shadow:0 2px 4px rgba(0,123,255,.2)}</style>
</head>
<body>
    <?php require_once('htm_modal.php'); ?>
    <?php require_once('htm_topo.php'); ?>
    
    <!-- BARRA DE PROGRESSO -->
    <div id="progress-bar"></div>
    <div class="wrapper">
        <div class="container">
            <div class="content_block row no-sidebar">
                <div class="fl-container">
                    <div class="posts-block">
                        <div class="contentarea">
                            <?php include_once('htm_banners.php'); ?>
                            <div id="equipe2" style="position: relative; padding-top:5px; padding-bottom: 5px; background-color:#FFFFFF">
                              <div align="center"><span style="width: 100%; text-align: center;">
                                <!-- Otimização de imagem com lazy loading -->
                                <img src="<?=$apresentacao['imagem']?>" style="max-width: 90%;" loading="lazy" alt="Banner principal">
                              </span></div>
                            </div>
                            <div id="blog" style="position: relative; padding-top:80px;">
                              <div class="container">
                                <div class="row">
                                  <div class="col-sm-7">
                                    <div class="titulo_padrao" style="text-align: left;" ><i class="fa fa-newspaper" aria-hidden="true"></i> <?=$comotrabalhamos['titulo']?></div>
                                    <div style="margin-top:30px;"><?=$comotrabalhamos['conteudo']?></div>
                                    <div class="row">
                                      <?php foreach ($noticias as $key => $value) { 
                                        echo "<div class='col-sm-6'>
                                        <div class='noticias_div'>
                                          <a href='".$value['endereco']."' target='_blank' class='noticias_imagem' style='background-image:url(".$value['imagem'].");'></a>
                                          <a class='noticias_titulo' href='".$value['endereco']."' >".$value['titulo']."</a>
                                          <div class='noticias_dia' >".$value['data']."</div>
                                          <div class='noticias_previa'>".$value['previa']."</div>
                                        </div>
                                      </div>"; 
                                      } ?>
                                    </div>
                                  </div>
                                  <div id="servicos" class="col-sm-5">
                                    <div class="titulo_padrao" style="text-align: left;" ><i class="fa fa-calendar" aria-hidden="true"></i> <?=$sanfona5['titulo']?></div>
                                    <div style="margin-top: 30px;"><?=$sanfona5['conteudo']?></div>
                                    <div style="margin-top:30px;">
                                      <div class="col-sm-12 module_cont module_accordion">
                                        <div class="shortcode_accordion_shortcode accordion">
                                          <h5 data-count="1" class="shortcode_accordion_item_title card_item_topo expanded_yes">
                                            <span class="card_sub_titu" ><?=$sanfona1['titulo']?></span>
                                            <span class="ico"></span>
                                          </h5>
                                          <div class="shortcode_accordion_item_body">
                                            <div class="ip"><?=$sanfona1['conteudo']?></div>
                                          </div>
                                          <h5 data-count="2" class="shortcode_accordion_item_title card_item_topo expanded_no">
                                            <span class="card_sub_titu" ><?=$sanfona2['titulo']?></span>
                                            <span class="ico"></span>
                                          </h5>
                                          <div class="shortcode_accordion_item_body">
                                            <div class="ip"><?=$sanfona2['conteudo']?></div>
                                          </div>
                                          <h5 data-count="3" class="shortcode_accordion_item_title card_item_topo expanded_no">
                                            <span class="card_sub_titu" ><?=$sanfona3['titulo']?></span>
                                            <span class="ico"></span>
                                          </h5>
                                          <div class="shortcode_accordion_item_body">
                                            <div class="ip"><?=$sanfona3['conteudo']?></div>
                                          </div>
                                          <h5 data-count="3" class="shortcode_accordion_item_title card_item_topo expanded_no">
                                            <span class="card_sub_titu" >
                                              <span class="style2"><i class="fa fa-microphone" aria-hidden="true"></i> ESPECIAL -</span>
                                              <?=$sanfona4['titulo']?>
                                            </span>
                                            <span class="ico"></span>
                                          </h5>
                                          <div class="shortcode_accordion_item_body">
                                            <div class="ip"><?=$sanfona4['conteudo']?></div>
                                          </div>
                                          <div class="apres_subtitulos" >
                                            <span class="apres_ico">
                                              <!-- Otimização de imagem com lazy loading -->
                                              <img src="<?=$apres_subtxt1['imagem']?>" loading="lazy" alt="Ícone informativo">
                                            </span>
                                            <br>
                                            <span class="apres_subtextos"><?=$apres_subtxt1['conteudo']?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div id="sobre" style="padding-top: 80px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- GALERIA DE VÍDEOS -->
         <div id="galeria" class="galeria_inicial" >
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <div class="titulo_padrao" style="color:#fff;"><i class="fa fa-camera" aria-hidden="true"></i> <?=$galeria_texto['titulo']?></div>
            <div style="margin-top:30px; color:#fff; width: 100%; padding-bottom: 30px;"><?=$galeria_texto['conteudo']?></div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <div class="bg_start wall_wrap" >
              <div class="list-of-images items3 photo_gallery">
             <?php include_once('htm_galeria_videos_exibir.php'); ?>
              <!-- GALERIA DE FOTOS -->
             <?php include_once('htm_galeria_fotos_exibir.php'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
        <!-- BLOCO DE PESQUISA (ENQUETE) -->
        <div id="equipe3" style="position: relative; padding-top:80px; padding-bottom: 80px; background-color:#FFCC00">
            <div class="container">
              <div class='row' ><div class='col-xs-12 col-sm-12 col-md-12' ><div><div class="titulo_padrao" ><i class="fa fa-question-circle" aria-hidden="true"></i> NOSSA PESQUISA</div></div></div></div>
              <div class='row' >
                <div class='col-xs-12 col-sm-6 col-md-6' >
                  <div style="margin-top:50px; width: 100%;">
                    <p><?=$enquete_texto['conteudo']?>
                      <br>
                      <span class="apres_subtextos">
                        <span class="apres_ico">
                          <!-- Otimização de imagem com lazy loading -->
                          <img src="<?=$apres_subtxt3['imagem']?>" loading="lazy" alt="Ícone de pesquisa">
                        </span>
                        <br>
                        <?=$apres_subtxt3['conteudo']?>
                      </span>
                    </p>
                  </div>
                </div>
                <div class='col-xs-12 col-sm-6 col-md-6' >
                  <div style="padding:25px; width: 100%; background-color:#FFFFFF; margin-top: 50px;">
                    <form action="<?=DOMINIO?>enquete/votar" method="post" id="enqueteform" name="enqueteform" >
                      <div style="font-size: 18px; color:#000; padding-bottom: 20px;"><?=$enquete['pergunta']?><strong>😉</strong></div>
                      <?php foreach ($enquete_respostas as $key => $value) { 
                        echo "<div style='padding-top:5px;' > 
                          <input type='radio' name='enquete' id='resposta_".$value['codigo']."' value='".$value['codigo']."' > 
                          <label for='resposta_".$value['codigo']."' >".$value['texto']."</label>
                        </div>"; 
                      } ?>
                      <div style="margin-top:30px;">
                        <input id="enviar_voto" type="button" value="VOTAR" class="botao_padrao" onClick="document.getElementById('enqueteform').submit();" >
                        <input type="hidden" name="codigo" value="<?=$enquete['codigo']?>">
                        <input type="button" value="RESULTADO" class="botao_padrao" onClick="window.location='<?=DOMINIO?>enquete'" >
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <!-- BLOCO EQUIPE -->
        <div id="equipe" style="position: relative; padding-top:80px; padding-bottom: 80px;">
          <div class="container">
            <div class="row">
              <div class="col-sm-12 ">
                <div class="titulo_padrao" ><i class="fa fa-users" aria-hidden="true"></i> <?=$equipe_texto['titulo']?></div>
                <div style="margin-top:30px;"><?=$equipe_texto['conteudo']?></div>
              </div>
            </div>
            <?php 
              $n = 1; 
              foreach ($equipe as $key => $value) { 
                if($n == 1){ 
                  echo "<div class='row'>"; 
                } 
                echo "<div class='col-sm-3'>
                  <div class='equipe_item'>
                    <div class='equipe_item_img' style='background-image:url(".$value['imagem'].");'></div>
                    <div class='equipe_item_nome' >".$value['titulo']."</div>
                  </div>
                </div>"; 
                if($n == 4){ 
                  echo "</div>"; 
                  $n = 1; 
                } else { 
                  $n++; 
                } 
              } 
              if($n != 1){ 
                echo "</div>";
              }
            ?>
          </div>
        </div>
        <!-- BLOCO DE PROGRAMAÇÃO -->
        <div id="programacao" style="position: relative; padding-top:80px; padding-bottom: 80px; background-color:#333333">
            <div class="container">
                <div class='row'>
                  <div class='col-xs-12 col-sm-12 col-md-12'>
                    <div class="titulo_padrao">
                      <div align="center" class="style3">NOSSA PROGRAMAÇÃO <strong>😉</strong></div>
                    </div>
                  </div>
                </div>
                <div class='row'>
                  <div class='col-xs-12 col-sm-2 col-md-2'></div>
                  <div class='col-xs-12 col-sm-8 col-md-8'>
                    <div style="margin-top:50px; width: 100%;"><?=$programacao_texto['conteudo']?></div>
                  </div>
                  <div class='col-xs-12 col-sm-2 col-md-2'></div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="programacao_dias_semana">
                      <a class="dias_semana_item <?php if($dia == 1){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(1);" id="bt_prog_dia_1" >Domingo</a>
                      <a class="dias_semana_item <?php if($dia == 2){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(2);" id="bt_prog_dia_2" >Segunda</a>
                      <a class="dias_semana_item <?php if($dia == 3){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(3);" id="bt_prog_dia_3" >Terça</a>
                      <a class="dias_semana_item <?php if($dia == 4){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(4);" id="bt_prog_dia_4" >Quarta</a>
                      <a class="dias_semana_item <?php if($dia == 5){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(5);" id="bt_prog_dia_5" >Quinta</a>
                      <a class="dias_semana_item <?php if($dia == 6){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(6);" id="bt_prog_dia_6" >Sexta</a>
                      <a class="dias_semana_item <?php if($dia == 7){ echo "dias_semana_item_ativo"; } ?>" onClick="prog_trocdia(7);" id="bt_prog_dia_7" >Sábado</a>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="lista_dia" id="progr_lista_dia">
                      <table style="width:100%">
                        <tr style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">
                          <td class="prog_td_titulo" ><i class="fa fa-calendar" aria-hidden="true"></i> INÍCIO</td>
                          <td class="prog_td_titulo" >PROGRAMA</td>
                          <td class="prog_td_titulo" >APRESENTADOR</td>
                        </tr>
                        <?php foreach ($lista_dia as $key => $value) { 
                          echo "<tr class='prog_linha' >
                            <td class='prog_td_linha' style='text-align:center;' >".$value['inicio']."</td>
                            <td class='prog_td_linha' >".$value['titulo']."</td>
                            <td class='prog_td_linha' >".$value['apresentador']."</td>
                          </tr>"; 
                        } ?>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
    </div>
        </div>
        <!-- BLOCO DE CONTADORES -->
        <div class="div_numeros" >
  <div class="container">
    <div class="row">
      <div class="col-sm-4 module_cont pb43">
        <div class="module_content shortcode_counter">
          <div style="margin-bottom: 20px; text-align: center; width: 100%;">
            <!-- Otimização de imagem com lazy loading -->
            <img src="<?=$numero_1['imagem']?>" style="height:45px;" loading="lazy" alt="Contador 1">
          </div>
          <div class="counter_wrapper">
            <div class="counter_content">
              <div class="counter_body">
                <div class="stat_count_wrapper">
                  <h1 class="stat_count" data-count="<?=$numero_1['conteudo']?>">0</h1>
                  <h4 class="counter_title">
                    <?=$numero_1['titulo']?>
                  </h4>
                </div>
                <div class="stat_temp"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-4 module_cont pb43">
        <div class="module_content shortcode_counter">
          <div style="margin-bottom: 20px; text-align: center; width: 100%;">
            <!-- Otimização de imagem com lazy loading -->
            <img src="<?=$numero_2['imagem']?>" style="height:45px;" loading="lazy" alt="Contador 2">
          </div>
          <div class="counter_wrapper">
            <div class="counter_content">
              <div class="counter_body">
                <div class="stat_count_wrapper">
                  <h1 class="stat_count" data-count="<?=$numero_2['conteudo']?>">0</h1>
                  <h4 class="counter_title">
                    <?=$numero_2['titulo']?>
                  </h4>
                </div>
                <div class="stat_temp"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-4 module_cont pb43">
        <div class="module_content shortcode_counter">
          <div style="margin-bottom: 20px; text-align: center; width: 100%;">
            <!-- Otimização de imagem com lazy loading -->
            <img src="<?=$numero_3['imagem']?>" style="height:45px;" loading="lazy" alt="Contador 3">
          </div>
          <div class="counter_wrapper">
            <div class="counter_content">
              <div class="counter_body">
                <div class="stat_count_wrapper">
                  <h1 class="stat_count" data-count="<?=$numero_3['conteudo']?>">0</h1>
                  <h4 class="counter_title">
                    <?=$numero_3['titulo']?>
                  </h4>
                </div>
                <div class="stat_temp"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<button onclick="topFunction()" id="topBtn" title="Voltar ao topo">↑</button>
    <?php require_once('htm_rodape.php'); ?>
<!-- SCRIPTS ESSENCIAIS - ORDEM CORRIGIDA -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
<script src="<?=LAYOUT?>js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>js/modules.js"></script>
<script src="<?=LAYOUT?>js/theme.js"></script>
<script src="<?=LAYOUT?>js/jquery.themepunch.plugins.min.js"></script>
<script src="<?=LAYOUT?>js/jquery.themepunch.revolution.min.js"></script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script>
    function dominio(){ return "<?=DOMINIO?>"; }
    jQuery(document).ready(function($) {
        "use strict";
        // Slider Revolution
        if ($('.fullscreen_slider').length) {
            $('.fullscreen_slider').show().revolution({
                delay: 5000,
                startwidth: 1366,
                startheight: 650,
                fullWidth: "on",
                fullScreen: "off",
                navigationType: "bullet",
                fullScreenOffsetContainer: ".main_header",
                fullScreenOffset: ""
            });
        }
        // Galeria de fotos com Magnific Popup
        $('#galeria .photo_gallery').magnificPopup({
            delegate: 'a.photozoom',
            type: 'image',
            gallery: { enabled: true }
        });
        // Galeria de vídeos com Magnific Popup
        $('.video_gallery').magnificPopup({
          delegate: 'a.video-popup',
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false
        });
    });
    // Função para trocar programação
    function prog_trocdia(dia){
        $('#progr_lista_dia').html("<div style='text-align:center;'><img src='<?=LAYOUT?>img/loading.gif' style='width:25px;' loading='lazy'></div>");
        $.post('<?=DOMINIO?>programacao/listaini', { dia: dia }, function(data){
            if(data){
                $('#progr_lista_dia').html(data);
                $(".dias_semana_item").removeClass("dias_semana_item_ativo");
                $("#bt_prog_dia_"+dia).addClass("dias_semana_item_ativo");
            }
        });
    }
</script>
<script>
$(document).ready(function () {
  $('.stat_count').each(function () {
    var $this = $(this);
    var countTo = parseInt($this.attr('data-count'), 10);
    $({ countNum: 0 }).animate({ countNum: countTo }, {
      duration: 2000,
      easing: 'swing',
      step: function () {
        $this.text(Math.floor(this.countNum));
      },
      complete: function () {
        $this.text(this.countNum);
      }
    });
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const titles = document.querySelectorAll(".shortcode_accordion_item_title");
  titles.forEach(function(title) {
    title.addEventListener("click", function() {
      // fecha todas
      titles.forEach(function(t) {
        t.classList.remove("expanded_yes");
        t.classList.add("expanded_no");
        const body = t.nextElementSibling;
        if (body && body.classList.contains("shortcode_accordion_item_body")) {
          body.style.display = "none";
        }
      });
      // abre o clicado
      title.classList.remove("expanded_no");
      title.classList.add("expanded_yes");
      const content = title.nextElementSibling;
      if (content && content.classList.contains("shortcode_accordion_item_body")) {
        content.style.display = "block";
      }
    });
  });
  // inicia primeiro aberto
  const firstOpen = document.querySelector(".shortcode_accordion_item_title.expanded_yes");
  if (firstOpen) {
    const body = firstOpen.nextElementSibling;
    if (body && body.classList.contains("shortcode_accordion_item_body")) {
      body.style.display = "block";
    }
  }
});
</script>
<script>
let t=document.getElementById("topBtn");window.onscroll=function(){scrollFunction()};function scrollFunction(){document.body.scrollTop>1000||document.documentElement.scrollTop>1000?t.classList.add("show"):t.classList.remove("show")}function topFunction(){document.body.scrollTop=0;document.documentElement.scrollTop=0}
</script>
<!-- Removida duplicação do jQuery e Bootstrap -->
<script>function destacarContato(){if(window.location.hash==='#contato'){$('#contato').removeClass('highlight-blink');setTimeout(function(){$('#contato').addClass('highlight-blink');},100);}}$(document).ready(destacarContato);$(window).on('hashchange',destacarContato);</script>
<style>.highlight-blink{animation:blink 2s ease-in-out 3 alternate;}@keyframes blink{0%{background-color:transparent;}50%{background-color:#ffff00;box-shadow:0 0 20px rgba(255,255,0,0.8);}100%{background-color:transparent;}}</style>
<!-- ANIMAÇÃO DE SCROLL + BARRA DE PROGRESSO -->
<script>
document.addEventListener("DOMContentLoaded",()=>{const e=document.getElementById("progress-bar");window.addEventListener("scroll",()=>{const t=document.documentElement.scrollHeight-window.innerHeight,n=window.scrollY,r=Math.min(n/t*100,100);e.style.width=r+"%"});const o=()=>{document.querySelectorAll(".titulo_padrao, .noticias_div, .equipe_item, .shortcode_accordion_item_title, .div_numeros, .galeria_inicial, #equipe3, #programacao").forEach(e=>{const t=e.getBoundingClientRect().top;window.innerHeight-t>50&&(e.style.opacity="1",e.style.transform="translateY(0)")})};o(),window.addEventListener("scroll",o)});document.querySelectorAll(".titulo_padrao, .noticias_div, .equipe_item, .shortcode_accordion_item_title, .div_numeros, .galeria_inicial, #equipe3, #programacao").forEach(e=>{e.style.transition="opacity 0.6s ease, transform 0.6s ease",e.style.opacity="0",e.style.transform="translateY(20px)"});
</script>
</body>
</html>