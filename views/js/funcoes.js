
// janelas por cima da tela

function modal( endereco ){

  $('#modal_conteudo').html("<div style='text-align:center;'><img src='"+dominio()+"_views/img/loading.gif' style='width:25px;'></div>");
  $('#janela_modal').modal('show');
    
  $.post(endereco, { variaveis: '' },function(data){
    if(data){
      $('#modal_conteudo').html(data);
    }
  });

}

function numeroParaMoeda(n, c, d, t)
{
    c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}
///////////////////////////////////////////////////////////////////
//Produtos

function ordena_lista(endereco){
  window.location=''+endereco;
}

//////////////////////////////////////////////////////////////////////////////////
/*Função  Pai de Mascaras*/
// onkeypress="Mascara(this,MaskMonetario)"
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
function moeda(v){ 
    v=v.replace(/\D/g,"") // permite digitar apenas numero 
    v=v.replace(/(\d{1})(\d{17})$/,"$1.$2") // coloca ponto antes dos ultimos digitos 
    v=v.replace(/(\d{1})(\d{13})$/,"$1.$2") // coloca ponto antes dos ultimos 13 digitos 
    v=v.replace(/(\d{1})(\d{10})$/,"$1.$2") // coloca ponto antes dos ultimos 10 digitos 
    v=v.replace(/(\d{1})(\d{7})$/,"$1.$2") // coloca ponto antes dos ultimos 7 digitos 
    v=v.replace(/(\d{1})(\d{1,4})$/,"$1,$2") // coloca virgula antes dos ultimos 4 digitos 
    return v;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////

//funçao valida email jquery
function validacaoEmail(sEmail) {
    
    // filtros
    var emailFilter=/^.+@.+\..{2,}$/;
    var illegalChars= /[\(\)\<\>\,\;\:\\\/\"\[\]]/
    // condição
    if(!(emailFilter.test(sEmail))||sEmail.match(illegalChars)){
        //emails inválido 
        return 1;
    }else{
        //emails Válido 
        return 0;
    }

}

/////////////////////////////////////////////////////////

//valida cpf

function validaCPF(cpf)
  {
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
          return false;
    for (i = 0; i < cpf.length - 1; i++)
          if (cpf.charAt(i) != cpf.charAt(i + 1))
                {
                digitos_iguais = 0;
                break;
                }
    if (!digitos_iguais)
          {
          numeros = cpf.substring(0,9);
          digitos = cpf.substring(9);
          soma = 0;
          for (i = 10; i > 1; i--)
                soma += numeros.charAt(10 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(0))
                return false;
          numeros = cpf.substring(0,10);
          soma = 0;
          for (i = 11; i > 1; i--)
                soma += numeros.charAt(11 - i) * i;
          resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
          if (resultado != digitos.charAt(1))
                return false;
          return true;
          }
    else
        return false;
}

 