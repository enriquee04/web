<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "proyecto";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Operación de agregar un usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre"]) && isset($_POST["correo"]) && isset($_POST["telefono"]) && isset($_POST["direccion"])) {
    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $telefono = $_POST["telefono"];
    $direccion = $_POST["direccion"];

    $sql = "INSERT INTO usuarios (nombre, correo, telefono, direccion) VALUES ('$nombre', '$correo', '$telefono', '$direccion')";
    if ($conn->query($sql) === TRUE) {
        echo "Usuario agregado exitosamente.";
    } else {
        echo "Error al agregar el usuario: " . $conn->error;
    }
}

// Operación de eliminar un usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar"])) {
    $idEliminar = $_POST["eliminar"];

    $sqlEliminar = "DELETE FROM usuarios WHERE id=$idEliminar";

    if ($conn->query($sqlEliminar) === TRUE) {
        echo "Usuario eliminado exitosamente.";
    } else {
        echo "Error al eliminar el usuario: " . $conn->error;
    }
}

// Verificar si se ha enviado el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];
    $nuevoNombre = $_POST["nuevo_nombre"];
    $nuevoCorreo = $_POST["nuevo_correo"];
    $nuevoTelefono = $_POST["nuevo_telefono"];
    $nuevaDireccion = $_POST["nueva_direccion"];

    $sql = "UPDATE usuarios SET nombre='$nuevoNombre', correo='$nuevoCorreo', telefono='$nuevoTelefono', direccion='$nuevaDireccion' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Los cambios se guardaron correctamente.";
    } else {
        echo "Error al actualizar el usuario: " . $conn->error;
    }
}

// Obtener la lista de usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

$usuarios = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agenda Contactos</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        max-width: 800px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    form {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    form label {
        flex: 0 0 48%;
        margin-bottom: 10px;
    }
    form input[type="text"],
    form input[type="number"] {
        width: calc(100% - 10px);
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    form button[type="submit"] {
        width: 100%;
        padding: 10px;
        background-color: #3E3E33;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    form button[type="submit"]:hover {
        background-color: #3E3E33;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
        margin-top: 20px;
    }
    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    table th {
        background-color: #3E3E33;
    }
    .edit-form {
        display: flex;
        align-items: center;
    }
    .edit-form input[type="text"],
    .edit-form input[type="number"] {
        margin-right: 10px;
        padding: 6px;
    }
    .edit-form button {
        padding: 6px 10px;
        background-color: #3E3E33;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .edit-form button:hover {
        background-color: #3E3E33;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Agenda Contactos</h2>

    <form method="post" action="crud.php" onsubmit="return validarCampos()">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="correo">Correo:</label>
        <input type="text" id="correo" name="correo" required>

        <label for="telefono">Telefono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <label for="direccion">Direccion:</label>
        <input type="text" id="direccion" name="direccion" required>

        <button type="submit">Agregar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Telefono</th>
                <th>Direccion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario["nombre"]; ?></td>
                    <td><?php echo $usuario["correo"]; ?></td>
                    <td><?php echo $usuario["telefono"]; ?></td>
                    <td><?php echo $usuario["direccion"]; ?></td>
                    <td class="edit-form">
                        <!-- Botón para abrir modal de edición -->
                        <button onclick="abrirModalEditar(<?php echo $usuario['id']; ?>)">Editar</button>
                        <!-- Formulario para eliminar usuario -->
                        <form method="post" action="crud.php">
                            <input type="hidden" name="eliminar" value="<?php echo $usuario["id"]; ?>">
                            <button type="submit">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para editar usuario -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <!-- Botón para cerrar el modal -->
        <span class="close" onclick="cerrarModalEditar()">&times;</span>
        <h2>Editar Usuario</h2>
        <form id="formEditar" method="post" action="crud.php">
            <!-- Campos de edición -->
        </form>
    </div>
</div>

<script>
    // Función para abrir el modal de edición
    function abrirModalEditar(id) {
        // Obtener el usuario con el ID correspondiente (puedes usar AJAX para esto)
        // Llenar los campos del formulario con los valores del usuario
        // Ejemplo:
        document.getElementById('formEditar').innerHTML = `
            <input type="hidden" name="id" value="${id}">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nuevo_nombre" name="nuevo_nombre" value="<?php echo $usuario['nombre']; ?>">
            <label for="correo">Correo:</label>
            <input type="text" id="nuevo_correo" name="nuevo_correo" value="<?php echo $usuario['correo']; ?>">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="nuevo_telefono" name="nuevo_telefono" value="<?php echo $usuario['telefono']; ?>">
            <label for="direccion">Dirección:</label>
            <input type="text" id="nueva_direccion" name="nueva_direccion" value="<?php echo $usuario['direccion']; ?>">
            <button type="submit">Guardar Cambios</button>
        `;
        // Mostrar el modal
        document.getElementById('modalEditar').style.display = "block";
    }

    // Función para cerrar el modal de edición
    function cerrarModalEditar() {
        document.getElementById('modalEditar').style.display = "none";
    }
</script>

</body>
</html>
