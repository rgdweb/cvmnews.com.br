<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<style>
.topo_div1_icos a i, .topo_redes_sociais_item img { transition: all 0.3s ease; transform: scale(1); }
.topo_div1_icos a:hover i { 
  transform: scale(1.2); 
  color: #ffd700; 
  text-shadow: 0 0 10px #ffd700, 0 0 20px #ffd700, 0 0 30px #ffd700;
  filter: drop-shadow(0 0 8px #ffd700);
  -webkit-text-stroke: 1px #00bfff;
  text-stroke: 1px #00bfff;
  box-shadow: 0 0 5px #00bfff, 0 0 10px #00bfff, 0 0 15px #00bfff;
}
.topo_redes_sociais_item { display: inline-block; animation: float 3s ease-in-out infinite; }
.topo_redes_sociais_item:hover img { transform: scale(1.15) translateY(-5px); box-shadow: 0 5px 15px rgba(255,215,0,0.3); }
.topo_redes_sociais_item:nth-child(1) { animation-delay: 0s; }
.topo_redes_sociais_item:nth-child(2) { animation-delay: 0.5s; }
.topo_redes_sociais_item:nth-child(3) { animation-delay: 1s; }
.topo_redes_sociais_item:nth-child(4) { animation-delay: 1.5s; }
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-8px); }
}
.logo img { transition: transform 0.3s ease; }
.logo:hover img { transform: scale(1.05); }
.menu li a { transition: all 0.3s ease; }
.menu li a:hover { 
  transform: translateY(-2px); 
  text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
  filter: drop-shadow(0 0 8px currentColor);
  -webkit-text-stroke: 1px #00bfff;
  text-stroke: 1px #00bfff;
}
.menu li:nth-child(1) a:hover { color: #ffd700; } /* INICIAL - Dourado */
.menu li:nth-child(2) a:hover { color: #ff6b6b; } /* FOTOS - Vermelho */
.menu li:nth-child(3) a:hover { color: #4ecdc4; } /* PROGRAMAÇÃO - Verde água */
.menu li:nth-child(4) a:hover { color: #45b7d1; } /* NOTICIAS - Azul claro */
.menu li:nth-child(5) a:hover { color: #96ceb4; } /* EQUIPE - Verde claro */
.menu li:nth-child(6) a:hover { color: #ffeaa7; } /* CONTATO - Amarelo claro */
.menu li:nth-child(7) a:hover { color: #fd79a8; } /* TV | VIDEOS - Rosa */
.navbar-toggle { transition: all 0.3s ease; }
.navbar-toggle:hover { 
  transform: scale(1.1); 
  background-color: #ffd700; 
  box-shadow: 0 0 15px #ffd700, 0 0 25px #ffd700, 0 0 35px #ffd700;
  filter: drop-shadow(0 0 10px #ffd700);
}
</style>

<div class="main_header">
    <div class="header_parent_wrap">
        <header>
            <div class="container">
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

                <div style="position:relative;width:100%;border-top:1px solid #ccc;padding-top:15px;margin-top:-10px;"></div>
                
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                
                <div class="collapse navbar-collapse" id="main-menu-collapse">
                    <div class="row">
                        <div class="col-sm-8">
                            <nav>
                                <ul class="menu">
                                    <?php
                                    function geramenu($lista,$controller,$pai){
                                        if($pai!=0){echo"<div class='sub-nav'><ul class='sub-menu'";}
                                        foreach($lista as $key=>$value){
                                            $array=explode('#',$value['destino']);
                                            $numero=count($array);
                                            $end_final='#'.end($array);
                                            if($end_final=="#conteudo"){
                                                $namesmapagina="";
                                                $endereco=$value['destino'];
                                            }else{
                                                if($numero>1){
                                                    $namesmapagina=" class='scrollSuave' ";
                                                    $endereco=$value['destino'];
                                                }else{
                                                    $namesmapagina="";
                                                    $endereco=$value['destino'];
                                                }
                                            }
                                            $pre_submenu=(count($value['filhos'])>0)?"class='menu-item-has-children'":"";
                                            echo"<li $pre_submenu><a $namesmapagina href='".$endereco."'>".$value['titulo']."</a>";
                                            if(count($value['filhos'])>0){
                                                geramenu($value['filhos'],$controller,1);
                                            }
                                            echo"</li>";
                                        }
                                        if($pai!=0){echo"</ul></div>";}
                                    }
                                    geramenu($_base['menu'],$controller,0);
                                    ?>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-sm-4">
                            <div class="topo_redes_sociais">
                                <?php foreach($_base['listaredes'] as $key=>$value){echo"<a href='".$value['endereco']."' target='_blank' class='topo_redes_sociais_item'><img src='".$value['imagem']."'></a>";} ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    </div>
</div>