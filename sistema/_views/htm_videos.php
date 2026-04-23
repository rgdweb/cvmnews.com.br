<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="icon" href="<?=FAVICON?>" type="image/x-icon"/>
<title><?=$_titulo?> - <?=TITULO_VIEW?></title>
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>font-awesome-4.6.2/css/font-awesome.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="<?=LAYOUT?>css/css.css">

<style>
body, .content-wrapper {
  background-color: #f4f4f4;
  font-family: 'Source Sans Pro', Arial, sans-serif;
  font-size: 14px;
}
.box {
  border-radius: 8px;
  overflow: hidden;
}
.box-body {
  padding: 20px;
}
.btn-group {
  margin-bottom: 15px;
  gap: 10px;
}

/* Botões */
.btn-primary {
  background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
  border: none;
}
.btn-default {
  background: #ff6b6b;
  color: #fff;
  border: none;
}
.btn-default:hover {
  background: #ff4c4c;
  color: #fff;
}

/* Tabela */
.table-modern thead tr {
  background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}
.table-modern tbody tr:hover {
  background-color: #f9f9f9;
}
.table-modern td, .table-modern th {
  vertical-align: middle !important;
}

/* Ordenação */
.sortable-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}
.ui-sortable-helper {
  transform: rotate(2deg);
  box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}
.ui-sortable-placeholder {
  background: #e3f2fd;
  border: 2px dashed #2196f3;
  height: 40px;
}

/* Checkboxes */
input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: #667eea;
}

/* Ação editar */
.btn-action {
  background: #3498db;
  border: none;
  border-radius: 3px;
  padding: 4px 8px;
  color: #fff;
}
.btn-action:hover {
  background: #217dbb;
}
</style>
</head>
<body class="hold-transition skin-blue <?php if($_base['menu_fechado'] == 1){ echo "sidebar-collapse"; } ?> sidebar-mini">
<div class="wrapper">
<?php require_once('htm_topo.php'); ?>
<?php require_once('htm_menu.php'); ?>

<div class="content-wrapper">
<section class="content-header">
  <h1><?=$_titulo?><small><?=$_subtitulo?></small></h1> 
</section>

<section class="content">
<div class="row">
<div class="col-xs-12">    		

<form action="<?=$_base['objeto']?>apagar_varios" method="post" id="form_apagar" name="form_apagar">
<div class="box">
  <div class="box-body">
    <div class="btn-group">
      <button type="button" class="btn btn-primary" onClick="window.location='<?=$_base['objeto']?>novo';">
        <i class="fa fa-plus"></i> Nova
      </button>
      <button type="button" class="btn btn-default" onClick="apagar_varios('form_apagar');">
        <i class="fa fa-trash"></i> Apagar Selecionados
      </button>
    </div>

    <table class="table table-bordered table-striped table-modern">
      <thead>
        <tr>
          <th style="width:40px;">Ord.</th>
          <th style="width:40px;">Sel.</th>
          <th>Título</th>
          <th style="width:60px;">Ações</th>
        </tr>
      </thead>
      <tbody id="sortable">
        <?php foreach ($lista as $key => $value): ?>
          <tr class="sortable-item" data-id="<?=$value['id']?>">
            <td style="text-align:center; cursor:move;"><i class="fa fa-arrows-v"></i></td>
            <td><input type="checkbox" class="marcar" name="apagar_<?=$value['id']?>" value="1"></td>
            <td><?=$value['titulo']?></td>
            <td style="text-align:center;">
              <button type="button" class="btn btn-action" onClick="window.location='<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>';">
                <i class="fa fa-pencil"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="margin-top:15px;">
      <button type="button" class="btn btn-success" id="salvarOrdem"><i class="fa fa-save"></i> Salvar Ordem</button>
    </div>

  </div>
</div>
</form>

</div>
</div>
</section>
</div>

<?php require_once('htm_rodape.php'); ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>

<script>
$(function(){
  $('input').iCheck({
    checkboxClass:'icheckbox_square-blue',
    radioClass:'iradio_square-blue'
  });

  $("#sortable").sortable({
    items: ".sortable-item",
    axis: "y",
    cursor: "move",
    placeholder: "ui-sortable-placeholder"
  });

  $("#salvarOrdem").click(function(){
    var ids = [];
    $("#sortable .sortable-item").each(function(){
      ids.push($(this).data('id'));
    });
    $.post('<?=$_base['objeto']?>ordem', { ordem: ids.join(',') }, function(resp){
      console.log(resp);
      alert("Ordem salva com sucesso!");
    }).fail(function(){
      alert("Erro ao salvar ordem.");
    });
  });
});
</script>

</body>
</html>
