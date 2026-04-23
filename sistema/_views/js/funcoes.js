function altera_menu(){
	$.post(dominio()+'menu/altera', { },function(){ });
}

function modal( endereco, titulo, tamanho, variaveis){
	
	$('.modal_titulo').html(titulo);

	if(tamanho == 'grande'){
		$('#modal_conteudo_g').html("<div style='text-align:center;'><img src='"+dominio()+"_views/img/loading.gif' style='width:25px;'></div>");
		$('#janela_modal_grande').modal();
	} else {
		$('#modal_conteudo_m').html("<div style='text-align:center;'><img src='"+dominio()+"_views/img/loading.gif' style='width:25px;'></div>");
		$('#janela_modal_medio').modal();
	}

    $.post(endereco, { variaveis: variaveis },function(data){
	    if(data){
	    	if(data){
	    		if(tamanho == 'grande'){
	    			$('#modal_conteudo_g').html(data);
	    		} else {
	    			$('#modal_conteudo_m').html(data);
	    		}
	    	}
	    }
	});

}

function confirma(endereco) {
	if(confirm('Tem certeza que deseja realizar esta ação?')){
		window.location=endereco;
	} else {
		return false;
	}
}
function confirma_apagar(endereco) {
	msg = "Tem certeza de que deseja remover este(s) item(s)?";
	if(confirm(msg)) {
		window.location=endereco;
	} else {
		return false;
	}
}
function apagar_varios(id) {
	msg = "Tem certeza de que deseja remover os itens selecionados?";
	if(confirm(msg)) {
		$('#'+id).submit();
	} else {
		return false;
	}
}

/*Função  Pai de Mascaras*/
function Mascara(o,f){
        v_obj=o
        v_fun=f
        setTimeout("execmascara()",1)
}    
/*Função que Executa os objetos*/
function execmascara(){
        v_obj.value=v_fun(v_obj.value)
}    
/*Função que Determina as expressões regulares dos objetos*/
function leech(v){
        v=v.replace(/o/gi,"0")
        v=v.replace(/i/gi,"1")
        v=v.replace(/z/gi,"2")
        v=v.replace(/e/gi,"3")
        v=v.replace(/a/gi,"4")
        v=v.replace(/s/gi,"5")
        v=v.replace(/t/gi,"7")
        return v
}
/*Função que permite apenas numeros*/
function Integer(v){
        return v.replace(/\D/g,"")
}
function Data(v){
        v=v.replace(/\D/g,"") 
        v=v.replace(/(\d{2})(\d)/,"$1/$2") 
        v=v.replace(/(\d{2})(\d)/,"$1/$2") 
        return v
}
function telefone(v){
	var numeros = v.length;
    v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
	v=v.replace(/^(0+)(\d)/g,"$2"); 
    v=v.replace(/^(\d\d)(\d)/g,"($1) $2") //Coloca parênteses em volta dos dois primeiros dígitos
	
	if(numeros == 15){
		v=v.replace(/(\d{5})(\d)/,"$1-$2")    //Coloca hífen entre o quarto e o quinto dígitos
	} else {
		v=v.replace(/(\d{4})(\d)/,"$1-$2")    //Coloca hífen entre o quarto e o quinto dígitos		
	}
	
	return v
}
function ceppp(v){
    v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
    v=v.replace(/(\d{5})(\d)/,"$1-$2")    //Coloca hífen entre o quarto e o quinto dígitos
    return v
}
function bloqueio(v){
    return ''
}
function MaskMonetario(v){
	v=v.replace(/\D/g,"");
	v=v.replace(/(\d{2})$/,",$1");
	v=v.replace(/(\d+)(\d{3},\d{2})$/g,"$1.$2");
	var qtdLoop = (v.length-3)/3; var count = 0;
	while (qtdLoop > count){ count++;
	v=v.replace(/(\d+)(\d{3}.*)/,"$1.$2");
	}v=v.replace(/^(0)(\d)/g,"$2");
	return v 
}
function porcentagem(v){
	v=v.replace(/\D/g,"");
	v=v.replace(/(\d{2})$/,".$1");
	v=v.replace(/(\d+)(\d{3},\d{2})$/g,"$1.$2");
	var qtdLoop = (v.length-3)/3; var count = 0;
	while (qtdLoop > count){ count++;
	v=v.replace(/(\d+)(\d{3}.*)/,"$1.$2");
	}v=v.replace(/^(0)(\d)/g,"$2");
	return v 
}