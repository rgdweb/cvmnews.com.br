<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" href="<?=FAVICON?>" type="image/x-icon" />
  <title><?=$_titulo?> - <?=TITULO_VIEW?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>font-awesome-4.6.2/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body class="hold-transition skin-blue <?php if($_base['menu_fechado'] == 1){ echo "sidebar-collapse"; } ?> sidebar-mini">
<div class="wrapper">

	<?php require_once('htm_modal.php'); ?>

	<?php require_once('htm_topo.php'); ?>

	<?php require_once('htm_menu.php'); ?>

	<div class="content-wrapper">

	    <section class="content-header">
	      <h1>
	      <?=$_titulo?>
	      <small><?=$_subtitulo?></small>
	      </h1> 
	    </section>

		<!-- Main content -->
    	<section class="content">
    	<div class="row">

        	<div class="col-xs-12">

				<div class="nav-tabs-custom">

					<ul class="nav nav-tabs ">
						
						<li <?php if($aba_selecionada == "menu"){ echo "class='active'"; } ?> >
							<a href="#menu" data-toggle="tab">Menu Topo</a>
						</li>

						<li <?php if($aba_selecionada == "menu_rodape"){ echo "class='active'"; } ?> >
							<a href="#menu_rodape" data-toggle="tab">Menu Rodape</a>
						</li>

						<li <?php if($aba_selecionada == "cores"){ echo "class='active'"; } ?> >
							<a href="#cores" data-toggle="tab">Cores</a>
						</li>

					</ul>

					<div class="tab-content" >


	  					<div id="menu" <?php if($aba_selecionada == "menu"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >

	  						<div>
	  							<button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>novo_menu', 'Novo Menu');" >Novo Menu</button>
					        </div>

					        <hr>

					        <div style="padding-bottom:15px;" >Ordene movendo para cima e para baixo e transforme em sub-menus movendo para os lados.</div>
					        
					        <div class="dd" id="nestable" >
							<?=$listamenu?>
							</div>

							<div style="clear:both; padding-top:15px;">
								<form action="<?=$_base['objeto']?>salvar_ordem_menu" method="post" >
				                    <input type="hidden" name="ordem" id="nestable-output" class="form-control">
				                    <button type="submit" class="btn btn-primary" >Salvar Ordem</button>
				                </form>
			                </div>

						</div>


	  					<div id="menu_rodape" <?php if($aba_selecionada == "menu_rodape"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >

	  						<div>
	  							<button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>novo_menu_rodape', 'Novo Menu Rodape');" >Novo Menu</button>
					        </div>

					        <hr>

					        <div style="padding-bottom:15px;" >Ordene movendo para cima e para baixo.</div>
					        
					        <div class="dd" id="nestable_rodape" >
							<?=$listamenu_rodape?>
							</div>
							
							<div style="clear:both; padding-top:15px;">
								<form action="<?=$_base['objeto']?>salvar_ordem_menu_rodape" method="post" >
				                    <input type="hidden" name="ordem" id="nestable-output_rodape" class="form-control">
				                    <button type="submit" class="btn btn-primary" >Salvar Ordem</button>
				                </form>
			                </div>
			                
						</div>


						<div id="cores" <?php if($aba_selecionada == "cores"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
	                	<form action="<?=$_base['objeto']?>cores_grv" class="form-horizontal" method="post">
	                	<?php

	                		$i = 1;
	                		foreach ($listacores as $key => $value) {	                			 

	                			if(!($i % 2)) { $bg = " style='padding-bottom:15px; background-color:#eee; ' "; } else { $bg = " style='padding-bottom:15px; ' "; } $i++;

	                			echo "
	                			<div class='row'>
	                			<div class='col-xs-12' $bg >";
		                		
		                			echo "
										<div style='float:left; width:50%; text-align:center; margin-top:15px;' >
											<input type='text' class='form-control' name='cor_titulo_".$value['id']."' value='".$value['titulo']."' >
										</div>
										<div style='float:left; width:25%; text-align:center; margin-top:15px;' >
											<input type='text' class='form-control my-colorpicker1' name='cor_".$value['id']."' value='".$value['cor']."' >
										</div>
										<div style='clear:both'></div>
		                			";
		                			
		                		echo "
		                		</div>
		                		</div>
		                		";

	                		} 

	                	?>
		                	
			                <div style="padding-top:15px;">
			                	<button type="submit" class="btn btn-primary">Salvar</button>
							</div>

						</form>
						</div>

                		
					</div>
				
				</div>
				
        	</div>

		</div>
		<!-- /.row -->
	</section>
    <!-- /.content -->

  </div>
  <!-- /.content-wrapper -->
  <?php require_once('htm_rodape.php'); ?>

</div>
<!-- ./wrapper -->

<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/select2/select2.full.min.js"></script>
<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.js"></script>
<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.js"></script> 
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>api/nestable/jquery.nestable.js"></script>
<script src="<?=LAYOUT?>api/nestable/examples.nestable.js"></script>

<!-- page script -->
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>

<script>
$(function(){
	$(".my-colorpicker1").colorpicker();
});
</script>

</body>
</html>