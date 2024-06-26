<?php 

$jsonFile = "archivo.json";

if (file_exists($jsonFile)) {
    $jsonClientes = file_get_contents($jsonFile);
    $aClientes = json_decode($jsonClientes, true);
} else {
    $aClientes = [];
}

$pos = -1;
$dni = "";
$nombre = "";
$telefono = "";
$correo = "";

if ($_POST) {
    $pos = $_POST["pos"] ?? -1;

    if (isset($_POST["btnEditar"])) {
        if ($pos >= 0) {
            $dni = $aClientes[$pos]["dni"];
            $nombre = $aClientes[$pos]["nombre"];
            $telefono = $aClientes[$pos]["telefono"];
            $correo = $aClientes[$pos]["correo"];
        }
    } else {
        if (isset($_POST["btnEliminar"])) {
            if ($pos >= 0) {
                if (file_exists("imagenes/" . $aClientes[$pos]["imagen"])) {
                    unlink("imagenes/" . $aClientes[$pos]["imagen"]);
                }
                unset($aClientes[$pos]);
            }
            header("Location: index.php");
        } else {
            $dni = trim($_POST["txtDNI"] ?? "");
            $nombre = trim($_POST["txtNombre"] ?? "");
            $telefono = trim($_POST["txtTelefono"] ?? "");
            $correo = trim($_POST["txtCorreo"] ?? "");
            $imagen = "";

            if (isset($_FILES["file1"]) && $_FILES["file1"]["error"] === UPLOAD_ERR_OK) {
                $nombreAleatorio = date("Ymdhmsi"). rand(1000, 2000); //202404180659371010
                $tmpFile = $_FILES["file1"]["tmp_name"];
                $extension = strtolower(pathinfo($_FILES["file1"]["name"], PATHINFO_EXTENSION));
                if($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                    $imagen = "$nombreAleatorio.$extension";
                    move_uploaded_file($tmpFile, "imagenes/$imagen");

                    if ($pos >= 0) {
                        //Eliminar anterior
                        if (file_exists("imagenes/" . $aClientes[$pos]["imagen"])) {
                            unlink("imagenes/" . $aClientes[$pos]["imagen"]);
                        }
                    }
                }
            }

            if ($pos >= 0) {
                //Actualizar
                $aClientes[$pos] = [
                    "dni" => $dni,
                    "nombre" => $nombre,
                    "telefono" => $telefono,
                    "correo" => $correo,
                    "imagen" => $imagen == "" ? $aClientes[$pos]["imagen"] : $imagen
                ];
            } else {
                //Insertar
                $aClientes[] = [
                    "dni" => $dni,
                    "nombre" => $nombre,
                    "telefono" => $telefono,
                    "correo" => $correo,
                    "imagen" => $imagen
                ];
            }
        }

        $jsonClientes = json_encode($aClientes);
        file_put_contents($jsonFile, $jsonClientes);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Clientes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha256-PI8n5gCcz9cQqQXm3PEtDuPG8qx9oFsFctPg0S5zb8g=" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/6d7e9baaed.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="container">
    <div class="row">
        <div class="col-12 text-center my-3">
            <h1>Registro de clientes</h1>
        </div>
        <div class="col-4">
            <form action="" method="post" class="form" enctype="multipart/form-data">
                <input type="hidden" name="pos" value="<?php echo $pos; ?>">
                <div class="mb-1">
                    <label for="txtDNI">DNI: *</label>
                    <input type="number" name="txtDNI" id="txtDNI" class="form-control" value="<?php echo $dni; ?>" required>
                </div>
                <div class="mb-1">
                    <label for="txtNombre">Nombre: *</label>
                    <input type="text" name="txtNombre" id="txtNombre" class="form-control" value="<?php echo $nombre; ?>" required>
                </div>
                <div class="mb-1">
                    <label for="txtTelefono">Teléfono:</label>
                    <input type="text" name="txtTelefono" id="txtTelefono" class="form-control" value="<?php echo $telefono; ?>">
                </div>
                <div class="mb-1">
                    <label for="txtCorreo">Correo: *</label>
                    <input type="email" name="txtCorreo" id="txtCorreo" class="form-control" value="<?php echo $correo; ?>" required>
                </div>
                <div class="mb-1">
                    <label for="file1">Archivo adjunto</label>
                    <input type="file" name="file1" id="file1" accept=".png, .jpeg, .png">
                    <p><small>Archivos admiditos: .jpg, .jpeg, .png</small></p>
                </div>
                <?php if($pos >= 0): ?>
                    <input type="submit" value="Actualizar" name="btnGuardar" class="btn btn-primary">
                    <a href="." class="btn btn-danger">NUEVO</a>
                <?php else: ?>
                    <input type="submit" value="Guardar" name="btnGuardar" class="btn btn-primary">
                <?php endif; ?>
            </form>
        </div>
        <div class="col-8">
            <table class="table-hover table border">
                <thead>
                    <tr>
                        <th scole="col">Imagen</th>
                        <th scole="col">DNI</th>
                        <th scole="col">Nombre</th>
                        <th scole="col">Teléfono</th>
                        <th scole="col">Correo</th>
                        <th scole="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($aClientes as $id => $cliente): ?>
                    <tr>
                        <td>
                            <img class="img-fluid" src="imagenes/<?php echo $cliente["imagen"]; ?>" alt="imagen">
                        </td>
                        <td><?php echo $cliente["dni"]; ?></td>
                        <td><?php echo $cliente["nombre"]; ?></td>
                        <td><?php echo $cliente["telefono"]; ?></td>
                        <td><?php echo $cliente["correo"]; ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="pos" value="<?php echo $id; ?>">
                                <input type="submit" value="Editar" class="btn btn-link" name="btnEditar">
                                <input type="submit" value="Eliminar" class="btn btn-link" name="btnEliminar">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>