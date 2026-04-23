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
  <link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css"/>
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
            <li <?php if($aba_selecionada == "imagem"){ echo "class='active'"; } ?> onClick="carrega_envio_imagens();" >
              <a href="#imagem" data-toggle="tab">Imagens</a>
            </li>
            <li <?php if($aba_selecionada == "audios"){ echo "class='active'"; } ?> >
              <a href="#audios" data-toggle="tab">Audios</a>
            </li>

  				</ul>

  				<div class="tab-content" >

  					<div id="dados" class="tab-pane <?php if($aba_selecionada == "dados"){ echo "active"; } ?>" >
  					<form action="<?=$_base['objeto']?>alterar_grv" class="form-horizontal" method="post">  						

  						<fieldset>
  							
                <div class="form-group">
  								<label class="col-md-12" >Titulo</label>
  								<div class="col-md-6">
  									<input name="titulo" type="text" class="form-control" value="<?=$data->titulo?>" >
  								</div>
  							</div>

                <div class="form-group">
                  <label class="col-md-12">Autor</label>
                  <div class="col-md-6">
                    <select name="autor" class="form-control select2" style="width: 100%;" >
                    <option value="" selected="" >Nenhum</option>
                    <?php
                      foreach ($autores as $key => $value) {
                        
                        echo "<option value='".$value['codigo']."' ".$value['selected'].">".$value['nome']."</option>";
                        
                      }
                    ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-12">Grupo/Categoria</label>
                  <div class="col-md-6">
                    <select name="grupo" class="form-control select2" style="width: 100%;" >
                    <?php
                      foreach ($categorias as $key => $value) {
                        
                        echo "<option value='".$value['codigo']."' ".$value['selected'].">".$value['titulo']."</option>";
                        
                      }
                    ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-12">Destaque</label>
                  <div class="col-md-6">
                    <select name="destaque" class="form-control select2" style="width: 100%;" >
                    <option value="">Nenhum</option>
                    <?php
                      foreach ($destaques as $key => $value) {
                        
                        echo "<option value='".$value['codigo']."' ".$value['selected']." >".$value['titulo']."</option>";
                        
                      }
                    ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  
                  <label class="col-md-12">Data/Hora</label>

                  <div class="col-md-2">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </span>
                      <input type="text" class="form-control datepicker" value="<?=$dia?>/<?=$mes?>/<?=$ano?>" name="dia" >
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="bootstrap-timepicker"> 
                        <div class="input-group">
                          <input type="text" class="form-control timepicker" name="hora" value="<?=$hora?>:<?=$minuto?>">
                          <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                          </div>
                        </div> 
                    </div>
                  </div>

                </div>

                <div class="form-group">
                  <label class="col-md-12">Prévia</label>
                  <div class="col-md-6">
                    <textarea class="form-control" name="previa" rows="5" ><?=$data->previa?></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-12">Conteúdo</label>
                  <div class="col-md-12">
                    <textarea class="ckeditor" id="conteudo" name="conteudo" rows="10" ><?=$data->conteudo?></textarea>
                  </div>
                </div>

  						</fieldset>

              <div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <input type="hidden" name="codigo" value="<?=$data->codigo?>" >
                <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial/grupo/<?=$data->categoria?>';" >Voltar</button>
  						</div>
  						
  				  </form>
  				  </div>


           <div id="imagem" class="tab-pane <?php if($aba_selecionada == 'imagem'){ echo 'active'; } ?>">

  <!-- AVISO PARA O USUÁRIO -->
  <div class="alert alert-warning" style="text-align:center; font-weight:bold;">
    ⚠️ Atenção: envie apenas imagens no formato <strong>PNG</strong>.
  </div>

  <button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>upload/codigo/<?=$data->codigo?>', 'Enviar Imagens');" >
    Carregar Imagens
  </button>

  <hr>

  <?php if(!empty($imagens)): ?>
    <div class="row">
      <?php foreach($imagens as $img): ?>
        <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom:20px; text-align:center;">
          <div class="foto-thumb" style="border:1px solid #ddd; padding:5px;">
            <img src="<?=$img['imagem_p']?>" alt="<?=htmlspecialchars($img['legenda'])?>" style="max-width:100%; border-radius:4px;" />
            <p style="margin-top:8px; font-size:14px; color:#555;"><?=htmlspecialchars($img['legenda'])?></p>
            <div style="margin-top:5px;">
              <button class="btn btn-default fa fa-file-text-o"
                onClick="modal('<?=$_base['objeto']?>legenda/id/<?=$img['id']?>/codigo/<?=$data->codigo?>','Alterar Legenda');"
                title="Editar legenda"></button>
              <button class="btn btn-danger fa fa-times-circle"
                onClick="confirma_apagar('<?=$_base['objeto']?>apagar_imagem/codigo/<?=$data->codigo?>/id/<?=$img['id']?>');"
                title="Remover imagem"></button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div style="text-align:center; padding:50px 0; font-size:16px; color:#777;">
      Nenhuma imagem cadastrada.
    </div>
  <?php endif; ?>

  <div style="margin-top:20px;">
    <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial/grupo/<?=$data->categoria?>';">
      Voltar
    </button>
  </div>

</div>


            <div id="audios" class="tab-pane <?php if($aba_selecionada == "audios"){ echo "active"; } ?>" >

              <div>
              <form action="<?=$_base['objeto']?>novo_audio/codigo/<?=$data->codigo?>" method="post" enctype="multipart/form-data">
              
                <fieldset>

                  <div class="form-group">
                    <label class="col-md-12" >Titulo</label>
                    <div class="col-md-4">
                      <input name="titulo" type="text" class="form-control" >
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label class="col-md-12">Arquivo</label>
                    <div class="col-md-8">

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

                      <div style="text-align:left; padding-top:10px;">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                      </div>

                    </div>
                  </div>
                  
                </fieldset>
                
              </form>
              </div>

              <hr>

              <?php
                
                foreach ($audios as $key => $value) {
                  
                  echo "
                  <div style='padding-left:15px;'>
                    <a href='#' onClick=\"confirma('".$_base['objeto']."apagar_audio/id/".$value['id']."/codigo/$data->codigo');\" style='color:#666; font-size:16px; padding-right:10px;' ><i class='fa fa-trash' aria-hidden='true'></i></a>
                    <a href='".$value['arquivo']."' target='_blank' style='color:#666; font-size:16px; ' > ".$value['titulo']."</a></div>
                  <hr>
                  ";
                  
                }
                
              ?>
              
              <div>
                <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial/grupo/<?=$data->categoria?>';" >Voltar</button>
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
<script src="<?=LAYOUT?>ckeditor412/ckeditor.js"></script>
<script src="<?=LAYOUT?>api/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script> 
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script>
$(document).ready(function() {

      $(".select2").select2();

      //Date picker
      $('.datepicker').datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy'
      });

      //Timepicker
      $('.timepicker').timepicker({
        showInputs: false,
        showMeridian: false
      });

      $( "#sortable_imagem" ).sortable({
        update: function(event, ui){
          var postData = $(this).sortable('serialize');
          console.log(postData);
          
          $.post('<?=$_base['objeto']?>ordenar_imagem', {list: postData, codigo: '<?=$data->codigo?>'}, function(o){
            console.log(o);
          }, 'json');
        }
      });      

});
</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>

</body>
</html>