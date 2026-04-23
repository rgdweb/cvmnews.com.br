<?php if(!isset($_base['libera_views'])){ header("HTTP/1.0 404 Not Found"); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Galeria de Fotos - <?=$_base['titulo_pagina']?></title>
  <link href="<?=LAYOUT?>css/bootstrap.css" rel="stylesheet" />
  <link href="<?=LAYOUT?>css/theme.css" rel="stylesheet" />
  <style>
    body { background:#f5f5f5; }
    .galeria-container {
      max-width: 1080px;
      margin: 40px auto;
      padding: 0 15px;
    }
    h1 {
      text-align: center;
      margin-bottom: 40px;
      color: #007bff;
      font-weight: 700;
      font-family: Arial, sans-serif;
    }
    .grid-fotos {
      display: grid;
      grid-template-columns: repeat(auto-fill,minmax(250px,1fr));
      gap: 20px;
    }
    .foto-item {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      background: #fff;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .foto-item:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,123,255,0.4);
    }
    .foto-item img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      display: block;
    }
    .foto-legenda {
      padding: 10px 15px;
      font-size: 14px;
      color: #333;
      font-family: Arial, sans-serif;
      text-align: center;
      min-height: 40px;
    }
  </style>
</head>
<body>

  <div class="galeria-container">
    <h1>Galeria de Fotos</h1>

    <?php if (!empty($fotos)): ?>
      <div class="grid-fotos" role="list">
        <?php foreach($fotos as $foto): ?>
          <div class="foto-item" role="listitem" tabindex="0" aria-label="Foto <?=$foto['codigo']?>">
            <img src="<?=DOMINIO?>uploads/galeria/<?=htmlspecialchars($foto['imagem'])?>" alt="Foto <?=$foto['codigo']?>" loading="lazy" />
            <div class="foto-legenda"><?= isset($foto['legenda']) ? htmlspecialchars($foto['legenda']) : 'Foto '.$foto['codigo'] ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Nenhuma foto disponível no momento.</p>
    <?php endif; ?>

  </div>

</body>
</html>
