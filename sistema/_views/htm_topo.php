<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<header class="main-header">

  <a href="<?=DOMINIO?>" class="logo">
    <span class="logo-mini"><img src="<?=LAYOUT?>img/ico.png" style="width:25px;" ></span>
    <span class="logo-lg" style="text-align:left;"><img src="<?=$_base['logo']?>" style="height:36px;"></span>
  </a>
  
  <nav class="navbar navbar-static-top">
      
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" onclick="altera_menu();">
        <span class="sr-only">Navegação</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <li class="dropdown user user-menu">
            
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?=$_base['conta_imagem']?>" class="user-image">
              <span class="hidden-xs"><?=$_base['conta_nome']?></span>
            </a>
            
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?=$_base['conta_imagem']?>" class="img-circle" alt="User Image">

                <p>
                  <?=$_base['conta_nome']?>
                  <small><?=$_base['conta_tipo']?></small>
                </p>
              </li>              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?=DOMINIO?>perfil" class="btn btn-default btn-flat">Perfil</a>
                </div>
                <div class="pull-right">
                  <a href="<?=DOMINIO?>autenticacao/logout" class="btn btn-default btn-flat">Sair</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
  </nav>
</header>