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
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.css" />
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

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

			<ul class="nav nav-tabs ">
				
				<?php if($acesso_emails){ ?>
				<li <?php if($aba_selecionada == "emails"){ echo "class='active'"; } ?> >
					<a href="#emails" data-toggle="tab">E-mails do Contato</a>
				</li>
				<?php } ?>

				<?php if($acesso_meta){ ?>
				<li <?php if($aba_selecionada == "meta"){ echo "class='active'"; } ?> >
					<a href="#meta" data-toggle="tab">Meta</a>
				</li>
				<?php } ?>

				<?php if($acesso_smtp){ ?>
				<li <?php if($aba_selecionada == "smtp"){ echo "class='active'"; } ?> >
					<a href="#smtp" data-toggle="tab">Configuração de envio (smtp)</a>
				</li>
				<?php } ?>

				<?php if($acesso_logo){ ?>
				<li <?php if($aba_selecionada == "imagem"){ echo "class='active'"; } ?> >
					<a href="#imagem" data-toggle="tab">Imagem da Organização</a>
				</li>
				<?php } ?>

			</ul>

			<div class="tab-content" >
  				
  				<?php if($acesso_emails){ ?>
  				<div id="emails" <?php if($aba_selecionada == "emails"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
                <form action="<?=$_base['objeto']?>apagar_emails" method="post" id="form_apagar" name="form_apagar" >

  						<div>

							<button type="button" class="btn btn-primary" onClick="modal('<?=$_base['objeto']?>novo_email', 'Alterar E-mail');" >Novo</button>
							
					        <button type="button" class="btn btn-default" onClick="apagar_varios('form_apagar');" >Apagar Selecionados</button>

				        </div>

				        <hr>

						<table class="table table-bordered table-striped">

		                	<thead>
								<tr>
				                	<th style='width:30px; text-align:center;' >Sel.</th>
				                    <th>Nome</th>
				                    <th>Email</th>
				                    <th>Codigo</th>
				                </tr>
							</thead>
							
				            <tbody>
							<?php
								
								foreach ($contatos as $key => $value) {

									$linklinha = "onClick=\"modal('".$_base['objeto']."alterar_email/codigo/".$value['id']."', 'Alterar E-mail');\" style='cursor:pointer;' ";
									
									echo "
									<tr>
										<td class='center' style='width:30px;' ><input class='flat-red' type='checkbox' class='marcar' name='apagar_".$value['id']."' value='1' ></td>
										<td $linklinha >".$value['titulo']."</td>
										<td $linklinha >".$value['email']."</td>
										<td >".$value['codigo']."</td>
									</tr>
									";
								}

							?>
				            </tbody>

		                </table>

		        </form>
				</div>
				<?php } ?>


  				<?php if($acesso_meta){ ?>
  				<div id="meta" <?php if($aba_selecionada == "meta"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
                <form action="<?=$_base['objeto']?>meta_grv" class="form-horizontal" method="post">
                		
                        <fieldset>
                        	
							<div class="form-group">
								<label class="col-md-12" >Titulo Página</label>
								<div class="col-md-7">
									<input name="titulo_pagina" type="text" class="form-control" value="<?=$data_meta->titulo_pagina?>" >
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-12" >Descrição</label>
								<div class="col-md-7">
									<input name="descricao" type="text" class="form-control" value="<?=$data_meta->descricao?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >Meta Descrição</label>
								<div class="col-md-7">
									<input name="keywords1" type="text" class="form-control" value="<?=$data_meta->keywords1?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >Capa Social</label>
								<div class="col-md-7">
									<input name="keywords2" type="text" class="form-control" value="<?=$data_meta->keywords2?>" >
								</div>
							</div>
							
						</fieldset>
						
                    	<div>
                    		<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
                        
				</form>
				</div>
				<?php } ?>

				<?php if($acesso_smtp){ ?>
				<div id="smtp" <?php if($aba_selecionada == "smtp"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
                <form action="<?=$_base['objeto']?>smtp_grv" class="form-horizontal" method="post">
                		
                        <fieldset>

							<div class="form-group">
								<label class="col-md-12" >Nome de Exibição</label>
								<div class="col-md-7">
									<input name="email_nome" type="text" class="form-control" value="<?=$data->email_nome?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >E-mail de Origem</label>
								<div class="col-md-7">
									<input name="email_origem" type="text" class="form-control" value="<?=$data->email_origem?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >E-mail de Retorno</label>
								<div class="col-md-7">
									<input name="email_retorno" type="text" class="form-control" value="<?=$data->email_retorno?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >Porta</label>
								<div class="col-md-7">
									<input name="email_porta" type="text" class="form-control" value="<?=$data->email_porta?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >E-mail - Host</label>
								<div class="col-md-7">
									<input name="email_host" type="text" class="form-control" value="<?=$data->email_host?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >E-mail - Usuário</label>
								<div class="col-md-7">
									<input name="email_usuario" type="text" class="form-control" value="<?=$data->email_usuario?>" >
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-12" >E-mail - Senha</label>
								<div class="col-md-7">
									<input name="email_senha" type="text" class="form-control" value="<?=$data->email_senha?>" >
								</div>
							</div>

						</fieldset>
						
                    	<div>
                    		<button type="submit" class="btn btn-primary">Salvar</button>
						</div>
                        
				</form>
				</div>
				<?php } ?>

				<?php if($acesso_logo){ ?>
				<div id="imagem" <?php if($aba_selecionada == "imagem"){ echo "class='tab-pane active'"; } else { echo "class='tab-pane'"; } ?> >
					
					<?php if($data->logo){ ?>
                        	
                            <div><img src="<?=PASTA_CLIENTE?>img_logo/<?=$data->logo?>" style="border:0px; max-width:300px;" ></div>
                            
                            <div style="padding-top:10px">
								<button type="button" class="btn btn-primary" onClick="confirma('<?=$_base['objeto']?>apagar_logo');"  >Apagar Imagem</button>
                            </div>
                        
					<?php } else { ?>
                        
                        <form action="<?=$_base['objeto']?>logo" class="form-horizontal" method="post" enctype="multipart/form-data" >
                        
                        <fieldset>
                        <div class="form-group">
							<label class="col-md-12">Arquivo</label>
							<div class="col-md-7">
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
							</div>
						</div>
                        </fieldset>
						
                        <div>
							<button type="submit" class="btn btn-primary">Enviar</button>
						</div>
                        
						</form>
					
                    <?php } ?>
                    
				</div>
				<?php } ?>
                
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
<script src="<?=LAYOUT?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?=LAYOUT?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?=LAYOUT?>plugins/fastclick/fastclick.js"></script>
<script src="<?=LAYOUT?>dist/js/app.min.js"></script>
<script src="<?=LAYOUT?>dist/js/demo.js"></script>
<script src="<?=LAYOUT?>api/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script>function dominio(){ return '<?=DOMINIO?>'; }</script>
<script src="<?=LAYOUT?>js/funcoes.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue'
    });
  });
</script>
</body>
</html>