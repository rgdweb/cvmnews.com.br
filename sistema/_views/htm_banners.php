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
.btn-group, .box-body > div:first-child {
  margin-bottom: 15px;
  gap: 10px;
}

/* Botões gerais */
.btn-primary {
  background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
  border: none;
}
.btn-primary:hover { opacity: 0.9; }
.btn-default {
  background: #ff6b6b;
  color: #fff;
  border: none;
}
.btn-default:hover { background: #ff4c4c; color: #fff; }

/* Tabela moderna */
.table-modern thead tr {
  background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
  color: #fff;
}
.table-modern td, .table-modern th { vertical-align: middle !important; }
.table-modern tbody tr:hover {
  background-color: #f9f9f9;
  transition: background 0.3s ease;
}

/* Botão de ação dentro da tabela */
.btn-action {
  background: #3498db;
  border: none;
  border-radius: 3px;
  padding: 4px 8px;
  color: white;
  display: inline-block;
  margin: 0 2px;
}
.btn-action:hover {
  background: #2980b9;
  color: #fff;
}

/* Efeitos de sortable */
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

/* Checkbox custom */
input[type="checkbox"] {
  width: 16px;
  height: 16px;
  accent-color: #667eea;
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

                <div style="text-align:left;">
                  <button type="button" class="btn btn-primary" onClick="window.location='<?=$_base['objeto']?>novo/grupo/<?=$grupo?>';">
                    <i class="fa fa-plus"></i> Novo
                  </button>
                  <button type="button" class="btn btn-default" onClick="apagar_varios('form_apagar');">
                    <i class="fa fa-trash"></i> Apagar Selecionados
                  </button>
                  <?php if($acesso_grupos){ ?>
                    <button type="button" class="btn btn-primary" style="background:#3498db;" onClick="window.location='<?=$_base['objeto']?>grupos';">
                      <i class="fa fa-folder-open"></i> Categorias
                    </button>
                  <?php } ?>
                </div>

                <div style="text-align:left; padding-top:15px;">
                  <select data-plugin-selectTwo class="form-control select2" name="grupo"
                    onChange="window.location='<?=$_base['objeto']?>inicial/grupo/'+this.value;">
                    <?php foreach ($categorias as $key => $value) {
                      echo "<option value='".$value['codigo']."' ".$value['selected'].">".$value['titulo']."</option>";
                    } ?>
                  </select>
                </div>

                <hr>

                <table class="table table-bordered table-striped table-modern">
                  <thead>
                    <tr>
                      <th style="width:40px;">Ord.</th>
                      <th style="width:40px;">Sel.</th>
                      <th>Título</th>
                      <th>Grupo/Categoria</th>
                      <th style="width:80px; text-align:center;">Ações</th>
                    </tr>
                  </thead>
                  <tbody id="sortable">
                    <?php foreach ($lista as $key => $value): ?>
                      <tr class="sortable-item" id="item_<?=$value['id']?>" data-id="<?=$value['id']?>">
                        <td style="width:30px; cursor:move; text-align:center;"><i class="fa fa-arrows-v"></i></td>
                        <td style="width:30px;"><input type="checkbox" class="marcar" name="apagar_<?=$value['id']?>" value="1"></td>
                        <td onClick="window.location='<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>';" style="cursor:pointer;"><?=$value['titulo']?></td>
                        <td onClick="window.location='<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>';" style="cursor:pointer;"><?=$value['categoria']?></td>
                        <td style="text-align:center;">
                          <a href="<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>" class="btn-action" title="Editar">
                            <i class="fa fa-pencil"></i>
                          </a>
                          <a href="<?=$_base['objeto']?>apagar/codigo/<?=$value['codigo']?>" class="btn-action" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este item?');">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

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
<script src="<?=LAYOUT?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>plugins/select2/select2.full.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script>
$(function(){
  $(".select2").select2();
  $('input').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue'
  });

  $("#sortable").sortable({
    update: function(event, ui){
      var ordem = $("#sortable").sortable("toArray");
      var nova_ordem = ordem.map(function(el){
        return el.replace("item_", "");
      }).join(",");

      $.post("<?=$_base['objeto']?>ordem", {
        grupo: "<?=$grupo?>",
        ordem: nova_ordem
      }, function(retorno){
        console.log("Ordem atualizada:", nova_ordem);
      });
    }
  });
});
</script>
</body>
</html>
