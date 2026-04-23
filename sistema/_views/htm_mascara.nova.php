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
  <link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css" />
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
        	<section class="panel">        	 
        	<form action="<?=$_base['objeto']?>novo_grv" class="form-horizontal" method="post" enctype="multipart/form-data" > 
		    
		    <div class="panel-body">
		    <fieldset>

		    	<div class="form-group">
		    		<label class="col-md-12" >Titulo</label>
		    		<div class="col-md-6">
		    			<input name="titulo" type="text" class="form-control" >
		    		</div>
		    	</div>

		    	<div id='tipo_imagem' class="form-group" >
		    		<label class="col-md-12">Logo (Formato Png)</label>
		    		<div class="col-md-6">
		    			<div class="fileupload fileupload-new" data-provides="fileupload">
		    				<div class="input-append">
			    				<div class="uneditable-input">
				    				<i class="fa fa-file fileupload-exists"></i>
				    				<span class="fileupload-preview"></span>
			    				</div>
			    				<span class="btn btn-default btn-file">
			    				<span class="fileupload-exists">Alterar</span>
			    				<span class="fileupload-new">Procurar arquivo</span>
			    				<input type="file" name="arquivo" />
			    				</span>
			    				<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remover</a>
			    			</div>
			    		</div>
			    	</div>
			    </div>

			    <div class="form-group">
			    	<label class="col-md-12" >Posição</label>
			    	<div class="col-md-6">
			    		<select data-plugin-selectTwo class="form-control populate" name='posicao' >
			    			<option value='1' >Centro</option>
			    			<option value='2' >Canto esquerdo superior</option>
			    			<option value='3' >Canto direito superior</option>
			    			<option value='4' >Canto esquerdo inferior</option>
			    			<option value='5' >Canto direito inferior</option>
			    		</select>
			    	</div>
			    </div>

			    <div class="form-group">
			    	<label class="col-md-12" >Preencher Imagem</label>
			    	<div class="col-md-6">
			    		<select data-plugin-selectTwo class="form-control populate" name='preencher' >
			    			<option value='0' >Não</option>
			    			<option value='1' >Sim</option>
			    		</select>
			    	</div>
			    </div>

			</fieldset>
			</div>

			<div class="panel-footer">
				<div class="row">
					<div class="col-md-12">
						<button type="submit" class="btn btn-primary">Salvar</button>
	                    <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';">Voltar</button>
					</div>
				</div>
			</div> 
			
		</form>
		</section>
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
<script src="<?=LAYOUT?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script>
  $(function () {
    $('#tabela1').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true
    });
  });
</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>

</body>
</html>