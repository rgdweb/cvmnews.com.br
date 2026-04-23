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
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/select2/select2.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css">

  <style>
    /* ===== LINHAS DA TABELA ===== */
    .table-hover-effect tbody tr:hover {
      background-color: #f0f8ff; /* azul claro */
      transition: background-color 0.3s ease;
    }

    /* ===== COLUNA ORDEM ===== */
    .table td:first-child i.fa-arrows-v {
      color: #888;
      transition: transform 0.2s ease, color 0.2s ease;
    }
    .table td:first-child i.fa-arrows-v:hover {
      color: #337ab7;
      transform: scale(1.3);
      cursor: move;
    }

    /* ===== BOTÕES DE AÇÃO ===== */
    .btn-xs.btn-primary, .btn-xs.btn-danger {
      transition: all 0.2s ease;
    }
    .btn-xs.btn-primary:hover {
      background-color: #286090;
      transform: scale(1.1);
    }
    .btn-xs.btn-danger:hover {
      background-color: #c9302c;
      transform: scale(1.1);
    }
    .btn-xs:active {
      transform: scale(0.95);
    }

    /* ===== SELECT2 ===== */
    .select2-container--default .select2-selection--single {
      border-radius: 4px;
      border-color: #3c8dbc;
      transition: box-shadow 0.3s ease;
    }
    .select2-container--default .select2-selection--single:focus {
      box-shadow: 0 0 5px rgba(60,141,188,0.5);
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

              <div style="margin-bottom:15px;">
                <button type="button" class="btn btn-primary" onClick="window.location='<?=$_base['objeto']?>novo/grupo/<?=$grupo?>';">
                  <i class="fa fa-plus"></i> Novo
                </button>
                <button type="button" class="btn btn-default" onClick="apagar_varios('form_apagar');">
                  <i class="fa fa-trash"></i> Apagar Selecionados
                </button>
                <button type="button" class="btn btn-default" onClick="window.location='<?=$_base['objeto']?>grupos';">
                  <i class="fa fa-folder-open"></i> Grupos
                </button>
              </div>

              <div style="margin-bottom:15px;">
                <select data-plugin-selectTwo class="form-control select2" name="grupo" 
                        onChange="window.location='<?=$_base['objeto']?>inicial/grupo/'+this.value;">
                  <?php
                    function montaCategorias($lista,$prefixo){
                      $retorno = '';
                      foreach($lista as $key => $value){
                        if(isset($value['titulo'])){
                          $retorno .= "<option value='".$value['codigo']."' ".$value['selected']." >$prefixo".$value['titulo']."</option>";
                          $retorno .= montaCategorias($value['filhos'], $prefixo.$value['titulo'].' >> ');
                        }
                      }
                      return $retorno;
                    }
                    echo montaCategorias($lista_grupos, '');
                  ?>
                </select>
              </div>

              <table class="table table-bordered table-striped table-hover-effect">
                <thead>
                  <tr>
                    <th style="width:30px;">Ord.</th>
                    <th style="width:30px;">Sel.</th>
                    <th>Título</th>
                    <th style="width:90px; text-align:center;">Ações</th>
                  </tr>
                </thead>
                <tbody id="sortable">
                  <?php foreach ($lista as $key => $value): ?>
                    <tr id="item_<?=$value['id']?>">
                      <td style="width:30px; cursor:move; text-align:center;">
                        <i class="fa fa-arrows-v"></i>
                      </td>
                      <td style="width:30px;">
                        <input type="checkbox" class="marcar" name="apagar_<?=$value['id']?>" value="1">
                      </td>
                      <td onClick="window.location='<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>';" style="cursor:pointer;">
                        <?=$value['titulo']?>
                      </td>
                      <td style="text-align:center;">
                        <a href="javascript:void(0);" class="btn btn-xs btn-primary"
                           onClick="window.location='<?=$_base['objeto']?>alterar/codigo/<?=$value['codigo']?>';"
                           title="Editar">
                          <i class="fa fa-pencil"></i>
                        </a>
                        <a href="<?=$_base['objeto']?>apagar/codigo/<?=$value['codigo']?>"
                           class="btn btn-xs btn-danger"
                           title="Excluir"
                           onclick="return confirm('Tem certeza que deseja excluir este item?');">
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
<script>
  $(function () {
    $(".select2").select2();
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue'
    });

    $("#sortable").sortable({
      update: function(event, ui){
        var postData = $(this).sortable('serialize');
        $.post('<?=$_base['objeto']?>ordem', { list: postData, grupo:'<?=$grupo?>' }, function(o){
          console.log(o);
        }, 'json');
      }
    });
  });
</script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
</body>
</html>
