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
<link rel="stylesheet" href="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>plugins/select2/select2.min.css">
<link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
<link rel="stylesheet" href="<?=LAYOUT?>css/css.css">
<style>
.sortable-item{cursor:move;transition:all 0.3s ease;}
.sortable-item:hover{background:#f8f9fa;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.ui-sortable-helper{background:#fff;box-shadow:0 8px 25px rgba(0,0,0,0.2);transform:rotate(2deg);}
.ui-sortable-placeholder{background:#e3f2fd;border:2px dashed #2196f3;height:60px;}
.table-modern{border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);}
.btn-gradient{background:linear-gradient(45deg,#667eea 0%,#764ba2 100%);border:none;color:#fff;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;transition:all 0.3s ease;}
.btn-gradient:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(102,126,234,0.4);color:#fff;}
.btn-danger-gradient{background:linear-gradient(45deg,#f093fb 0%,#f5576c 100%);}
.header-modern{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:20px;margin-bottom:30px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);}
</style>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body class="hold-transition skin-blue <?php if($_base['menu_fechado']==1){echo"sidebar-collapse";}?> sidebar-mini">
<div class="wrapper">
<?php require_once('htm_topo.php');require_once('htm_menu.php');?>
<div class="content-wrapper">
<section class="content-header header-modern">
<h1><?=$_titulo?><small><?=$_subtitulo?></small></h1>
</section>
<section class="content">
<div class="row">
<div class="col-xs-12">
<form action="<?=$_base['objeto']?>apagar_varios" method="post" id="form_apagar">
<div class="box table-modern">
<div class="box-body">
<?php if($permissao){?>
<div style="text-align:left;margin-bottom:20px;">
<button type="button" class="btn btn-gradient" onClick="window.location='<?=$_base['objeto']?>novo';"><i class="fa fa-plus"></i> Nova</button>
<button type="button" class="btn btn-gradient btn-danger-gradient" onClick="apagar_varios('form_apagar');"><i class="fa fa-trash"></i> Apagar Selecionados</button>
</div>
<hr>
<?php }?>
<table class="table table-bordered table-striped">
<thead style="background:linear-gradient(45deg,#667eea,#764ba2);color:#fff;">
<tr><th style="width:40px;text-align:center;">Ord.</th><th style="width:40px;text-align:center;">Sel.</th><th>Título</th><th style="width:80px;text-align:center;">Ações</th></tr>
</thead>
<tbody id="sortable">
<?php
$n=0;
foreach($lista as $value){
$linklinha="onClick=\"window.location='".$_base['objeto']."alterar/codigo/".$value['codigo']."';\" style='cursor:pointer;'";
echo "<tr class='sortable-item' data-id='".$value['id']."'>
<td style='width:40px;text-align:center;cursor:move;'><i class='fa fa-arrows-v text-primary'></i></td>
<td style='width:40px;text-align:center;'><input type='checkbox' class='marcar' name='apagar_".$value['id']."' value='1'></td>
<td $linklinha>".$value['titulo']."</td>
<td style='width:80px;text-align:center;'>
<button type='button' class='btn btn-xs btn-primary' onClick=\"window.location='".$_base['objeto']."alterar/codigo/".$value['codigo']."';\"><i class='fa fa-edit'></i></button>
</td>
</tr>";
$n++;
}
if($n==0){echo "<tr><td colspan='4' style='padding:40px;text-align:center;color:#999;'>Nenhum Resultado</td></tr>";}
?>
</tbody>
</table>
</div>
</div>
</form>
</div>
</div>
</section>
</div>
<?php require_once('htm_rodape.php');?>
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
$(function(){
$(".select2").select2();
$('input').iCheck({checkboxClass:'icheckbox_square-blue',radioClass:'iradio_square-blue'});
$("#sortable").sortable({
items:".sortable-item",
handle:"td:first-child",
placeholder:"ui-sortable-placeholder",
tolerance:"pointer",
cursor:"move",
opacity:0.8,
update:function(e,ui){
var ordem=[];
$("#sortable .sortable-item").each(function(){
ordem.push($(this).data('id'));
});
$.post('<?=$_base['objeto']?>ordem',{ids:ordem.join(',')},function(r){
console.log('Ordem salva:',r);
},'json').fail(function(){console.log('Erro ao salvar ordem');});
}
});
});
function dominio(){return '<?=DOMINIO?>';}
</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
</body>
</html>