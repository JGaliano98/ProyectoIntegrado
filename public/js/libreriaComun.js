function obtenerInformacion(contenidoFichero, patron, nCampos) {
    let lineas = contenidoFichero.trim().split("\n"); // Eliminar espacios en blanco al principio y al final
    let nLineas = lineas.length;
    let informacion = [];

    console.log("Número de líneas en el archivo:", nLineas);
    console.log("Patrón utilizado:", patron);

    for (let i = 0; i < nLineas; i++) {
        let linea = lineas[i].trim(); // Eliminar espacios en blanco al principio y al final de la línea
        console.log("Línea actual:", linea);
        if (patron.test(linea)) { // Verificar que la línea coincida con el patrón
            let partes = linea.split(";");
            console.log("Partes de la línea:", partes);
            if (partes.length === nCampos) { // Verificar el número de campos por línea
                informacion.push(partes); // Agregar la línea al array de información
                console.log("Línea válida, añadida al array de información.");
            } else {
                console.log("Línea inválida: número incorrecto de campos.");
            }
        } else {
            console.log("Línea no coincide con el patrón:", linea);
        }
    }

    return informacion;
}