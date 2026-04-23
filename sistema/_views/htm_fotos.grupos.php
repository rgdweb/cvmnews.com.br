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
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/select2/select2.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
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

				<div class="box">
					<div class="box-body">

	  					<div>
	  						<button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>novo_grupo', 'Novo Grupo');" >Novo Grupo</button>
	  						<button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial';" >Voltar</button>
					    </div>
					    
					    <hr>
					    
					    <div style="padding-bottom:15px;" >Ordene movendo para cima e para baixo e transforme em sub-categoria movendo para os lados.</div>

					    <?php
					    	
					    	$objeto_end = $_base['objeto'];

					    	function montaCategorias($lista, $objeto_end){
							 	$retorno = '';
							 	$retorno .= '<ol class="dd-list">';
								foreach($lista as $key => $value){

									if(isset($value['titulo'])){
										
										$retorno .= '<li class="dd-item dd3-item" data-id="'.$value['id'].'" >';
										
										$retorno .= '
										<div class="dd-handle dd3-handle" ><i class="fa fa-arrows"></i></div>
										<div class="dd3-content-editar" onClick="window.location=\''.$objeto_end.'alterar_grupo/codigo/'.$value['codigo'].'\';" ><i class="fa fa-pencil"></i></div>
										<div class="dd3-content">'.$value['titulo'].'</div>';
										
										$retorno .= montaCategorias($value['filhos'], $objeto_end);

										$retorno .= '</li>';

									}
								}
								$retorno .= '</ol>';
								return $retorno;
							} 

					    ?>
					    <div class="dd" id="nestable" >
							<?=montaCategorias($lista_grupos, $objeto_end)?>
						</div>

						<div style="clear:both; padding-top:15px;">
						<form action="<?=$_base['objeto']?>salvar_ordem_grupos" method="post" >
			            	<input type="hidden" name="ordem" id="nestable-output" class="form-control">
			                <button type="submit" class="btn btn-primary" >Salvar Ordem</button>
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
<script src="<?=LAYOUT?>api/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>plugins/select2/select2.full.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script src="<?=LAYOUT?>api/nestable/jquery.nestable.js"></script>
<script src="<?=LAYOUT?>api/nestable/examples.nestable.js"></script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>

</body>
</html>