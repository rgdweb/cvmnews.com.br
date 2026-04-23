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
  <link rel="stylesheet" href="<?=LAYOUT?>api/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.css">   
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/all.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/timepicker/bootstrap-timepicker.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/select2/select2.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>api/uploadfy/css/uploadify.css" type="text/css" />
  <link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css">
  
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

  				<ul class="nav nav-tabs">
  					
  					<li <?php if($aba_selecionada == "dados"){ echo "class='active'"; } ?> >
  						<a href="#dados" data-toggle="tab">Dados</a>
  					</li>
            <li <?php if($aba_selecionada == "imagem"){ echo "class='active'"; } ?> >
              <a href="#imagem" data-toggle="tab">Imagens</a>
            </li>

  				</ul>

  				<div class="tab-content" >

  					<div id="dados" class="tab-pane <?php if($aba_selecionada == "dados"){ echo "active"; } ?>" >
  					<form action="<?=$_base['objeto']?>alterar_grv" class="form-horizontal" method="post">  						

  						<fieldset>

                <div class="form-group">
  								<label class="col-md-12" >Titulo</label>
  								<div class="col-md-7">
  									<input name="titulo" type="text" class="form-control" value="<?=$data->titulo?>" >
  								</div>
  							</div>
                
                <div class="form-group">
                  <label class="col-md-12" >Endereço</label>
                  <div class="col-md-7">
                    <input name="endereco" type="text" class="form-control" value="<?=$data->endereco?>" >
                  </div>
                </div>

  						</fieldset>

              <div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <input type="hidden" name="codigo" value="<?=$data->codigo?>" >
                <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';" >Voltar</button>
  						</div>
  						
  				  </form>
  				  </div>


            <div id="imagem" class="tab-pane <?php if($aba_selecionada == "imagem"){ echo "active"; } ?>" >
              <?php if(!$data->imagem){ ?>
              <form action="<?=$_base['objeto']?>imagem/codigo/<?=$data->codigo?>" method="post" enctype="multipart/form-data">
                  
                  <fieldset> 
                    <label>Arquivo</label> 
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
                  </fieldset>
                  
                  <div style="text-align:left; padding-top:10px;">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';" >Voltar</button>
                  </div>
                  
              </form>
              <?php } else { ?>

                <div style="text-align:left;">
                  <img src="<?=PASTA_CLIENTE?>img_parceiros/<?=$data->imagem?>" style="max-width:300px;" >
                </div>

                <div style="text-align:left; padding-top:10px;">
                  <button type="button" class="btn btn-primary" onClick="confirma('<?=$_base['objeto']?>apagar_imagem/codigo/<?=$data->codigo?>');" >Apagar Imagem</button>
                  <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';" >Voltar</button>
                </div>

              <?php } ?>

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
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>api/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script src="<?=LAYOUT?>api/uploadfy/js/swfobject.js"></script>
<script src="<?=LAYOUT?>api/uploadfy/js/jquery.uploadify.v2.1.4.min.js"></script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script>

  $(document).ready(function(){
    $(".select2").select2();
  }); 

</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>

</body>
</html>