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

/* ===== BOTÃO HAMBURGER DO TEMA ===== */
a.menu_toggler {
  background: none !important;
  background-image: none !important;
  width: 44px !important;
  height: 44px !important;
  position: relative !important;
  display: none;
  float: right !important;
  margin: 0 !important;
  border: 1px solid #666 !important;
  border-radius: 4px !important;
  padding: 0 !important;
  cursor: pointer;
  z-index: 1001;
  text-indent: -9999px;
}
a.menu_toggler:before,
a.menu_toggler:after,
a.menu_toggler span {
  content: '' !important;
  position: absolute !important;
  left: 8px !important;
  width: 26px !important;
  height: 3px !important;
  background-color: #fff !important;
  border-radius: 2px !important;
  transition: all 0.3s ease !important;
  display: block !important;
}
a.menu_toggler:before { top: 10px !important; }
a.menu_toggler span { top: 20px !important; }
a.menu_toggler:after { top: 30px !important; }

a.menu_toggler.close_toggler {
  background-color: #333 !important;
  border-color: #ffd700 !important;
}
a.menu_toggler.close_toggler:before {
  top: 20px !important;
  transform: rotate(45deg) !important;
  background-color: #ffd700 !important;
}
a.menu_toggler.close_toggler span { opacity: 0 !important; }
a.menu_toggler.close_toggler:after {
  top: 20px !important;
  transform: rotate(-45deg) !important;
  background-color: #ffd700 !important;
}
a.menu_toggler:hover {
  background-color: #333 !important;
  border-color: #ffd700 !important;
  box-shadow: 0 0 10px rgba(255,215,0,0.4);
}

/* Desktop */
@media (min-width: 768px) {
  a.menu_toggler { display: none !important; }
  a.tagline_toggler { display: none !important; }
  .mobile_menu_wrapper { display: none !important; }
  header nav { display: block !important; }
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
  a.menu_toggler {
    display: block !important;
    margin-top: -50px !important;
  }
  .mobile_menu_wrapper {
    display: none;
    background: #222 !important;
    width: 100% !important;
    padding: 10px 0 !important;
    margin-top: 10px !important;
    border-top: 2px solid #ffd700 !important;
    position: relative !important;
    z-index: 1000 !important;
  }
  .mobile_menu_wrapper .mobile_menu {
    padding: 0 15px !important;
    list-style: none !important;
  }
  .mobile_menu_wrapper .mobile_menu li {
    border-bottom: 1px solid rgba(255,255,255,0.1);
  }
  .mobile_menu_wrapper .mobile_menu li a.mob_link {
    color: #fff !important;
    font-size: 16px !important;
    font-weight: bold !important;
    padding: 12px 5px !important;
    display: block !important;
    border-bottom: none !important;
  }
  .mobile_menu_wrapper .mobile_menu li a.mob_link:hover {
    color: #ffd700 !important;
  }
  .mobile_menu_wrapper .sub-nav {
    display: none;
    background: rgba(0,0,0,0.3) !important;
    padding-left: 15px;
  }
  .mobile_menu_wrapper .showsub .sub-nav {
    display: block !important;
  }
  .mobile_menu_wrapper .sub-menu { width: 100% !important; }
  .mobile_menu_wrapper .sub-menu li a {
    color: #ccc !important;
    font-size: 14px !important;
    padding: 8px 5px !important;
  }
  .mobile_menu_wrapper li.menu-item-has-children:before {
    color: #ffd700 !important;
    right: 10px !important;
    top: 14px !important;
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

                <!-- Redes sociais ACIMA da linha branca -->
                <div class="row topo-redes-row">
                    <div class="col-sm-12">
                        <div class="topo_redes_sociais">
                            <?php foreach($_base['listaredes'] as $key=>$value){echo"<a href='".$value['endereco']."' target='_blank' class='topo_redes_sociais_item'><img src='".$value['imagem']."' alt='Rede Social'></a>";} ?>
                        </div>
                    </div>
                </div>

                <hr style="margin-top:10px;">

                <!-- Menu principal desktop -->
                <!-- O theme.js cria o a.menu_toggler e .mobile_menu_wrapper automaticamente -->
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
