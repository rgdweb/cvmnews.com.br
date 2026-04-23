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
            <li <?php if($aba_selecionada == "respostas"){ echo "class='active'"; } ?> >
              <a href="#respostas" data-toggle="tab">Respostas</a>
            </li>

  				</ul>

  				<div class="tab-content" >

  					<div id="dados" class="tab-pane <?php if($aba_selecionada == "dados"){ echo "active"; } ?>" >
  					<form action="<?=$_base['objeto']?>alterar_grv" class="form-horizontal" method="post">  						

  						<fieldset>
  							
                <div class="form-group">
  								<label class="col-md-12" >Enquete</label>
  								<div class="col-md-6">
  									<input name="enquete" type="text" class="form-control" value="<?=$data->enquete?>" >
  								</div>
  							</div>

                <div class="form-group">
                  <label class="col-md-12">Status</label>
                  <div class="col-md-6">
                    <select name="status" class="form-control select2" style="width: 100%;" >
                      <option value='1' <?php if($data->status == 1){ echo "selected"; } ?> >Ativo</option>
                      <option value='2' <?php if($data->status == 2){ echo "selected"; } ?>  >Finalizado</option>
                    </select>
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


            <div id="respostas" class="tab-pane <?php if($aba_selecionada == "respostas"){ echo "active"; } ?>" >
              
              <button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>nova_resposta/codigo/<?=$data->codigo?>', 'Nova Resposta');">Nova Resposta</button>

              <hr>

              <table class="table table-bordered table-striped">

                <thead>
                  <tr>
                    <th>#</th>
                    <th>Resposta</th>
                    <th class="center">Votos</th>
                    <th>Porcentagem</th>
                  </tr>
                </thead>
                
                <tbody>
                <?php
                  
                  $i = 1;
                  foreach ($relatorio as $key => $value) {
                   
                   $linklinha = "onClick=\"modal('".$_base['objeto']."alterar_resposta/codigo_enquete/".$data->codigo."/codigo/".$value['codigo']."', 'Alterar Resposta');\" style='cursor:pointer;' ";

                    echo "
                    <tr>
                        <td $linklinha >$i</td>
                        <td $linklinha >".$value['resposta']."</td>
                        <td $linklinha class='center' >".$value['votos']."</td>
                        <td $linklinha >
                          <div class='progress progress-sm progress-half-rounded m-none mt-xs light' >
                            <div class='progress-bar progress-bar-primary' style='width:".$value['votos_porc']."%;'>".$value['votos_porc']."%</div>
                          </div>
                        </td>
                    </tr>
                    ";

                  $i++;
                  }

                ?>
                </tbody>

              </table>


              <div style="padding-top:15px;" >
                <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>';" >Voltar</button>
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
<script src="<?=LAYOUT?>plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?=LAYOUT?>plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?=LAYOUT?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>api/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script src="<?=LAYOUT?>api/uploadfy/js/swfobject.js"></script>
<script src="<?=LAYOUT?>api/uploadfy/js/jquery.uploadify.v2.1.4.min.js"></script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script>
$(document).ready(function() {

  $(".select2").select2();

});
</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>

</body>
</html>