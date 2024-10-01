document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault(); // Prevenir la redirección del enlace

        const url = this.href; // Obtener la URL del enlace
        const productImage = this.closest('.producto-item').querySelector('img');
        const cartIcon = document.querySelector('.fa-shopping-cart');

        if (!productImage || !cartIcon) return;

        // Crear un clon de la imagen para la animación
        const imageClone = productImage.cloneNode(true);
        imageClone.style.position = 'absolute';
        imageClone.style.zIndex = '1000';
        imageClone.style.width = productImage.offsetWidth + 'px';
        imageClone.style.height = productImage.offsetHeight + 'px';
        imageClone.style.transition = 'all 0.8s ease-in-out';

        const rect = productImage.getBoundingClientRect();
        imageClone.style.top = `${rect.top + window.scrollY}px`;  // Ajustar la posición con scroll
        imageClone.style.left = `${rect.left + window.scrollX}px`;  // Ajustar la posición con scroll
        document.body.appendChild(imageClone);

        const cartRect = cartIcon.getBoundingClientRect();
        const xOffset = cartRect.left - rect.left;
        const yOffset = cartRect.top - rect.top;

        // Iniciar la animación después de un breve retraso
        setTimeout(() => {
            imageClone.style.transform = `translate(${xOffset}px, ${yOffset}px) scale(0)`;
            imageClone.style.opacity = '0';
        }, 100);

        // Eliminar el clon de la imagen después de que termine la animación
        imageClone.addEventListener('transitionend', () => {
            imageClone.remove();
        });

        // Realizar la solicitud AJAX
        fetch(url, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.cart-count').textContent = data.cartCount;
                } else if (data.error) {
                    alert(data.error); // Mostrar error si existe
                }
            })
            .catch(error => console.error('Error al añadir al carrito:', error));
    });
});
