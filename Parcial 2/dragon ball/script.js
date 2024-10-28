const audio = document.getElementById('backgroundAudio');
const toggleButton = document.getElementById('toggleMusic');

// Comprobar si el audio ya se está reproduciendo en otra pestaña
if (!localStorage.getItem('audioInitialized')) {
    localStorage.setItem('audioInitialized', 'true'); // Marcar que la música ha iniciado
    audio.play().catch((error) => console.error('Error al reproducir audio:', error));
}

// Restaurar la posición y el estado del audio
window.addEventListener('load', () => {
    const savedTime = localStorage.getItem('audioTime') || 0;
    const isPaused = localStorage.getItem('audioPaused') === 'true';

    audio.currentTime = parseFloat(savedTime);

    if (!isPaused) {
        audio.play().catch((error) => console.error('Error al reproducir audio:', error));
        toggleButton.innerText = '🔇 Pausar Música';
    } else {
        toggleButton.innerText = '🔊 Reproducir Música';
    }
});

// Guardar la posición y el estado del audio al salir o recargar la página
window.addEventListener('beforeunload', () => {
    localStorage.setItem('audioTime', audio.currentTime);
    localStorage.setItem('audioPaused', audio.paused);
});

// Controlar reproducción/pausa con el botón
toggleButton.addEventListener('click', () => {
    if (audio.paused) {
        audio.play();
        toggleButton.innerText = '🔇 Pausar Música';
    } else {
        audio.pause();
        toggleButton.innerText = '🔊 Reproducir Música';
    }
});
