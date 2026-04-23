<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Entrar - <?=TITULO_VIEW?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?=LAYOUT?>bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>font-awesome-4.6.2/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?=LAYOUT?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="<?=LAYOUT?>css/css.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box" style="padding-top:95px; text-align: center;">
    <div class="login-logo" style="text-align: center;">
      <img class="img-responsive" src="<?=$_base['logo']?>" style="max-height:100px;" >
    </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"></p>

    <form action="<?=DOMINIO?>autenticacao/login" method="post">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Usuário" name="usuario">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Senha" name="senha">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div style="padding-top:5px;"><a href="<?=DOMINIO?>recuperar">Esqueci minha senha!</a></div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<script src="<?=LAYOUT?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="<?=LAYOUT?>bootstrap/js/bootstrap.min.js"></script>
<script src="<?=LAYOUT?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
