<?php

class cidades extends controller {	
	
	public function lista($estado){

		$lista = array();

		$db = new mysql();
		$exec = $db->executar("SELECT * FROM cidade where uf='$estado' order by nome asc ");
		$n = 0;
		while($data = $exec->fetch_object()){
			
			$lista[$n]['id'] = $data->id;
			$lista[$n]['nome'] = $data->nome;
			$lista[$n]['uf'] =  $data->uf;

		$n++;
		}

		return $lista;
	}

	public function nome_cidade($cidade){ 
		
		$db = new mysql();
		$exec = $db->executar("SELECT * FROM cidade where id='$cidade' ");
		$data = $exec->fetch_object();
		
		return $data->nome;		
	}

	public function combo(){
		
		$estado = $this->get('estado');
		$cidades = $this->lista($estado);

		$selecionado = $this->post('selecionado');

		echo '<select data-plugin-selectTwo class="form-control populate" name="cidade" >';
		echo "<option value='' >Selecione</option>";

		foreach ($cidades as $key => $value) {
			
			if($selecionado == $value['id']){ $select = " selected "; } else { $select = ""; }

			echo "<option value='".$value['id']."' $select >".$value['nome']."</option>";

		}
		
		echo "</select>";
		
		echo "
		<script>
			$('select').select2();
		</script>
		";

	}

}