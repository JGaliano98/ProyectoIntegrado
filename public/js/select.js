//Función que pasandole un array bidimensional 
//genera los option del select añadiendo al final 
//datos es un array
//donde es un objeto DOM Select

function cargarDatosSelect(datos, donde) {
    console.log("Datos a cargar en select:", datos); // Agregar mensaje de consola
    for (let i = 0; i < datos.length; i++) {
        let option = document.createElement("option");
        option.value = datos[i][0];
        option.innerHTML = datos[i][1]; // Mostrar el nombre correctamente
        donde.appendChild(option);
    }
}

//Función que pasándole un objeto DOM select lo vacia
function vaciarSelect(select){
    select.innerHTML="";
}

//Funcion pasar seleccionados
//Pasa los elementos seleccionado en origen a destino
function pasarSeleccionadosSelect(origen, destino){
    while (origen.selectedOptions.length>0){
        let candidato=origen.selectedOptions[0];
        candidato.selected=false;
        destino.appendChild(candidato);
    }
}

function pasarTodosSelect(origen, destino){
    while (origen.options.length>0){
        let candidato=origen.options[0];
        candidato.selected=false;
        destino.appendChild(candidato);
    }
}

function obtenerDatosSelect(select){
    let respuesta=[];
    for (let i=0;i<select.options.length;i++){
        let dato=[select.options[i].value,select.options[i].innerHTML];
        respuesta.push(dato);
    }
    return respuesta;
}