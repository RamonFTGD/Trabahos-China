<?php
session_start();
if (isset($_POST['login'])) {
    // Aquí iría la conexión a la base de datos (ejemplo con MySQL)
    include 'db.php'; // Archivo de conexión a la base de datos

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta a la base de datos para verificar el usuario
    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    $user = $query->fetch();

    // Verificación de contraseña    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Inicio de Sesión - Torneo de Artes Marciales</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap'); /* Fuente futurista */

/* Estilo Global */
body {
    font-family: 'Orbitron', sans-serif;
    background-image: url('backgrounds/sparking_zero_bg.jpg');
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
#background-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ajusta el video sin distorsión */
            z-index: -1; /* Detrás del contenido */
        }
/* Animación de fondo */
@keyframes background-pulse {
    0% { filter: brightness(1); }
    100% { filter: brightness(1.3); }
}

/* Contenedor registro */
 .register-container {
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
          
          .login-container {
            border: 2px solid rgba(0, 255, 255, 0.7); /* Borde luminoso */
            border-radius: 12px;
            width: 350px;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.7);
            animation: glow 2s infinite alternate; /* Efecto de brillo */
            display: none; 
            background: rgba(0, 0, 0, 0.85);
            border: 2px solid rgba(0, 255, 255, 0.7);
            text-align: center;
            z-index: 1;
            opacity: 0; 
            transform: scale(0.8) translateY(-100%);
            transition: opacity 0.8s, transform 0.8s; 
        }

        
        .subtitle {
            position: absolute;
            bottom: 20px;
            color: white;
            font-size: 18px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
            z-index: 1;
        }

       
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


.show {
    display: block;
    animation: sparkingZeroAnimation 1.5s ease-out forwards;
}

    </style>
</head>
<body>
    <button id="toggleMusic">🔊 Reproducir Música</button>

    <audio id="backgroundAudio" loop>
        <source src="musica/official-opening-movie.mp3" type="audio/mp3">
        Tu navegador no soporta audio HTML5.
    </audio>

    <div class="login-container" id="loginContainer">
        <h2>Iniciar Sesión</h2>
        <form method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    </div>

    <div class="subtitle" id="subtitle">Presiona la tecla X para omitir</div>

    <script>
        const loginContainer = document.getElementById('loginContainer');
        const subtitle = document.getElementById('subtitle');
        const backgroundAudio = document.getElementById('backgroundAudio');

        // Escuchar evento de clic en el botón
        document.getElementById('toggleMusic').addEventListener('click', () => {
            // Reproducir o pausar la música
            if (backgroundAudio.paused) {
                backgroundAudio.play(); // Reproduce la música
                backgroundAudio.loop = true; // Habilita el loop
                console.log('Música iniciada');
            } else {
                backgroundAudio.pause(); // Pausa la música
                console.log('Música pausada');
            }
        });

        // Escuchar evento de tecla presionada
        document.addEventListener('keydown', (event) => {
            if (event.key === 'x' || event.key === 'X') {
                console.log('Mostrando contenedor con animación'); // Depuración
                loginContainer.style.display = 'block'; // Ahora está visible
                requestAnimationFrame(() => {
                    loginContainer.classList.add('show');
                });
                // Ocultar el subtítulo
                subtitle.style.display = 'none';
            }
        });
    </script>
</body>
</html>