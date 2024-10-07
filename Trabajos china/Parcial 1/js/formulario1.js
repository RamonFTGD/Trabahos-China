document.getElementById('enviar').addEventListener('click', function(event) {

    const nombre = document.getElementById('nombre').value;
    const apellido = document.getElementById('apellido').value;
    const telefono = document.getElementById('telefono').value;
    const correo = document.getElementById('correo').value;



    if (!nombre || !apellido || !telefono || !correo) {
        alert('Por favor, completa todos los campos.');
        return;
    }


    alert('¡Datos enviados!');
});
