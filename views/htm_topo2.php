<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.topo_div1_icos a i { transition: all 0.3s ease; transform: scale(1); }
.topo_div1_icos a:hover i {
  transform: scale(1.2);
  color: #ffd700;
  text-shadow: 0 0 10px #ffd700, 0 0 20px #ffd700;
  filter: drop-shadow(0 0 8px #ffd700);
  -webkit-text-stroke: 1px #00bfff;
}

/* Redes sociais */
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

#main-menu-collapse {
  margin-top: 10px;
}

/* ===== NOSSO BOTÃO HAMBURGER (id=cvmmenu_btn) ===== */
#cvmmenu_btn {
  display: none;
  position: absolute;
  right: 15px;
  top: 10px;
  width: 44px;
  height: 44px;
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 4px;
  background: none;
  cursor: pointer;
  z-index: 99999;
  color: #fff;
  font-size: 22px;
  text-align: center;
  line-height: 44px;
  padding: 0;
}
#cvmmenu_btn:hover,
#cvmmenu_btn.menu_aberto {
  background-color: rgba(255,215,0,0.2);
  border-color: #ffd700;
  color: #ffd700;
}

/* NOSSO menu mobile */
#cvmmenu_mobile {
  display: none;
  background: #222;
  width: 100%;
  padding: 10px 0;
  border-top: 2px solid #ffd700;
  position: relative;
  z-index: 99998;
}
#cvmmenu_mobile ul {
  list-style: none;
  padding: 0 15px;
  margin: 0;
}
#cvmmenu_mobile ul li {
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
#cvmmenu_mobile ul li a {
  color: #fff;
  font-size: 16px;
  font-weight: bold;
  padding: 12px 5px;
  display: block;
  text-decoration: none;
}
#cvmmenu_mobile ul li a:hover {
  color: #ffd700;
}
#cvmmenu_mobile .sub-nav {
  display: none;
  background: rgba(0,0,0,0.3);
  padding-left: 15px;
}
#cvmmenu_mobile .sub-nav ul {
  padding: 0;
}
#cvmmenu_mobile .sub-nav li a {
  color: #ccc;
  font-size: 14px;
  padding: 8px 5px;
}

/* Desktop */
@media (min-width: 768px) {
  #cvmmenu_btn { display: none !important; }
  #cvmmenu_mobile { display: none !important; }
  header nav { display: block !important; }
}

/* Mobile */
@media (max-width: 767px) {
  #cvmmenu_btn { display: block !important; }
  a.menu_toggler { display: none !important; }
  a.tagline_toggler { display: none !important; }
  .mobile_menu_wrapper { display: none !important; }
  #main-menu-collapse nav { display: none !important; }
  .logo_div a.logo img { width: 80px !important; height: 80px !important; }
  .topo_redes_sociais { justify-content: center; padding: 5px 0; }
  .topo_redes_sociais_item { width: 32px; height: 32px; }
  /* Email: trocar texto longo por ENVIAR E-MAIL no mobile */
  .topo_div1_item_txt2.email-texto { font-size: 11px !important; word-break: break-all; }
  /* Ajustar itens de contato no mobile - empilhados verticalmente */
  .topo_div1_item { display: block !important; width: 100% !important; margin: 5px 0 !important; height: auto !important; text-align: center !important; }
  .topo_div1_icos { float: none !important; margin: 0 auto !important; }
  .topo_div1_textos { float: none !important; width: 100% !important; text-align: center !important; }
  .topo_div1_item_txt1 { text-align: center !important; padding-left: 0 !important; }
  .topo_div1_item_txt2 { text-align: center !important; padding-left: 0 !important; font-size: 12px !important; }
}
</style>

<div class="main_header">
    <div class="header_parent_wrap">
        <header style="position:relative;">
            <div class="container">

                <!-- NOSSO BOTÃO HAMBURGER - direto no HTML -->
                <button id="cvmmenu_btn" onclick="cvmToggleMenu()">
                    <i class="fas fa-bars"></i>
                </button>

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
                                <div class="topo_div1_icos"><a href="javascript:newPopup();"><i class="fas fa-play"></i></a></div>
                                <div class="topo_div1_textos">
                                    <div class="topo_div1_item_txt1"><?=$_base['topo_horarios']['titulo']?></div>
                                    <div class="topo_div1_item_txt2"><?=$_base['programacao']['programa']?></div>
                                </div>
                            </div>
                            <div class="topo_div1_item">
                                <div class="topo_div1_icos"><a href="mailto:<?=$_base['topo_email']['conteudo']?>"><i class="far fa-envelope"></i></a></div>
                                <div class="topo_div1_textos">
                                    <div class="topo_div1_item_txt1"><?=$_base['topo_email']['titulo']?></div>
                                    <div class="topo_div1_item_txt2 email-texto">ENVIAR E-MAIL</div>
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

                <!-- NOSSO MENU MOBILE -->
                <div id="cvmmenu_mobile">
                    <ul>
                        <?php
                        function geramenumobile2($lista,$controller,$pai){
                            if($pai!=0){echo"<div class='sub-nav'><ul>";}
                            foreach($lista as $key=>$value){
                                $titulo_limpo = mb_strtoupper(trim(strip_tags($value['titulo'])));
                                if (strpos($titulo_limpo, 'INICIAL') !== false) continue;
                                $endereco=$value['destino'];
                                $tem_filhos=(count($value['filhos'])>0);
                                echo"<li><a href='".$endereco."'>".$value['titulo']."</a>";
                                if($tem_filhos){
                                    geramenumobile2($value['filhos'],$controller,1);
                                }
                                echo"</li>";
                            }
                            if($pai!=0){echo"</ul></div>";}
                        }
                        geramenumobile2($_base['menu'],$controller,0);
                        ?>
                    </ul>
                </div>

            </div>
        </header>
    </div>
</div>

<!-- JavaScript do menu mobile - inline, não depende de jQuery nem de theme.js -->
<script>
function cvmToggleMenu() {
  var menu = document.getElementById('cvmmenu_mobile');
  var btn = document.getElementById('cvmmenu_btn');
  if (!menu || !btn) return;
  if (menu.style.display === 'block') {
    menu.style.display = 'none';
    btn.classList.remove('menu_aberto');
    btn.innerHTML = '<i class="fas fa-bars"></i>';
  } else {
    menu.style.display = 'block';
    btn.classList.add('menu_aberto');
    btn.innerHTML = '<i class="fas fa-times"></i>';
  }
}
</script>
