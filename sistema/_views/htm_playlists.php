<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title><?=$_titulo?> - <?=TITULO_VIEW?></title>
  <link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>font-awesome-4.6.2/css/font-awesome.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css" />
</head>
<body class="hold-transition skin-blue <?php if($_base['menu_fechado'] == 1) echo "sidebar-collapse"; ?> sidebar-mini">

<?php require_once('htm_topo.php'); ?>
<?php require_once('htm_menu.php'); ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1><?=$_titulo?></h1>
  </section>

  <section class="content">
    <form action="<?=$_base['objeto']?>apagar_varios" method="post" id="form_apagar">
      <div style="margin-bottom: 15px;">
        <button type="button" class="btn btn-primary" onclick="window.location='<?=$_base['objeto']?>novo'">
          <i class="fa fa-plus-circle"></i> Nova Playlist
        </button>
        <button type="submit" class="btn btn-danger">Apagar Selecionados</button>
      </div>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th style="width:40px;">Sel.</th>
            <th>Nome da Playlist</th>
            <th>Descrição</th>
            <th style="width:100px;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($lista)) foreach($lista as $pl): ?>
          <tr>
            <td><input type="checkbox" name="apagar_<?=$pl->id?>" value="1" /></td>
            <td><?=htmlspecialchars($pl->nome)?></td>
            <td><?=nl2br(htmlspecialchars($pl->descricao))?></td>
            <td>
              <a href="<?=$_base['objeto']?>alterar/id/<?=$pl->id?>" class="btn btn-xs btn-info" title="Editar">
                <i class="fa fa-pencil"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($lista)): ?>
          <tr><td colspan="4" class="text-center">Nenhuma playlist cadastrada.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </form>
  </section>
</div>

<?php require_once('htm_rodape.php'); ?>

<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
