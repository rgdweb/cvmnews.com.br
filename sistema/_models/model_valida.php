<?php

Class model_valida extends model{ 

    public function email($form_email1){

	    $mail_correcto = 0;
		if ((strlen($form_email1) >= 6) && (substr_count($form_email1,"@") == 1) && (substr($form_email1,0,1) != "@") && (substr($form_email1,strlen($form_email1)-1,1) != "@")){ 
			if ((!strstr($form_email1,"'")) && (!strstr($form_email1,"\"")) && (!strstr($form_email1,"\\")) && (!strstr($form_email1,"\$")) && (!strstr($form_email1," "))) { 
				if (substr_count($form_email1,".")>= 1){
					$term_dom = substr(strrchr ($form_email1, '.'),1);
					if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
						$antes_dom = substr($form_email1,0,strlen($form_email1) - strlen($term_dom) - 1); 
						$caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1); 
						if ($caracter_ult != "@" && $caracter_ult != "."){ 
							$mail_correcto = 1; 
						} 
					} 
				} 
			}
		}
		if($mail_correcto == 1){
			//correto
			return true;
		} else {
			return false;
		}
    }

    
}