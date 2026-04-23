<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<style>
.topo_div1_icos a i { transition: all 0.3s ease; transform: scale(1); }
.topo_div1_icos a:hover i {
  transform: scale(1.2);
  color: #ffd700;
  text-shadow: 0 0 10px #ffd700, 0 0 20px #ffd700;
  filter: drop-shadow(0 0 8px #ffd700);
  -webkit-text-stroke: 1px #00bfff;
}

/* Redes sociais - acima da linha, fora do collapse */
.topo_redes_sociais {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  padding: 8px 0 2px 0;
}
.topo_redes_sociais_item {
  display: inline-block;
  width: 40px;
  height: 40px;
  transition: all 0.3s ease;
  animation: floatSuave 3s ease-in-out infinite;
}
.topo_redes_sociais_item:nth-child(1) { animation-delay: 0s; }
.topo_redes_sociais_item:nth-child(2) { animation-delay: 0.5s; }
.topo_redes_sociais_item:nth-child(3) { animation-delay: 1s; }
.topo_redes_sociais_item:nth-child(4) { animation-delay: 1.5s; }
.topo_redes_sociais_item:nth-child(5) { animation-delay: 2s; }
.topo_redes_sociais_item img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  border-radius: 50%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}
.topo_redes_sociais_item:hover img {
  transform: scale(1.15) translateY(-3px);
  box-shadow: 0 4px 12px rgba(255,215,0,0.4);
}
@keyframes floatSuave {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}

.logo img { transition: transform 0.3s ease; }
.logo:hover img { transform: scale(1.05); }
.menu li a { transition: all 0.3s ease; }
.menu li a:hover {
  transform: translateY(-2px);
  text-shadow: 0 0 10px currentColor;
  filter: drop-shadow(0 0 8px currentColor);
  -webkit-text-stroke: 1px #00bfff;
}
.menu li:nth-child(1) a:hover { color: #ffd700; }
.menu li:nth-child(2) a:hover { color: #ff6b6b; }
.menu li:nth-child(3) a:hover { color: #4ecdc4; }
.menu li:nth-child(4) a:hover { color: #45b7d1; }
.menu li:nth-child(5) a:hover { color: #96ceb4; }
.menu li:nth-child(6) a:hover { color: #ffeaa7; }
.menu li:nth-child(7) a:hover { color: #fd79a8; }

.navbar-toggle { transition: all 0.3s ease; cursor: pointer; }
.navbar-toggle:hover {
  transform: scale(1.1);
  background-color: #ffd700;
  box-shadow: 0 0 15px #ffd700;
  filter: drop-shadow(0 0 10px #ffd700);
}

/* Desktop */
@media (min-width: 768px) {
  .navbar-header { display: none !important; }
  #main-menu-collapse {
    display: block !important;
    height: auto !important;
    overflow: visible !important;
  }
}

/* Mobile */
@media (max-width: 767px) {
  .logo_div a.logo img {
    width: 80px !important;
    height: 80px !important;
  }
  .topo_redes_sociais {
    justify-content: center;
    padding: 5px 0;
  }
  .topo_redes_sociais_item {
    width: 32px;
    height: 32px;
  }
  .navbar-header {
    display: block !important;
    position: relative;
    float: right;
    margin-top: -45px;
    z-index: 1001;
  }
  .navbar-toggle {
    display: block !important;
    background-color: #333;
    border: 1px solid #666;
    border-radius: 4px;
    padding: 9px 10px;
    margin: 8px 0;
    cursor: pointer;
    position: relative;
    z-index: 1002;
  }
  .navbar-toggle .icon-bar {
    display: block !important;
    width: 22px;
    height: 2px;
    border-radius: 1px;
    background-color: #fff;
    margin-top: 4px;
  }
  .navbar-toggle .icon-bar:first-child {
    margin-top: 0;
  }
  /* Menu mobile escondido por padrão */
  #main-menu-collapse {
    display: none;
    margin-top: 10px;
    clear: both;
    width: 100%;
    background: inherit;
  }
  /* Menu mobile aberto */
  #main-menu-collapse.menu-aberto {
    display: block !important;
  }
  .menu {
    flex-direction: column;
    gap: 0;
    width: 100%;
  }
  .menu li a {
    display: block;
    padding: 12px 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    width: 100%;
  }
  .menu .sub-nav { display: block; }
  .menu .sub-menu {
    list-style: none;
    padding-left: 15px;
    margin: 0;
  }
  .menu .sub-menu li a {
    font-size: 14px;
    padding: 8px 15px;
  }
}

#main-menu-collapse {
  margin-top: 10px;
}
</style>

<div class="main_header">
    <div class="header_parent_wrap">
        <header>
            <div class="container">
                <!-- Linha 1: Logo + Contato -->
                <div class="row">
                    <div class="col-sm-4">
                        <div class="logo_div">
                            <a href="<?=DOMINIO?>" class="logo">
                                <img alt="" src="<?=$_base['imagem']['147129831543478']?>">
                            </a>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="topo_div1">
                            <div class="topo_div1_item">
                                <div class="topo_div1_icos"><a href="javascript:newPopup();"><i class="fa fa-play"></i></a></div>
                                <div class="topo_div1_textos">
                                    <div class="topo_div1_item_txt1"><?=$_base['topo_horarios']['titulo']?></div>
                                    <div class="topo_div1_item_txt2"><?=$_base['programacao']['programa']?></div>
                                </div>
                            </div>
                            <div class="topo_div1_item">
                                <div class="topo_div1_icos"><a href="mailto:<?=$_base['topo_email']['conteudo']?>"><i class="far fa-envelope"></i></a></div>
                                <div class="topo_div1_textos">
                                    <div class="topo_div1_item_txt1"><?=$_base['topo_email']['titulo']?></div>
                                    <div class="topo_div1_item_txt2"><?=$_base['topo_email']['conteudo']?></div>
                                </div>
                            </div>
                            <div class="topo_div1_item">
                                <div class="topo_div1_icos"><a href="tel:<?=$_base['topo_ligue']['conteudo']?>"><i class="fas fa-mobile-alt"></i></a></div>
                                <div class="topo_div1_textos">
                                    <div class="topo_div1_item_txt1"><?=$_base['topo_ligue']['titulo']?></div>
                                    <div class="topo_div1_item_txt2"><?=$_base['topo_ligue']['conteudo']?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redes sociais ACIMA da linha branca, FORA do collapse -->
                <div class="row topo-redes-row">
                    <div class="col-sm-12">
                        <div class="topo_redes_sociais">
                            <?php foreach($_base['listaredes'] as $key=>$value){echo"<a href='".$value['endereco']."' target='_blank' class='topo_redes_sociais_item'><img src='".$value['imagem']."' alt='Rede Social'></a>";} ?>
                        </div>
                    </div>
                </div>

                <hr style="margin-top:10px;">

                <!-- Botão hamburger mobile - onclick próprio -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" id="btn-menu-toggle" onclick="toggleMenuMobile()">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <!-- Menu principal (SÓ menu, sem redes sociais) -->
                <div id="main-menu-collapse">
                    <nav>
                        <ul class="menu">
                            <?php
                            function geramenu2($lista,$controller,$pai){
                                if($pai!=0){echo"<div class='sub-nav'><ul class='sub-menu'>";}
                                foreach($lista as $key=>$value){
                                    $titulo_limpo = mb_strtoupper(trim(strip_tags($value['titulo'])));
                                    if (strpos($titulo_limpo, 'INICIAL') !== false) continue;

                                    $array=explode('#',$value['destino']);
                                    $numero=count($array);
                                    $end_final='#'.end($array);
                                    $endereco=$value['destino'];
                                    $namesmapagina=($end_final!="#conteudo"&&$numero>1)?" class='scrollSuave'":"";
                                    $pre_submenu=(count($value['filhos'])>0)?"class='menu-item-has-children'":"";

                                    echo"<li $pre_submenu><a $namesmapagina href='".$endereco."'>".$value['titulo']."</a>";
                                    if(count($value['filhos'])>0){
                                        geramenu2($value['filhos'],$controller,1);
                                    }
                                    echo"</li>";
                                }
                                if($pai!=0){echo"</ul></div>";}
                            }
                            geramenu2($_base['menu'],$controller,0);
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>
    </div>
</div>

<!-- JavaScript próprio para o menu mobile - NÃO depende do Bootstrap -->
<script>
function toggleMenuMobile() {
  var menu = document.getElementById('main-menu-collapse');
  var btn = document.getElementById('btn-menu-toggle');
  if (!menu) return;
  if (menu.classList.contains('menu-aberto')) {
    menu.classList.remove('menu-aberto');
    btn.classList.remove('active');
  } else {
    menu.classList.add('menu-aberto');
    btn.classList.add('active');
  }
}
(function() {
  function ajustarMenu() {
    var menu = document.getElementById('main-menu-collapse');
    if (!menu) return;
    if (window.innerWidth >= 768) {
      menu.classList.remove('menu-aberto');
      menu.style.display = '';
      menu.style.height = '';
      menu.style.overflow = '';
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ajustarMenu);
  } else {
    ajustarMenu();
  }
  window.addEventListener('resize', ajustarMenu);
})();
</script>
