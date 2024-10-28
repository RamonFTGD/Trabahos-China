<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Dashboard - Personajes</title>
    <style>
        body {
    font-family: 'Orbitron', sans-serif;
    background-image: url('backgrounds/sparking_zero_bg.jpg');
    /* Usa tu fondo de Sparking Zero */
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    overflow: hidden;
    animation: background-pulse 10s infinite alternate; /* Pulso suave en el fondo */
}

/* Animación de fondo */
@keyframes background-pulse {
    0% { filter: brightness(1); }
    100% { filter: brightness(1.3); }
}

/* Contenedores de login y registro */
.login-container, .register-container {
    background: rgba(0, 0, 0, 0.85);
    border: 2px solid rgba(0, 255, 255, 0.7); /* Borde luminoso */
    border-radius: 12px;
    width: 350px;
    padding: 40px;
    box-shadow: 0 0 20px rgba(0, 255, 255, 0.7);
    text-align: center;
    animation: glow 2s infinite alternate; /* Efecto de brillo */
}

/* Animación de brillo */
@keyframes glow {
    0% { box-shadow: 0 0 10px rgba(0, 255, 255, 0.4); }
    100% { box-shadow: 0 0 30px rgba(0, 255, 255, 1); }
}

/* Títulos */
h2 {
    font-size: 32px;
    color: #00ffcc;
    text-shadow: 0 0 20px #00ffcc, 0 0 40px #00ffff;
    margin-bottom: 20px;
}

/* Formularios */
form {
    display: flex;
    flex-direction: column;
}

label {
    color: #d0faff;
    margin-top: 10px;
}

input {
    padding: 12px;
    margin-top: 5px;
    border: 1px solid #00ffcc;
    border-radius: 8px;
    background-color: rgba(0, 0, 0, 0.6);
    color: white;
    outline: none;
    transition: box-shadow 0.3s ease;
}

input:focus {
    box-shadow: 0 0 10px #00ffcc;
    border-color: #00ffff;
}

/* Botones */
button {
    padding: 15px;
    margin-top: 20px;
    background: linear-gradient(45deg, #00ffcc, #007bff);
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    transition: 0.4s;
    box-shadow: 0 0 15px #00ffcc;
}

button:hover {
    background: linear-gradient(45deg, #007bff, #00ffcc);
    box-shadow: 0 0 30px #00ffff;
    transform: scale(1.05);
}

/* Contenedor de error */
.error {
    color: #ff6b6b;
    margin-top: 10px;
    text-shadow: 0 0 10px #ff6b6b;
}

/* Vista previa del avatar */
.avatar-preview img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 3px solid #00ffff;
    box-shadow: 0 0 15px #00ffcc;
    margin-top: 15px;
}

/* Tablas */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    border: 1px solid #00ffcc;
    text-align: center;
    color: white;
}

th {
    background: linear-gradient(45deg, #007bff, #00ffcc);
}

tbody tr:nth-child(even) {
    background: rgba(0, 0, 0, 0.5);
}

/* Botones adicionales */
.buttons-container button {
    background: linear-gradient(45deg, #ff6b6b, #ffcc33);
    color: white;
    margin-top: 10px;
    transition: 0.3s;
}

.buttons-container button:hover {
    background: linear-gradient(45deg, #ffcc33, #ff6b6b);
    box-shadow: 0 0 20px #ffcc33;
}
#background-audio {
    display: none; /* El audio es invisible en la página */
}
#toggleMusic {
            position: fixed; 
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            box-shadow: 0 0 10px rgba(0, 0, 255, 0.5);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #toggleMusic:hover {
            background-color: #0056b3;
            transform: scale(1.1); 
        }
             /* Contenedor del Dashboard */
             .dashboard-container {
            opacity: 0; /* Invisible al inicio */
            transform: scale(0.8) translateY(-100%);
            border: 2px solid rgba(0, 255, 255, 0.7);
            border-radius: 12px;
            width: 350px;
            padding: 40px;
            border: 2px solid rgba(0, 255, 255, 0.7); /* Borde luminoso */
            animation: glow 2s infinite alternate; /* Efecto de brillo */
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.7);
            background: rgba(0, 0, 0, 0.85);
            text-align: center;
            transition: opacity 1.5s, transform 1.5s;
        }

        /* Animación épica tipo Sparking Zero */
        @keyframes sparkingZeroAnimation {
            0% {
                opacity: 0;
                transform: scale(0.5) translateY(-100%);
                filter: blur(10px);
            }
            50% {
                opacity: 1;
                transform: scale(1.1) translateY(10%);
                filter: blur(0);
                box-shadow: 0 0 30px rgba(0, 255, 255, 0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
                box-shadow: 0 0 15px rgba(0, 255, 255, 0.6);
            }
        }

        /* Clase que activa la animación */
        .show {
            animation: sparkingZeroAnimation 1.5s ease-out forwards;
        }
                /* Animación de fondo */
                @keyframes background-pulse {
            0% { filter: brightness(1); }
            100% { filter: brightness(1.3); }
        }

        /* Contenedor del menú de configuración */
        .settings-container {
            width: 400px;
            padding: 40px;
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid rgba(0, 255, 255, 0.7);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.7);
            text-align: center;
            animation: glow 2s infinite alternate;
        }

        /* Animación de brillo */
        @keyframes glow {
            0% { box-shadow: 0 0 10px rgba(0, 255, 255, 0.4); }
            100% { box-shadow: 0 0 30px rgba(0, 255, 255, 1); }
        }

        /* Estilo de los títulos */
        h1 {
            font-size: 28px;
            color: #00ffcc;
            text-shadow: 0 0 15px #00ffcc, 0 0 30px #00ffff;
            margin-bottom: 20px;
        }

        /* Estilo de los sliders */
        input[type="range"] {
            width: 100%;
            margin: 15px 0;
            appearance: none;
            background: #007bff;
            height: 8px;
            border-radius: 5px;
            outline: none;
            transition: background 0.3s;
        }

        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            width: 20px;
            height: 20px;
            background: #00ffcc;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
        }

        /* Botón para regresar */
        .settings-container button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(45deg, #00bfff, #007bff);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .settings-container button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
        }

  .scrollable-container {
    height: 80vh; /* Alto del contenedor para permitir desplazamiento */
    overflow-y: auto; /* Habilitar desplazamiento vertical */
    padding: 20px;
    border-radius: 15px;
    background: rgba(0, 0, 0, 0.6); /* Fondo translúcido */
    box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
}
</style>
</head>
<div class="settings-container">
        <h1>Configuración</h1>

        <!-- Control de Volumen -->
        <label for="volumeControl">Volumen de la Música</label>
        <input type="range" id="volumeControl" min="0" max="1" step="0.01" value="0.5">

        <!-- Control de Brillo -->
        <label for="brightnessControl">Brillo de Pantalla</label>
        <input type="range" id="brightnessControl" min="0.5" max="1.5" step="0.1" value="1">

        <!-- Botón para regresar -->
        <button onclick="window.location.href='dashboard.php'">Regresar</button>
    </div>

    <audio id="backgroundAudio" loop>
        <source src="musica/official-opening-movie.mp3" type="audio/mp3">
        Tu navegador no soporta audio HTML5.
    </audio>

    <script>
        const audio = document.getElementById('backgroundAudio');
        const volumeControl = document.getElementById('volumeControl');
        const brightnessControl = document.getElementById('brightnessControl');

        // Ajustar el volumen de la música
        volumeControl.addEventListener('input', (e) => {
            audio.volume = e.target.value;
            console.log('Volumen:', audio.volume);
        });

        // Ajustar el brillo de la pantalla
        brightnessControl.addEventListener('input', (e) => {
            const value = e.target.value;
            document.body.style.filter = `brightness(${value})`;
            console.log('Brillo:', value);
        });

        // Reproducir música al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            audio.play().catch((error) => {
                console.error('Error al reproducir la música:', error);
            });
        });
    </script>
     <script src="script.js"></script>
</body>
</html>