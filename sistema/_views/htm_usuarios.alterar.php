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
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
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
	<form action="<?=$_base['objeto']?>alterar_grv" class="form-horizontal" method="post">
		
        <div class="panel-body">
        	
			<fieldset>
				
				<div class="form-group">
					<label class="col-md-12" >Nome</label>
					<div class="col-md-6">
						<input name="nome" type="text" class="form-control" value="<?=$data->nome?>" >
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-12" >E-mail</label>
					<div class="col-md-6">
						<input name="email" type="text" class="form-control" value="<?=$data->email_recuperacao?>" >
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-md-12" >Digite um usuário</label>
					<div class="col-md-4">
						<input name="usuario_sis" type="text" class="form-control" >
					</div>
				</div>
                
				<div class="form-group">
					<label class="col-md-12" >Digite uma senha</label>
					<div class="col-md-4">
						<input name="senha_sis" type="password" class="form-control" >
					</div>
				</div>
                
 				<div style="padding-top:10px; padding-bottom:20px;"><strong>PERMISSÕES</strong></div>

				<div class="form-group">
					<div class="col-md-12">
					<?php
 						
						foreach ($permissoes as $key => $value) {
 							
	 						if($value['id_pai'] == 0){
	 							$margem = 0;
							} else {
								$margem = 20;
							}

							if($value['check']){
								$check = " checked='' ";
							} else {
								$check = "";
							}

							echo "
							<div class='checkbox-custom' style='margin-left:".$margem."px;' >
								<input type='checkbox' id='check_".$value['id']."' name='setor_".$value['id']."' ".$check." value='1' >
								<label for='check_".$value['id']."' >".$value['titulo']."</label>
							</div>
							";
						}

					?>
					</div>
				</div>
				
			</fieldset>

			<div style="padding-top:15px;"></div>
            
		</div>
		
        <div class="panel-footer">
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn btn-primary">Salvar</button>
					<input type="hidden" name="codigo" value="<?=$data->codigo?>" >
                    <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';">Voltar</button>
				</div>
			</div>
		</div> 
 		
	</form>
	</section>






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

<!-- jQuery 2.2.3 -->
<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue'
    });
  });
</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>

</body>
</html>