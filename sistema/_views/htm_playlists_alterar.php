<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title><?=$_titulo?> - <?=TITULO_VIEW?></title>
  <link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css" />
</head>
<body class="hold-transition skin-blue <?php if($_base['menu_fechado']==1) echo 'sidebar-collapse'; ?> sidebar-mini">

<?php require_once('htm_topo.php'); ?>
<?php require_once('htm_menu.php'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Alterar Playlist</h1>
  </section>

  <section class="content">
    <form action="<?=$_base['objeto']?>alterar_grv" method="post" class="form-horizontal">

      <input type="hidden" name="id" value="<?=htmlspecialchars($data->id)?>" />

      <div class="form-group">
        <label class="col-sm-2 control-label">Nome da Playlist <span style="color:red;">*</span></label>
        <div class="col-sm-6">
          <input type="text" name="nome" class="form-control" maxlength="255" required value="<?=htmlspecialchars($data->nome)?>" />
        </div>
      </div>
      
      <div class="form-group">
        <label class="col-sm-2 control-label">Descrição</label>
        <div class="col-sm-8">
          <textarea name="descricao" rows="5" class="form-control"><?=htmlspecialchars($data->descricao)?></textarea>
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <button type="button" onclick="window.location='<?=$_base['objeto']?>inicial';" class="btn btn-default">Cancelar</button>
        </div>
      </div>

    </form>
  </section>

</div>

<?php require_once('htm_rodape.php'); ?>

<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
