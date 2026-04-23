<?php
class busca_blog extends controller {
	
	public function init(){		
	}
	
	public function inicial(){		
		
		$busca = $this->post('busca');		
		$this->irpara(DOMINIO.'blog/lista/busca/'.$busca.'/#corpo');
		
	}
	
}