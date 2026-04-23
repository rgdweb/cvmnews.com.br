<table style="width:100%">

  <tr style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;">
    <td class="prog_td_titulo" >INÍCIO</td>
    <td class="prog_td_titulo" >PROGRAMA</td>
    <td class="prog_td_titulo" >APRESENTADOR</td>
  </tr>

  <?php

  foreach ($lista_dia as $key => $value) {

    echo "
    <tr class='prog_linha' >
    <td class='prog_td_linha' style='text-align:center;' >".$value['inicio']."</td>
    <td class='prog_td_linha' >".$value['titulo']."</td>
    <td class='prog_td_linha' >".$value['apresentador']."</td>
    </tr>
    ";

  }

  ?>
</table>