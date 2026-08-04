document.addEventListener('DOMContentLoaded', function () {
    const bgContainer = document.getElementById('dynamicBg');

    // Lista de imágenes temáticas (puedes reemplazar por tus imágenes locales: 'img/bg-futbol.jpg', etc.)
    const bgImages = [
        'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=1920&auto=format&fit=crop', // Fútbol
        'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1920&auto=format&fit=crop', // Gaming / eSports
        'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=1920&auto=format&fit=crop', // Vóley
        'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=1920&auto=format&fit=crop', // Básquet
        'https://images.unsplash.com/photo-1511512578047-dfb367046420?q=80&w=1920&auto=format&fit=crop',  // Casino / Cartas
        'https://imagenes2.eltiempo.com/files/image_1200_535/uploads/2024/06/07/66639c764f7f0.jpeg'
    ];

    // 1. Elegir imagen al azar
    const randomIndex = Math.floor(Math.random() * bgImages.length);
    const selectedImageUrl = bgImages[randomIndex];

    // 2. Precargar la imagen antes de mostrarla
    const imgLoader = new Image();
    imgLoader.src = selectedImageUrl;

    imgLoader.onload = function () {
        if (bgContainer) {
            // Aplicar el fondo
            bgContainer.style.backgroundImage = `linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(30, 58, 138, 0.82) 100%), url('${selectedImageUrl}')`;
            // Hacerlo visible con fundido suave
            bgContainer.style.opacity = '1';
        }
    };

    // Si ocurre un error de red al cargar la imagen remota
    imgLoader.onerror = function () {
        if (bgContainer) {
            bgContainer.style.opacity = '1'; // Se mantendrá con el gradiente azul elegante de respaldo
        }
    };
});