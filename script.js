// Función asincrónica para descargar imágenes.
async function download_img($url, $fname=""){
    // Obtener la imagen desde la URL proporcionada.
    const _image = await fetch($url);
    // Convertir la imagen a un blob.
    const blob = await _image.blob();
    // Crear una URL a partir del blob.
    const url = window.URL.createObjectURL(blob);
    // Crear un elemento <a> para descargar la imagen.
    const link = document.createElement("a");
    link.href = url;
    link.download = $fname; // Establecer el nombre de archivo para la descarga.
    link.click(); // Simular un clic para iniciar la descarga.
}

// Esperar a que el documento esté listo.
$(document).ready(function(){
    // Establecer un controlador de clic para los elementos con clase .media-item.
    $('.media-item').click(function(e){
        e.preventDefault(); // Prevenir el comportamiento predeterminado.

        // Referencia al modal de vista previa y extracción de datos necesarios del elemento clickeado.
        var prevModal = $('#media-preview');
        var key = $(this)[0].dataset.key;
        var fname = $(this)[0].dataset.filename;

        // Mostrar la imagen seleccionada en el modal.
        prevModal.find('#preview-media').html(`
            <img src="${__stacked[key]['src']['large']}" />
        `);

        // Mostrar el nombre del fotógrafo en el modal.
        prevModal.find('#phtotographer').text(`${__stacked[key]['photographer']}`);

        // Preparar contenedor para enlaces de descarga.
        var dlContainer = $('#downloads');
        dlContainer.html(''); // Limpiar contenedor previo.
        var sizes = {
            original: "Original",
            large2x: "2x Larger",
            large: "Large",
            medium: "Medium",
            small: "Small",
            portrait: "Portrait",
            landscape: "Landscape",
            tiny: "Tiny"
        };

        // Generar enlaces de descarga para cada tamaño disponible.
        Object.keys(__stacked[key]['src']).forEach(k=>{
            var _url = __stacked[key]['src'][k];
            var dlItem = $(`<a class="btn btn-sm btn-primary d-block rounded-pill mb-2 download-item" href="${_url}"><strong>${sizes[k]}</strong></a>`);
            dlContainer.append(dlItem);
            // Establecer evento de clic para descargar la imagen seleccionada.
            dlItem.click(function(e){
                e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace.
                download_img(_url, fname); // Llamar a la función de descarga.
            });
        });

        // Mostrar el modal de vista previa.
        prevModal.modal('show');
    });

    // Controlador de eventos para la entrada de búsqueda.
    $('#search').on('keypress', function(e){
        if(e.which == 13 || e.keyCode == 13 || e.key == "Enter" ){
            var uri = new URL(location.href);
            var searchParams = uri.searchParams;
            // Eliminar el parámetro de búsqueda existente si lo hay.
            if(searchParams.has('search'))
                searchParams.delete('search');
            // Añadir el valor de búsqueda actual a los parámetros de la URL.
            searchParams.set('search', $(this).val());
            // Redirigir a la URL actualizada.
            location.href = uri.toString();
        }
    });
});

// Controlador de eventos para mostrar el modal al hacer clic en las imágenes.
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.media-item img').forEach(item => {
        item.addEventListener('click', function() {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadButton');
            modal.style.display = "block";
            modalImg.src = this.src; // Establecer la imagen en el modal.
            // Establecer el evento de clic para el botón de descarga.
            downloadBtn.onclick = function() {
                const a = document.createElement('a');
                a.href = modalImg.src;
                a.download = 'Descarga.jpg'; // Nombre del archivo de descarga.
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            };
        });
    });

    // Cerrar el modal al hacer clic en el botón de cierre.
    document.querySelector('.close-button').addEventListener('click', function() {
        document.getElementById('imageModal').style.display = "none";
    });
});
