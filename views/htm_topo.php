<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!-- Font Awesome 6 (único) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<style>
/* Ícones e animações */
.topo_div1_icos a i,
.topo_redes_sociais_item img {
  transition: all 0.3s ease;
  transform: scale(1);
}
.topo_div1_icos a:hover i {
  transform: scale(1.2);
  color: #ffd700;
  text-shadow: 0 0 10px #ffd700, 0 0 20px #ffd700;
  filter: drop-shadow(0 0 8px #ffd700);
  -webkit-text-stroke: 1px #00bfff;
}
.topo_redes_sociais_item {
  display: inline-block;
  animation: float 3s ease-in-out infinite;
}
.topo_redes_sociais_item:hover img {
  transform: scale(1.15) translateY(-5px);
  box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
}
@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-8px);
  }
}

/* Logo */
.logo_div a.logo img {
  width: 160px;
  height: 160px;
  border-radius: 30%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
  background-color: #fff;
  padding: 4px;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.logo_div a.logo:hover img {
  transform: scale(2.1);
}

/* Menu geral */
.menu {
  white-space: nowrap;
  padding-left: 0;
  margin: 0;
  list-style: none;
  display: flex;
  justify-content: flex-start;
  gap: 20px;
}
.menu li a {
  display: inline-block;
  padding: 10px 5px;
  transition: all 0.3s ease;
}
.menu li a:hover {
  transform: translateY(-2px);
  text-shadow: 0 0 10px currentColor;
  filter: drop-shadow(0 0 8px currentColor);
  -webkit-text-stroke: 1px #00bfff;
}

/* Cores específicas de cada item do menu ao passar o mouse */
.menu li:nth-child(1) a:hover { color: #ffd700; }
.menu li:nth-child(2) a:hover { color: #ff6b6b; }
.menu li:nth-child(3) a:hover { color: #4ecdc4; }
.menu li:nth-child(4) a:hover { color: #45b7d1; }
.menu li:nth-child(5) a:hover { color: #96ceb4; }
.menu li:nth-child(6) a:hover { color: #ffeaa7; }
.menu li:nth-child(7) a:hover { color: #fd79a8; }

/* Botão mobile */
.navbar-toggle {
  transition: all 0.3s ease;
}
.navbar-toggle:hover {
  transform: scale(1.1);
  background-color: #ffd700;
  box-shadow: 0 0 15px #ffd700;
  filter: drop-shadow(0 0 10px #ffd700);
}

/* Redes sociais - posicionamento correto */
.topo_redes_sociais {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
  padding: 5px 0;
}

/* Menu row - alinhar verticalmente */
#main-menu-collapse .row {
  display: flex;
  align-items: center;
}

/* Esconder botão mobile em telas grandes */
@media (min-width: 768px) {
  .navbar-header {
    display: none;
  }
}

/* Logo menor no mobile */
@media (max-width: 767px) {
  .logo_div a.logo img {
    width: 80px !important;
    height: 80px !important;
    padding: 3px;
  }

  /* Botão hamburger visível no mobile */
  .navbar-header {
    display: block;
    position: relative;
    float: right;
    margin-top: -50px;
    z-index: 1000;
  }

  .navbar-toggle {
    display: block !important;
    background-color: #333;
    border: 1px solid #666;
    border-radius: 4px;
    padding: 9px 10px;
    margin: 8px 0;
    cursor: pointer;
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

  /* Menu colapsado no mobile */
  #main-menu-collapse {
    margin-top: 10px;
    clear: both;
  }

  #main-menu-collapse .row {
    display: block;
  }

  #main-menu-collapse .col-sm-9,
  #main-menu-collapse .col-sm-3 {
    width: 100%;
  }

  .menu {
    flex-direction: column;
    gap: 0;
  }

  .menu li a {
    display: block;
    padding: 12px 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
  }

  .topo_redes_sociais {
    justify-content: center;
    padding: 15px 0;
  }
}

/* Ajuste da margem do menu colapsado */
#main-menu-collapse {
  margin-top: 10px;
}

</style>
<a href="https://titan.hostgator.com.br/mail/" target="_blank" class="link-webmail-flutuante">
    Webmail 
</a>
<div class="main_header">
  <div class="header_parent_wrap">
    <header>
      <div class="container">
        <div class="row">
          <div class="col-sm-4">
            <div class="logo_div">
              <a href="<?=DOMINIO?>" class="logo">
                <img src="<?=$_base['imagem']['147129831543478']?>" alt="Logo">
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

        <hr style="margin-top:10px;">

        <!-- Botão para menu mobile -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>

        <!-- Menu principal -->
        <div class="collapse navbar-collapse" id="main-menu-collapse">
          <div class="row">
            <div class="col-sm-9">
              <nav>
                <ul class="menu">
                                     <?php
                    function geramenu($lista, $controller, $pai) {
                      if ($pai != 0) { echo "<div class='sub-nav'><ul class='sub-menu'>"; }
                      foreach ($lista as $value) {
                    
                        // Remove qualquer item com título semelhante a "INICIAL"
                        $titulo_limpo = mb_strtoupper(trim(strip_tags($value['titulo'])));
                        if (strpos($titulo_limpo, 'INICIAL') !== false) continue;
                    
                        $array = explode('#', $value['destino']);
                        $numero = count($array);
                        $end_final = '#'.end($array);
                        $endereco = $value['destino'];
                        $namesmapagina = ($end_final != "#conteudo" && $numero > 1) ? " class='scrollSuave'" : "";
                        $pre_submenu = (count($value['filhos']) > 0) ? "class='menu-item-has-children'" : "";
                    
                        echo "<li $pre_submenu><a $namesmapagina href='$endereco'>{$value['titulo']}</a>";
                        if (count($value['filhos']) > 0) {
                          geramenu($value['filhos'], $controller, 1);
                        }
                        echo "</li>";
                      }
                      if ($pai != 0) { echo "</ul></div>"; }
                    }
                    geramenu($_base['menu'], $controller, 0);
                    ?>

                </ul>
              </nav>
            </div>
            <div class="col-sm-3">
              <div class="topo_redes_sociais">
                <?php foreach($_base['listaredes'] as $value): ?>
                  <a href="<?=$value['endereco']?>" target="_blank" class="topo_redes_sociais_item">
                    <img src="<?=$value['imagem']?>" alt="Rede Social">
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </header>
  </div>
</div>
