<?php
$carpetaNombre = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '';
$carpetaRuta = "./descarga/" . $carpetaNombre;
$mensaje = '';
try {
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
        $mensaje = "Carpeta '$carpetaNombre' creada con éxito.";
    } else {
        $mensaje = "La carpeta '$carpetaNombre' ya existe.";
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivo'])) {
            foreach ($_FILES['archivo']['tmp_name'] as $key => $tmp_name) {
                $archivo = $_FILES['archivo']['name'][$key];
                // Replace spaces with underscores
                $archivo = str_replace(' ', '_', $archivo);
                if (move_uploaded_file($tmp_name, $carpetaRuta . '/' . $archivo)) {
                    $mensaje = "Archivo '$archivo' subido con éxito.";
                } else {
                    throw new Exception("Error al subir el archivo '$archivo'.");
                }
            }
        }
    }
    if (isset($_POST['eliminarArchivo'])) {
        $archivoAEliminar = $_POST['eliminarArchivo'];
        $archivoRutaAEliminar = $carpetaRuta . '/' . $archivoAEliminar;
        if (file_exists($archivoRutaAEliminar)) {
            if (unlink($archivoRutaAEliminar)) {
                $mensaje = "Archivo '$archivoAEliminar' eliminado con éxito.";
            } else {
                throw new Exception("Error al eliminar el archivo.");
            }
        } else {
            throw new Exception("El archivo '$archivoAEliminar' no existe.");
        }
    }
} catch (Exception $e) {
    $mensaje = "Error: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPLOAD</title>
    <style>
        body {
            background-image: url('fondo.jpg');
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat; 
            height: 100vh; 
            margin: 0; 
        }
    </style>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h1> Comparte videos,<br> musica y mas<sup class="beta"> BETA </sup></h1>
    <div class="content">
        <center>
            <h3 class="SUBE">Enlace temporal: <span id="shareLink"><br>websitestudio.site<?php echo $carpetaNombre;?></span></h3>
            <button id="copyButton" onclick="copyToClipboard()">Copiar Enlace</button>
        </center>
        <div class="container">
            <div class="drop-area" id="drop-area">
                <form action="" id="form" method="POST" enctype="multipart/form-data">
                    <input type="file" class="file-input" name="archivo[]" id="archivo" multiple>
                    <label for="archivo">Arrastra tus archivos aquí<br>o selecciona archivos</label>
                    <button type="submit" class="upload-btn">Subir Archivos</button>
                </form>
                <div class="progress-bar" id="progressBar" style="display:none;">
                    <div class="progress" id="progress"></div>
                </div>
            </div>
            <div class="uploaded-files">
                <h3>Archivos Subidos:</h3>
                <div id="file-list" class="pila">
                    <?php
                    $targetDir = $carpetaRuta;
                    $files = scandir($targetDir);
                    $files = array_diff($files, array('.', '..'));
                    if (count($files) > 0) {
                        foreach ($files as $file) {
                            echo "<div class='archivos_subidos'>
                            <div><a href='$carpetaRuta/$file' download class='boton-descargar'>$file</a></div>
                            <div>
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminarArchivo' value='$file'>
                                <button type='submit' class='btn_delete'>Eliminar</button>
                            </form>
                        </div>
                        </div>";
                        }
                    } else {
                        echo "No se han subido archivos.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="upload.js"></script>
    <script>
        function copyToClipboard() {
            const shareLink = document.getElementById('shareLink').innerText;
            navigator.clipboard.writeText(shareLink).then(() => {
                alert('Enlace copiado al portapapeles!');
            }).catch(err => {
                console.error('Error al copiar el enlace: ', err);
            });
        }
    </script>
</body>
</html>