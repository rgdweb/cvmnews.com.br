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
	<link rel="stylesheet" href="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.css">
	<link rel="stylesheet" href="<?=LAYOUT?>plugins/timepicker/bootstrap-timepicker.min.css">
	<link rel="stylesheet" href="<?=LAYOUT?>plugins/select2/select2.min.css">
	<link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?=LAYOUT?>dist/css/skins/_all-skins.min.css">
	<link rel="stylesheet" href="<?=LAYOUT?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css"> 
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

			<section class="content">
				<div class="row">
					<div class="col-xs-12">

						<div style="padding-bottom: 20px;">
							<button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>novo', 'Novo');" >Novo</button>
						</div>

						<div class="nav-tabs-custom">

							<ul class="nav nav-tabs ">

								<li <?php if($aba_selecionada == "domingo"){ echo "class='active'"; } ?> >
									<a href="#domingo" data-toggle="tab">Domingo</a>
								</li>
								<li <?php if($aba_selecionada == "segunda"){ echo "class='active'"; } ?> >
									<a href="#segunda" data-toggle="tab">Segunda</a>
								</li>
								<li <?php if($aba_selecionada == "terca"){ echo "class='active'"; } ?> >
									<a href="#terca" data-toggle="tab">Terça</a>
								</li>
								<li <?php if($aba_selecionada == "quarta"){ echo "class='active'"; } ?> >
									<a href="#quarta" data-toggle="tab">Quarta</a>
								</li>
								<li <?php if($aba_selecionada == "quinta"){ echo "class='active'"; } ?> >
									<a href="#quinta" data-toggle="tab">Quinta</a>
								</li>
								<li <?php if($aba_selecionada == "sexta"){ echo "class='active'"; } ?> >
									<a href="#sexta" data-toggle="tab">Sexta</a>
								</li>
								<li <?php if($aba_selecionada == "sabado"){ echo "class='active'"; } ?> >
									<a href="#sabado" data-toggle="tab">Sábado</a>
								</li>

							</ul>
							
							<div class="tab-content" >
								
								
								<div id="domingo" <?php if($aba_selecionada == "domingo"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_0" class="table table-bordered table-striped">
										
										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php

											foreach ($domingo as $key => $value) {

												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";
												
												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="segunda" <?php if($aba_selecionada == "segunda"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_1" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php

											foreach ($segunda as $key => $value) {

												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";

												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="terca" <?php if($aba_selecionada == "terca"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_2" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php
											
											foreach ($terca as $key => $value) {

												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";

												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="quarta" <?php if($aba_selecionada == "quarta"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_3" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php
											
											foreach ($quarta as $key => $value) {

												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";

												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="quinta" <?php if($aba_selecionada == "quinta"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_4" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php
											
											foreach ($quinta as $key => $value) {
												
												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";
												
												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="sexta" <?php if($aba_selecionada == "sexta"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_5" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php
											
											foreach ($sexta as $key => $value) {
												
												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";
												
												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
								</div>


								<div id="sabado" <?php if($aba_selecionada == "sabado"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
									<table id="tabela_6" class="table table-bordered table-striped">

										<thead>
											<tr>
												<th>Início</th>
												<th>Programa</th>
												<th></th>
											</tr>
										</thead>

										<tbody>
											<?php
											
											foreach ($sabado as $key => $value) {
												
												$linklinha = "onClick=\"modal('".$_base['objeto']."alterar/codigo/".$value['codigo']."', 'Alterar');\" style='cursor:pointer;' ";
												
												echo "
												<tr>												
												<td $linklinha >".$value['inicio']."</td>
												<td $linklinha >".$value['programa']."</td>
												<td><a href='#' onclick=\"confirma('".$_base['objeto']."apagar/codigo/".$value['codigo']."')\" >Apagar</a></td>
												</tr>
												";

											}

											?>
										</tbody>

									</table>
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

		<!-- jQuery 2.2.3 -->
		<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
		<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
		<script src="<?=LAYOUT?>plugins/select2/select2.full.min.js"></script>
		<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.js"></script>
		<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
		<script src="<?=LAYOUT?>plugins/input-mask/jquery.inputmask.extensions.js"></script>
		<script src="<?=LAYOUT?>plugins/daterangepicker/daterangepicker.js"></script>
		<script src="<?=LAYOUT?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
		<script src="<?=LAYOUT?>plugins/datepicker/bootstrap-datepicker.js"></script>
		<script src="<?=LAYOUT?>plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
		<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
		<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
		<script src="<?=LAYOUT?>api/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
		<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
		<script src="<?=LAYOUT?>dist/js/demo.js"></script> 
		<script src="<?=LAYOUT?>js/funcoes.js"></script>
		<script>function dominio(){ return '<?=DOMINIO?>'; }</script>

	</body>
	</html>