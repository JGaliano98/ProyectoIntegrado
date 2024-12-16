// Seleccionar todos los botones con la clase 'add-to-cart' y agregar un evento 'click' a cada uno
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevenir la acción predeterminada del enlace, como redireccionar

        const url = this.href; // Obtener la URL del enlace (que será usada para la solicitud AJAX)
        const productImage = this.closest('.producto-item').querySelector('img'); // Seleccionar la imagen del producto dentro del contenedor más cercano
        const cartIcon = document.querySelector('.fa-shopping-cart'); // Seleccionar el icono del carrito en la interfaz

        // Verificar que tanto la imagen del producto como el icono del carrito existen, en caso contrario, salir
        if (!productImage || !cartIcon) return;

        // Crear un clon de la imagen del producto para usarlo en la animación
        const imageClone = productImage.cloneNode(true);
        imageClone.style.position = 'absolute'; // Posicionarlo de manera absoluta para poder moverlo libremente
        imageClone.style.zIndex = '1000'; // Asegurar que el clon esté por encima de otros elementos
        imageClone.style.width = productImage.offsetWidth + 'px'; // Mantener el ancho original
        imageClone.style.height = productImage.offsetHeight + 'px'; // Mantener la altura original
        imageClone.style.transition = 'all 0.8s ease-in-out'; // Configurar la transición para la animación

        // Obtener las coordenadas actuales de la imagen original
        const rect = productImage.getBoundingClientRect();
        imageClone.style.top = `${rect.top + window.scrollY}px`; // Ajustar la posición vertical considerando el scroll
        imageClone.style.left = `${rect.left + window.scrollX}px`; // Ajustar la posición horizontal considerando el scroll
        document.body.appendChild(imageClone); // Agregar el clon al cuerpo del documento para que sea visible

        // Obtener las coordenadas del icono del carrito
        const cartRect = cartIcon.getBoundingClientRect();
        const xOffset = cartRect.left - rect.left; // Calcular la diferencia horizontal entre la imagen y el carrito
        const yOffset = cartRect.top - rect.top; // Calcular la diferencia vertical entre la imagen y el carrito

        // Iniciar la animación para mover la imagen hacia el carrito
        setTimeout(() => {
            imageClone.style.transform = `translate(${xOffset}px, ${yOffset}px) scale(0)`; // Mover y reducir el tamaño de la imagen
            imageClone.style.opacity = '0'; // Hacer que la imagen desaparezca al final
        }, 100); // Retrasar ligeramente la animación para asegurar que los estilos iniciales se apliquen

        // Eliminar el clon de la imagen después de que termine la animación
        imageClone.addEventListener('transitionend', () => {
            imageClone.remove(); // Quitar el elemento del DOM para liberar memoria
        });

        // Realizar la solicitud AJAX para añadir el producto al carrito
        fetch(url, { method: 'GET' }) // Hacer una solicitud GET a la URL obtenida del enlace
            .then(response => response.json()) // Parsear la respuesta como JSON
            .then(data => {
                if (data.success) {
                    // Actualizar el contador del carrito si la operación fue exitosa
                    document.querySelector('.cart-count').textContent = data.cartCount;
                } else if (data.error) {
                    // Mostrar un mensaje de error en caso de que ocurra un problema en el servidor
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error al añadir al carrito:', error)); // Manejar errores durante la solicitud AJAX
    });
});
