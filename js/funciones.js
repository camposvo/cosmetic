/*-------------------------------------------------------------------------------------------
	Nombre: funciones.js                                            
	Descripcion: Archivo que contiene funciones de utilidad en javascrip para ser 
		utilizadas en los archivos PHP
--------------------------------------------------------------------------------------------*/

/*-------------------------------------------------------------------------------------------
	Define el color de la fila 
--------------------------------------------------------------------------------------------*/
const  FILA ="#D0FDC6";  


// Función para calcular los días transcurridos entre dos fechas
function restaFechas(f1,f2)
 {
 var aFecha1 = f1.split('/'); 
 var aFecha2 = f2.split('/'); 
 var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
 var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
 var dif = fFecha2 - fFecha1;
 var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
 return dias;
 }


function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
			numero=parseFloat(numero);
			if(isNaN(numero)){
				return "";
			}

			if(decimales!==undefined){
				// Redondeamos
				numero=numero.toFixed(decimales);
			}

			// Convertimos el punto en separador_decimal
			numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

			if(separador_miles){
				// Añadimos los separadores de miles
				var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
				while(miles.test(numero)) {
					numero=numero.replace(miles, "$1" + separador_miles + "$2");
				}
			}

			return numero;
		}	
	


function redondeo2decimales(numero)
{
	var flotante = parseFloat(numero);
	var resultado = Math.round(flotante*100)/100;
	return resultado;
}

 function cambiacolor_over(celda,color){ 
	
	celda.style.backgroundColor=color; 

} 

function cambiacolor_out(celda,color){ 
		celda.style.backgroundColor=color; 
}

/*-------------------------------------------------------------------------------------------
	Nombre: validarNum
	Descripcion: funcion que valida que una tecla presionada sea numerica
--------------------------------------------------------------------------------------------*/
function validarNum(e){
	tecla = (document.all) ? e.keyCode : e.which; //Se obtiene el valor ASCII de la tecla pulsada.
    if (tecla == 8 || tecla == 0) return true;    //Se comprueba si es la tecla pulsada es la tecla de retroceso y retorna
    patron = /\d/;                                // Se establece una expresion regular
    te = String.fromCharCode(tecla);              //Se pasa el valor ASCII de la variable tecla a su carácter correspondiente
    return patron.test(te);                       //Si el carácter coincide con el patrón, la función devuelve true, si no coincide devuelve false. 
}

/*-------------------------------------------------------------------------------------------
	Nombre: mes_letras
	Descripcion: Recibe un numero y retorna el mes en letras 
--------------------------------------------------------------------------------------------*/
function mes_letras(numero){
	var mes="";
	if(numero=="01") mes="Enero";
	if(numero=="02") mes="Febrero";
	if(numero=="03") mes="Marzo";
	if(numero=="04") mes="Abril";
	if(numero=="05") mes="Mayo";
	if(numero=="06") mes="Junio";
	if(numero=="07") mes="Julio";
	if(numero=="08") mes="Agosto";
	if(numero=="09") mes="Septiembre";
	if(numero=="10") mes="Octubre11111";
	if(numero=="11") mes="Noviembre";
	if(numero=="12") mes="Diciembre";
	return mes;
}

/*-------------------------------------------------------------------------------------------
	Nombre: dia_letras
	Descripcion: Recibe un numero y retorna el dia en letras 
--------------------------------------------------------------------------------------------*/
function dia_letras(numero)
	{var temp=new Array();
	var cadena="";
	numero=parseInt(numero);
	temp[0]=parseInt(numero/10);
	temp[1]=parseInt(numero%10);
	var j=0;
	while(j<2)
		{if(numero<10){j++;}
		if(numero>15 && numero<20){ cadena="dieci"; j++;}
		if(numero>20 && numero<30){ cadena= "venti"; j++;}
		if(numero>30 && numero<32){ cadena= "treinta y"; j++;}
		if(temp[j]==1){cadena=cadena+"uno"; j++;}
		else if(temp[j]==2){cadena=cadena+"dos"; j++;}
		else if(temp[j]==3){cadena=cadena+"tres"; j++;}
		else if(temp[j]==4){cadena=cadena+"cuatro"; j++;}
		else if(temp[j]==5){cadena=cadena+"cinco"; j++;}
		else if(temp[j]==6){cadena=cadena+"seis";j++;}
		else if(temp[j]==7){cadena=cadena+"siete"; j++;}
		else if(temp[j]==8){cadena=cadena+"ocho"; j++;}
		else if(temp[j]==9){cadena=cadena+"nueve"; j++;}
		if(numero==10){cadena="diez"; j++;}
		if(numero==11){cadena="once"; j++;}
		if(numero==12){cadena="doce"; j++;}
		if(numero==13){cadena="trece"; j++;}
		if(numero==14){cadena="catorce"; j++;}
		if(numero==15){cadena="quince"; j++;}
		if(numero==20){cadena="veinte"; j++;}
		if(numero==30){cadena="treinta"; j++;}
		}//fin del while
	return cadena;
}

/*-------------------------------------------------------------------------------------------
	Nombre: validarLetras
	Descripcion: funcion que valida que una tecla presionada sea Letra
--------------------------------------------------------------------------------------------*/
function validarLetras(e){
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8 || tecla == 0){
				return true;
	}
    patron =/[A-Za-zñÑ().,\s]/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

/*-------------------------------------------------------------------------------------------
	Nombre: validardec
	Descripcion: Valida que una tecla presionada sea un numero o un punto
--------------------------------------------------------------------------------------------*/
function validardec(e) {
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8 || tecla == 0) return true;
	patron = /[0-9.]+/;
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

/*-------------------------------------------------------------------------------------------
	Nombre: compararFecha1mayorqueFecha2
	Descripcion: Devuelva true si fecha1 es mayor que fecha2 sino devuelve false
--------------------------------------------------------------------------------------------*/
function compararFecha1mayorqueFecha2(fecha1, fecha2){
		//alert ('Mayor o menor que');
	dia1=fecha1.split("/")[0];
	mes1=fecha1.split("/")[1];
	anyo1=fecha1.split("/")[2];
	
	dia2=fecha2.split("/")[0];
	mes2=fecha2.split("/")[1];
	anyo2=fecha2.split("/")[2];
	
	f1 = new Date(anyo1,mes1-1,dia1);
	f2 = new Date(anyo2,mes2-1,dia2);
	if(f1 > f2)
			return true;
	return false;
}

/*-------------------------------------------------------------------------------------------
	Nombre: IsNumeric
	Descripcion: Comprueba si un valor es Numerico y devuelve true sino devuelve false
--------------------------------------------------------------------------------------------*/
function IsNumeric(valor)
{
	var log=valor.length; var sw="S";
	for (x=0; x<log; x++){
		v1=valor.substr(x,1);
		v2 = parseInt(v1);
		//Compruebo si es un valor numrico
		if (isNaN(v2)) { sw= "N";}
	}
	if (sw=="S") {return true;} else {return false; }
}

/*-------------------------------------------------------------------------------------------
	Nombre: campos_blancos
	Descripcion: Comprueba si los campos de un formulario estan vacios, toma en consideracion
		que sin un campo inicia con la letra o_ es obligatorio sino puede estar vacio. Retorna 
		true si quedan campos blancos sino retona false
--------------------------------------------------------------------------------------------*/
function campos_blancos(forma){

for(i=0;i<forma.elements.length;i++)
    {	snombrecampo = forma.elements[i].name;
		console.log(forma.elements[i].type);
		
    	if (forma.elements[i].type == "text")	
    	 {	if ((snombrecampo.substring(0,1) == "o")||(snombrecampo.substring(0,1) == "n"))
    	   	{	if (snombrecampo.substring(2,1) == "_")
     	   		{	if (snombrecampo.substring(0,1) == "o")
					{	if (forma.elements[i].value == "")
					 		{	alert(" Este campo es requerido para continuar!");
								forma.elements[i].focus();
					  			return true;
       	     				}
        			}
					if (snombrecampo.substring(0,1) == "n")			
					{	if (forma.elements[i].value != "")
						{	if(IsNumeric(forma.elements[i].value)==false)				   
							{alert("Debe introducir solo numeros en este campo");
							 forma.elements[i].value = "";
							 forma.elements[i].focus();
							 return true;
						   }
          				 } // fin del  if (forma.elements[i].value != "")
        			} // fin del if (snombrecampo.substring(0,1) == "n")
				}
   			}
			else
			{continue;
			}
   		}
	   else 
		if(forma.elements[i].type == "select-multiple")
			{	if(forma.elements[i].length<1)
					{alert("Este campo es requerido para continuar!");
					forma.elements[i].focus();
					return true;
					}
			}
		else
		   if (forma.elements[i].type == "select-one")	
			
		   	   { if (snombrecampo.substring(0,1) == "o")
					 {if (snombrecampo.substring(2,1) == "_")
						{var indice = forma.elements[i].selectedIndex ;
						 var textoEscogido = forma.elements[i].options[indice].text
						 if (textoEscogido == 'Seleccionar')
							{alert("Este campo es requerido para continuar!");
							 forma.elements[i].focus();
							 return true;
						 	}
						 }
					  }
				}
			else
		   		{ continue;}

	}//Fin del For
return false;
}//fin campos_blancos

/*-------------------------------------------------------------------------------------------
	Nombre: campos_blancos
	Descripcion: Retorna la Fecha actual en el siguiente formato
		FECHA:  "JUEVES, 12 DE OCTUBRE DE 2006"
--------------------------------------------------------------------------------------------*/
function fecha1(){
	fecha = new Date()
	mes = fecha.getMonth()
	diaMes = fecha.getDate()
	diaSemana = fecha.getDay()
	anio = fecha.getFullYear()
	dias = new Array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sbado')
	meses = new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre')
	document.write('<span id="fecha">')
	document.write (dias[diaSemana] + ", " + diaMes + " de " + meses[mes] + " de " + anio)
	document.write ('</span>')
}

/*-------------------------------------------------------------------------------------------
	Nombre: calcula_edad
	Descripcion: Retorna la edad de una persona en base a la fecha de nacimiento
--------------------------------------------------------------------------------------------*/
function calcula_edad(fecha){
	hoy = new Date()
	var array_fecha = fecha.split("/")
	if (array_fecha.length!=3)
	   return false

	var ano
	ano = parseInt(array_fecha[2],10);
	if (isNaN(ano))	return false

	var mes
	mes = parseInt(array_fecha[1],10);
	if (isNaN(mes)) return false 

	var dia
	dia = parseInt(array_fecha[0],10);
	if (isNaN(dia)) return false

	if (ano<=99)	    //si el ao de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
		ano +=1900
					
	edad=hoy.getFullYear()- ano - 1; //-1 porque no se si ha cumplido aos ya este ao
	if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
		return edad
	if (hoy.getMonth() + 1 - mes > 0)return edad+1
	if (hoy.getDate() - dia >= 0)return edad + 1
	
	return edad 
}

/*-------------------------------------------------------------------------------------------
	Nombre: valida_edad
	Descripcion: Valida si la edad segun la fecha de nacimiento es un valor valido
--------------------------------------------------------------------------------------------*/
function valida_edad(fecha_nac){
	fecha_nac = document.formulario.o_fecha_nacimiento.value;
	ed = calcula_edad (fecha_nac);
	if (ed == false){
		alert("Fecha de Nacimiento Invalida...");
		document.formulario.o_fecha_nacimiento.value = "";
	}else{
		document.formulario.o_edad.value = ed;
	}
}