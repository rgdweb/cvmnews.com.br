<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>

<?php
// Tratamento da reordenação das imagens (direto no próprio arquivo)
if (isset($_POST['list']) && isset($_POST['codigo'])) {
    // Ajuste conforme sua estrutura de model
    require_once '../../models/imagens_model.php'; // Substitua pelo caminho correto
    
    $model = new imagens_model(); // Certifique-se de ter esse model

    parse_str($_POST['list'], $items);

    if (!empty($items['item'])) {
        $ordem = 0;
        foreach ($items['item'] as $id) {
            $model->atualizarOrdem($id, $ordem);
            $ordem++;
        }
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'erro', 'msg' => 'Nenhuma imagem encontrada para ordenar.']);
    }

    exit;
}
?>

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
              <li <?php if($aba_selecionada == "dados"){ echo "class='active'"; } ?>>
                <a href="#dados" data-toggle="tab">Dados</a>
              </li>
              <li <?php if($aba_selecionada == "imagem"){ echo "class='active'"; } ?> onClick="carrega_envio_imagens();">
                <a href="#imagem" data-toggle="tab">Imagens</a>
              </li>
            </ul>

            <div class="tab-content">
              <!-- Aba Dados -->
              <div id="dados" class="tab-pane <?php if($aba_selecionada == "dados"){ echo "active"; } ?>">
                <form action="<?=$_base['objeto']?>alterar_grv" class="form-horizontal" method="post">
                  <fieldset>
                    <div class="form-group">
                      <label class="col-md-12">Título</label>
                      <div class="col-md-6">
                        <input name="titulo" type="text" class="form-control" value="<?=$data->titulo?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-12">Conteúdo</label>
                      <div class="col-md-12">
                        <textarea class="ckeditor" id="conteudo" name="conteudo" rows="10"><?=$data->conteudo?></textarea>
                      </div>
                    </div>
                  </fieldset>

                  <div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <input type="hidden" name="codigo" value="<?=$data->codigo?>">
                    <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial/grupo/<?=$data->grupo?>';">Voltar</button>
                  </div>
                </form>
              </div>

              <!-- Aba Imagens -->
              <div id="imagem" class="tab-pane <?php if($aba_selecionada == "imagem"){ echo "active"; } ?>">
                <button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>upload/codigo/<?=$data->codigo?>', 'Enviar Imagens');">Carregar Imagens</button>
                <hr>
                <div style="text-align:center;">
                  <ul id="sortable_imagem">
                    <?php
                    $n = 0;
                    foreach ($imagens as $key => $value) {
                      echo "
                        <li id='item_".$value['id']."'>
                          <div class='quadro_img' style='background-image:url(".$value['imagem_p'].");'></div>
                          <div style='padding-top:5px; text-align:center;'>
                            <button class='btn btn-default fa fa-file-text-o' onClick=\"modal('".$_base['objeto']."legenda/id/".$value['id']."/codigo/$data->codigo', 'Alterar Legenda');\" title='Editar legenda'></button>
                            <button class='btn btn-default fa fa-times-circle' onClick=\"confirma_apagar('".$_base['objeto']."apagar_imagem/codigo/$data->codigo/id/".$value['id']."');\" title='Remover imagem'></button>
                          </div>
                        </li>
                      ";
                      $n++;
                    }
                    ?>
                  </ul>
                </div>
                <?php if($n == 0): ?>
                  <div style="text-align:center; padding-top:100px; padding-bottom:100px;">Nenhuma imagem adicionada!</div>
                <?php endif; ?>
                <div>
                  <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>inicial/grupo/<?=$data->grupo?>';">Voltar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php require_once('htm_rodape.php'); ?>
</div>

<!-- JS -->
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

    $("#sortable_imagem").sortable({
        update: function(event, ui) {
            var serializedData = $(this).sortable("serialize");

            $.ajax({
                url: '', // Mesmo arquivo
                type: 'POST',
                data: {
                    list: serializedData,
                    codigo: '<?=$data->codigo?>'
                },
                success: function(response) {
                    console.log("Ordem salva", response);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao salvar ordem:", error);
                }
            });
        }
    });
});
</script>

<script>function dominio(){ return '<?=DOMINIO?>'; }</script>

</body>
</html>