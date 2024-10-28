<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

// Procesar la creación de un nuevo personaje
if (isset($_POST['add_character'])) {
    $name = $_POST['name'];
    $race = $_POST['race'];
    $avatar = $_POST['avatar']; // Avatar generado o elegido por el estudiante

    $user_id = $_SESSION['user_id'];

    // Insertar el nuevo personaje en la base de datos
    $query = $pdo->prepare("
        INSERT INTO characters (user_id, name, race, avatar, strength, speed, endurance, level, experience)
        VALUES (:user_id, :name, :race, :avatar, 50, 50, 50, 1, 0)
    ");
    $query->execute([
        'user_id' => $user_id,
        'name' => $name,
        'race' => $race,
        'avatar' => $avatar
    ]);

    // Redirigir a la lista de personajes
    header('Location: listCharacters.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Crear Personaje</title>
    <script src="random.js" defer></script>
    <style>
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
    animation: background-pulse 10s infinite alternate; 
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
#background-audio {
    display: none; /* El audio es invisible en la página */
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
.create-character-container {
    opacity: 0;
    transform: scale(0.8) translateY(-100%);
    width: 400px;
    padding: 40px;
    background: linear-gradient(145deg, rgba(15, 15, 35, 0.9), rgba(30, 30, 50, 0.95));
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0, 255, 255, 0.5), 0 0 50px rgba(0, 255, 255, 0.3);
    border: 2px solid rgba(0, 255, 255, 0.7);
    text-align: center;
    transition: opacity 1.5s, transform 1.5s;
}
.create-character-container h1 {
    font-size: 28px;
    color: #00ffcc;
    text-shadow: 0 0 15px #00ffcc, 0 0 30px #00ffff;
    margin-bottom: 20px;
}

.show {
    display: block;
    animation: sparkingZeroAnimation 1.5s ease-out forwards;
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
<body>
<button id="toggleMusic">🔊 Reproducir Música</button>

<audio id="backgroundAudio" loop>
    <source src="musica/official-opening-movie.mp3" type="audio/mp3">
    Tu navegador no soporta audio HTML5.
</audio>
<div class="scrollable-container">
<div class="create-character-container" id="createCharacterContainer">
    <h1>Crear un Nuevo Personaje</h1>
    <form method="POST">
        <label for="name">Nombre del personaje:</label>
        <input type="text" id="name" name="name" required>

        <label for="race">Raza:</label>
        <select id="race" name="race" required>
            <option value="Saiyan">Saiyan</option>
            <option value="Namek">Namek</option>
            <option value="Humano">Humano</option>
            <option value="Freezer Race">Freezer Race</option>
            <option value="Majin">Majin</option>
        </select>

        <input type="hidden" id="avatar" name="avatar">
        <div class="avatar-preview" id="avatarPreview"></div>

        <button type="submit" name="add_character">Agregar Personaje</button>
    </form>

    <form action="dashboardCharacters.php" method="get">
        <button type="submit">Regresar</button>
    </form>
</div>
        </form>
    </div>
    <script>
        const createCharacterContainer = document.getElementById('createCharacterContainer');

// Activar la animación al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('Mostrando el contenedor de creación de personaje');
    createCharacterContainer.classList.add('show');
});
    </script>
    <script src="script.js"></script>

</body>
</html>
