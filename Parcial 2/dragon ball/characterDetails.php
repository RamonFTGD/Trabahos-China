<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

// Obtener el ID del personaje seleccionado
$character_id = $_GET['character_id'];

// Obtener los detalles del personaje
$query = $pdo->prepare("SELECT * FROM characters WHERE id = :character_id AND user_id = :user_id");
$query->execute(['character_id' => $character_id, 'user_id' => $_SESSION['user_id']]);
$character = $query->fetch();

if (!$character) {
    echo "Personaje no encontrado.";
    exit();
}

// Obtener las transformaciones desbloqueadas del personaje
$query = $pdo->prepare("
    SELECT t.name 
    FROM transformations t
    JOIN character_transformations ct ON t.id = ct.transformation_id
    WHERE ct.character_id = :character_id
");
$query->execute(['character_id' => $character_id]);
$unlocked_transformations = $query->fetchAll(PDO::FETCH_COLUMN);

// Obtener todas las transformaciones que el personaje podría desbloquear
$query = $pdo->prepare("
    SELECT * 
    FROM transformations
    WHERE level_required <= :level
    AND id NOT IN (
        SELECT transformation_id FROM character_transformations WHERE character_id = :character_id
    )
");
$query->execute(['level' => $character['level'], 'character_id' => $character_id]);
$available_transformations = $query->fetchAll();

// Desbloquear las transformaciones cuando se alcanza el nivel requerido
if (isset($_POST['unlock_transformation'])) {
    $transformation_id = $_POST['unlock_transformation'];
    
    // Insertar la nueva transformación en la tabla character_transformations
    $query = $pdo->prepare("
        INSERT INTO character_transformations (character_id, transformation_id)
        VALUES (:character_id, :transformation_id)
    ");
    $query->execute(['character_id' => $character_id, 'transformation_id' => $transformation_id]);

    // Recargar la página para mostrar las transformaciones actualizadas
    header("Location: characterDetails.php?character_id=$character_id");
    exit();
}

// Función para entrenar al personaje (aumentar atributos)
if (isset($_POST['train'])) {
    $strengthGain = rand(1, 5);
    $speedGain = rand(1, 5);
    $enduranceGain = rand(1, 5);

    //Incremento de nivel
    $levelUp = $character['level'] + 1;

    $query = $pdo->prepare("
        UPDATE characters 
        SET strength = strength + :strength, speed = speed + :speed, endurance = endurance + :endurance,level = :level
        WHERE id = :character_id
    ");
    $query->execute([
        'strength' => $strengthGain,
        'speed' => $speedGain,
        'endurance' => $enduranceGain,
        'level' => $levelUp,
        'character_id' => $character_id
    ]);

    // Recargar los detalles del personaje después del entrenamiento
    header("Location: characterDetails.php?character_id=$character_id");
    exit();
}

// Función para eliminar el personaje
if (isset($_POST['delete'])) {
    //Eliminar de la tabla referenciada
    $query = $pdo->prepare("DELETE FROM character_transformations WHERE character_id = :character_id");
    $query->execute(['character_id' => $character_id]);

    $query = $pdo->prepare("DELETE FROM characters WHERE id = :character_id AND user_id = :user_id");
    $query->execute(['character_id' => $character_id, 'user_id' => $_SESSION['user_id']]);

    // Redirigir a la lista de personajes después de eliminar
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
    <title>Detalles del Personaje</title>
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
        .character-details-container {
    opacity: 0;
    transform: scale(0.8) translateY(-100%);
    width: 450px;
    padding: 40px;
    background: linear-gradient(145deg, rgba(15, 15, 35, 0.9), rgba(30, 30, 50, 0.95));
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0, 255, 255, 0.5), 0 0 50px rgba(0, 255, 255, 0.3);
    border: 2px solid rgba(0, 255, 255, 0.7);
    text-align: center;
    transition: opacity 1.5s, transform 1.5s;
}

/* Título */
.character-details-container h1 {
    font-size: 28px;
    color: #00ffcc;
    text-shadow: 0 0 15px #00ffcc, 0 0 30px #00ffff;
    margin-bottom: 20px;
}

/* Subtítulos */
.character-details-container h2 {
    font-size: 24px;
    color: #00ffcc;
    text-shadow: 0 0 10px #00ffcc;
    margin: 15px 0;
}
.character-details-container p {
    color: #ADD8E6;
}

.characters-details-container a:hover {
    color: #00ffcc;
    text-shadow: 0 0 10px #00ffff;
}

/* Lista de Transformaciones */
.character-details-container ul {
    list-style: none;
    padding: 0;
    margin: 10px 0;
}

.character-details-container li {
    background: rgba(0, 0, 0, 0.5);
    margin-bottom: 10px;
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
}
.character-details-container img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid #00ffff;
    box-shadow: 0 0 15px #00ffcc;
    margin-top: 15px;
}

/* Mostrar el contenedor con animación */
.show {
    display: block;
    animation: sparkingZeroAnimation 1.5s ease-out forwards;
}

/* Animación tipo Sparking Zero */
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
<div class="character-details-container" id="characterDetailsContainer">
    <h1>Detalles del Personaje</h1>

    <img src="avatars/<?php echo $character['avatar']; ?>" alt="Avatar de personaje">
    <p><strong>Nombre:</strong> <?php echo $character['name']; ?></p>
    <p><strong>Raza:</strong> <?php echo $character['race']; ?></p>
    <p><strong>Nivel:</strong> <?php echo $character['level']; ?></p>
    <p><strong>Fuerza:</strong> <?php echo $character['strength']; ?></p>
    <p><strong>Velocidad:</strong> <?php echo $character['speed']; ?></p>
    <p><strong>Resistencia:</strong> <?php echo $character['endurance']; ?></p>

    <h2>Transformaciones Desbloqueadas</h2>
    <?php if (count($unlocked_transformations) > 0): ?>
        <ul>
            <?php foreach ($unlocked_transformations as $transformation): ?>
                <li><?php echo $transformation; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No has desbloqueado ninguna transformación aún.</p>
    <?php endif; ?>

    <h2>Transformaciones Disponibles</h2>
    <?php if (count($available_transformations) > 0): ?>
        <form method="POST">
            <ul>
                <?php foreach ($available_transformations as $transformation): ?>
                    <li>
                        <?php echo $transformation['name']; ?> 
                        (Requiere nivel <?php echo $transformation['level_required']; ?>)
                        <button type="submit" name="unlock_transformation" value="<?php echo $transformation['id']; ?>">
                            Desbloquear
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </form>
    <?php else: ?>
        <p>No tienes transformaciones disponibles para desbloquear.</p>
    <?php endif; ?>

    <form method="POST" style="display:inline;">
        <button type="submit" name="train">Entrenar Personaje</button>
    </form>

    <form method="POST" style="display:inline;">
        <button type="submit" name="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este personaje?');">
            Eliminar Personaje
        </button>
    </form>

    <form action="listCharacters.php" method="get">
        <button type="submit">Regresar a la Lista de Personajes</button>
    </form>
</div>
</div>
 <script>
    const characterDetailsContainer = document.getElementById('characterDetailsContainer');

// Activar la animación al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('Mostrando los detalles del personaje');
    characterDetailsContainer.classList.add('show');
});

 </script>
    <script src="script.js"></script>

</body>
</html>
