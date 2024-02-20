<?php
// Incluir la clase stackAPI para interactuar con la API de Pexels.
require_once("./stackAPI.class.php");
// Crear una instancia de la clase stackAPI.
$stacks = new stackAPI();
// Obtener el término de búsqueda desde la URL, si existe, o dejarlo en blanco si no.
$search = $_GET['search'] ?? "";
// Obtener el número de página desde la URL, o usar 1 como predeterminado.
$page = $_GET['page'] ?? 1;

// Si se ha proporcionado un término de búsqueda, buscar imágenes relacionadas.
// De lo contrario, obtener una selección curada de imágenes.
if (!empty($search))
    $get_image = $stacks->get_stack('search', ['per_page' => 40, "query" => $search, "page" => $page]);
else
    $get_image = $stacks->get_stack('curated', ['per_page' => 40, "page" => $page]);

// Calcular el número total de páginas basado en el total de resultados y el número de resultados por página.
$pages = ceil(($get_image['result']['total_results'] ?? 1) / 40);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal de Imágenes</title>
    <link rel="shortcut icon" href="./faviconconfiguroweb.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
    <style>
        /* Estilos personalizados para la cuadrícula de imágenes y el modal */
        .media-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .media-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        .modal {
            position: fixed;
            z-index: 2;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            position: relative;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            background-color: #fff;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 25px;
            color: #000;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        #downloadButton {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            cursor: pointer;
        }

        #scriptMessage {
            margin-top: 20px;
            /* Espacio por encima del mensaje */
            padding: 10px;
            background-color: #ffdd57;
            /* Fondo amarillo para llamar la atención */
            color: #000;
            /* Texto oscuro para contraste */
            font-weight: bold;
            /* Hacer el texto más grueso */
            border-radius: 5px;
            /* Bordes redondeados para suavizar */
            text-align: center;
            /* Centrar el texto */
        }

        #scriptMessage a {
            color: #000;
            /* Color del enlace */
            text-decoration: underline;
            /* Subrayar para destacar que es un enlace */
        }
    </style>
</head>

<body>
    <!-- Navegación del sitio. -->
    <nav class="container-fluid">
        <ul>
            <li><strong>Portal de Imágenes de Pexels</strong></li>
        </ul>
        <ul>
            <li><a href="https://www.youtube.com/channel/UCVnrMbml3wUIuwz-oSaIUnA">Youtube</a></li>
            <li><a href="https://github.com/configuroweb">GitHub</a></li>
            <li><a href="https://www.configuroweb.com/" role="button">ConfiguroWeb</a></li>
        </ul>
    </nav>
    <main class="container">
        <div class="grid">
            <section>
                <hgroup>
                    <h2>Portal de Imágenes ConfiguroWeb</h2>
                    <h3>Colección de Imágenes Gratuitas</h3>
                </hgroup>
                <p>Las mejores fotos de stock gratis, imágenes libres de regalías y vídeos compartidos por creadores.</p>
                <div id="searchbox-container" class="py-4">
                    <form action="./" method="get">
                        <input type="search" name="search" id="search" placeholder="Introduce lo que estés buscando aquí" class="form-control" value="<?= htmlspecialchars($search) ?>">
                        <button type="submit">Buscar</button>
                    </form>
                </div>
                <div class="media-container">
                    <?php if ($get_image['status'] == 'success' && isset($get_image['result']['photos']) && !empty($get_image['result']['photos'])) : ?>
                        <?php foreach ($get_image['result']['photos'] as $photo) : ?>
                            <div class="media-item">
                                <img src="<?= $photo['src']['medium'] ?>" alt="<?= $photo['alt'] ?>" style="cursor: pointer;">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="pagination">
                    <?php if ($page != 1) : ?>
                        <a href="./?<?= http_build_query(['search' => $search, 'page' => $page - 1]) ?>">Anterior</a>
                    <?php endif; ?>
                    <span><?= "{$page}/{$pages}" ?></span>
                    <?php if ($page < $pages) : ?>
                        <a href="./?<?= http_build_query(['search' => $search, 'page' => $page + 1]) ?>">Siguiente</a>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
    <div id="imageModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <img id="modalImage" src="" alt="Imagen en Tamaño Completo">
            <p id="imageInfo"><span id="imageAuthor"></span></p>
            <!-- Nuevo mensaje aquí -->
            <div id="scriptMessage">
                Este script es uno de los más de 150 códigos que tengo en mi página web <a href="https://configuroweb.com" target="_blank">configuroweb.com</a>
            </div>
            <button id="downloadButton">Descargar Imagen</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.media-item img').forEach(item => {
                item.addEventListener('click', function() {
                    const modal = document.getElementById('imageModal');
                    const modalImg = document.getElementById('modalImage');
                    const downloadBtn = document.getElementById('downloadButton');
                    modal.style.display = "block";
                    modalImg.src = this.src; // Establece la imagen seleccionada en el modal
                    downloadBtn.onclick = function() { // Función de descarga
                        const a = document.createElement('a');
                        a.href = modalImg.src;
                        a.download = 'Descarga.jpg'; // Nombre del archivo de descarga
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    };
                });
            });

            // Cerrar el modal
            document.querySelector('.close-button').addEventListener('click', function() {
                document.getElementById('imageModal').style.display = "none";
            });
        });
    </script>
</body>

</html>