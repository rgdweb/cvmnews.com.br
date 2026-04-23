<?php if(!isset($pagina_titulo)) $pagina_titulo = 'Álbuns'; ?>
<?php if(!isset($albuns)) $albuns = []; ?>

<div class="container" style="padding:50px 0;">
  <h2 class="titulo_padrao" style="text-align:center;margin-bottom:40px;">
    <?= htmlspecialchars($pagina_titulo) ?>
  </h2>

  <div class="row">
    <?php if(count($albuns)): ?>
      <?php foreach($albuns as $album): ?>
        <div class="col-md-4 col-sm-6" style="margin-bottom:30px;">
          <a href="<?=DOMINIO?>galeria/ver_album/<?=htmlspecialchars($album['codigo'])?>">
            <div style="border:1px solid #ddd; padding:5px; border-radius:5px;">
              <img src="<?=htmlspecialchars($album['capa'])?>" alt="<?=htmlspecialchars($album['titulo'])?>" style="width:100%; height:200px; object-fit:cover;">
              <div style="text-align:center; font-weight:bold; padding:10px;">
                <?=htmlspecialchars($album['titulo'])?>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center">Nenhum álbum encontrado.</p>
    <?php endif; ?>
  </div>
</div>
