<?php if(!isset($pagina_titulo)) $pagina_titulo = 'Álbum'; ?>
<?php if(!isset($fotos)) $fotos = []; ?>

<div class="container" style="padding:50px 0;">
  <h2><?=htmlspecialchars($pagina_titulo)?></h2>

  <div class="row">
    <?php if(count($fotos)): ?>
      <?php foreach($fotos as $foto): ?>
        <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom:20px;">
          <a href="<?=htmlspecialchars($foto['imagem'])?>" target="_blank">
            <img src="<?=htmlspecialchars($foto['imagem'])?>" style="width:100%; border:1px solid #ccc; border-radius:4px;">
          </a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nenhuma foto encontrada neste álbum.</p>
    <?php endif; ?>
  </div>
</div>
