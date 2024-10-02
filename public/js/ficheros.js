// Espera a que la ventana se haya cargado completamente antes de ejecutar el código
window.addEventListener("load", function() {

    const checkActEdTabla = document.getElementById("actEdTabla");
    const tabla = document.getElementById("modalTable");
    const guardarBtn = document.getElementById("modalGuardarBtn");
    const fichero = document.getElementById("csvFileInput");

  

    // Funciones de clic para los botones de cancelar, borrar, editar y guardar en la tabla
    function pulsadoCancelar(fila) {
        return function() {
            fila.cancelar();
        };
    }

    function pulsadoBorrar(fila) {
        return function() {
            const respuesta = confirm("¿Estás seguro que quieres borrar?");
            if (respuesta) {
                fila.parentElement.removeChild(fila);
            }
        };
    }

    function pulsadoEditar(fila) {
        return function() {
            fila.editar();
        };
    }

    function pulsadoGuardar(fila) {
        return function() {
            fila.guardar();
        };
    }

    // Activa o desactiva la edición de la tabla según el estado del checkbox
    if (checkActEdTabla) {
        checkActEdTabla.onchange = function() {
            if (this.checked) {
                tabla.activarEdicion(pulsadoBorrar, pulsadoEditar, pulsadoGuardar, pulsadoCancelar);
            } else {
                tabla.desactivarEdicion();
            }
        };
    }

    // Maneja la carga de archivos CSV
    if (fichero) {
        fichero.onchange = function() {
            const ficheroSubido = this.files[0];
            const patron = new RegExp(this.dataset.patron);
            const campos = parseInt(this.dataset.campo);

            if ((/\.csv$/i).test(ficheroSubido.name)) {
                const lector = new FileReader();
                lector.readAsText(ficheroSubido);
                lector.onload = function() {
                    const informacion = obtenerInformacion(this.result, patron, campos);
                    tabla.setData(informacion);
                };
            } else {
                alert("El fichero subido no tiene el formato csv");
            }
        };
    }

    // Guarda los datos de la tabla en el servidor
    if (guardarBtn) {
        guardarBtn.addEventListener("click", function() {
            const entidad = fichero.dataset.entidad;

            if (checkActEdTabla && checkActEdTabla.checked) {
                alert("Tienes que quitar el modo edición para poder guardar.");
            } else {
                const datos = tabla.getData();
                const patron = new RegExp(fichero.dataset.patron);

                const validacion = validarDatos(datos, patron);
                if (!validacion.valido) {
                    alert(`Algunos datos no cumplen con el patrón requerido. Error en la línea: ${validacion.linea + 1}`);
                    return;
                }

                datos.forEach(fila => {
                    if (fila[1]) {
                        fila[1] = fila[1].toLowerCase();
                    }
                });

                console.log(tabla.getData());

                if (entidad === 'Categoria') {
                    subirDatos(datos, 'categorias');
                }
                if (entidad === 'User') {
                    subirDatos(datos, 'users');
                }
            }
        });
    }
    // Sube los datos a la API correspondiente
    function subirDatos(datos, ruta) {
        let datosFormateados;
    
        // Formatear los datos según la entidad (ruta)
        switch (ruta) {
            case 'categorias':
                datosFormateados = datos.map(fila => {
                    return {
                        "Nombre": fila[0], // Primer elemento es el nombre
                        "descripcion": fila[1] // Segundo elemento es la descripción
                    };
                });
                break;
    
            case 'users':
                datosFormateados = datos.map(fila => {
                    return {
                        "email": fila[0] ? fila[0].trim() : null,             // Primer elemento es el email
                        "nombre": fila[1] ? fila[1].trim() : null,            // Segundo elemento es el nombre
                        "apellido1": fila[2] ? fila[2].trim() : null,         // Tercer elemento es el primer apellido
                        "apellido2": fila[3] ? fila[3].trim() : null,         // Cuarto elemento es el segundo apellido
                        "telefono": fila[4] ? fila[4].trim() : null,          // Quinto elemento es el teléfono
                        "roles": ["ROLE_USER"],                               // Valor fijo
                        "password": "$13$pPdz4u3/CZ/CDlElL8e.Kemlfz9x2RkX0qKK0otgZBjZZ6IgYneKG", // Valor fijo
                        "is_verified": 1,                                     // Valor fijo
                        "is_active": 1                                        // Valor fijo
                    };
                });
    
                // Filtrar cualquier objeto donde falten datos obligatorios
                datosFormateados = datosFormateados.filter(user => {
                    return user.email && user.nombre && user.apellido1 && user.apellido2;
                });
    
                break;
    
            default:
                // Si la entidad no necesita transformación específica, dejar los datos como están
                datosFormateados = datos;
                break;
        }
    
        // Verificar los datos formateados antes de enviarlos
        console.log('Datos Formateados:', datosFormateados);
    
        const rutaAPI = 'API/' + ruta;
        fetch('/' + rutaAPI, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosFormateados) // Enviar los datos formateados
        })
        .then(response => response.json())
        .then(data => {
            console.log('Éxito:', data);
    
            const tBody = document.getElementById("modalTable").tBodies[0];
            const filas = tBody.rows;
            const nFilas = filas.length;
    
            for (let i = nFilas - 1; i >= 0; i--) {
                if (data.errors.indexOf(i) == -1) {
                    tBody.removeChild(filas[i]);
                }
            }
    
            if (data.errors.length === 0) {
                alert('Datos subidos con éxito');
                setTimeout(function() {
                    location.reload();
                }, 500);
            } else {
                const errorIndices = data.errors.join(', ');
                alert(`Los siguientes usuarios no se han podido añadir: ${errorIndices}`);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('Hubo un error al subir los datos.');
        });
    }
    



    // Valida los datos según el patrón proporcionado
    function validarDatos(datos, patron) {
        for (let i = 0; i < datos.length; i++) {
            const fila = datos[i].join(';');
            if (!patron.test(fila)) {
                return { valido: false, linea: i };
            }
        }
        return { valido: true, linea: -1 };
    }
});