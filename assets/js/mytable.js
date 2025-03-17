/**
 * Created by Stuart on 23/7/2018.
 */

document.addEventListener("DOMContentLoaded", function() {
    $('#example2').DataTable({
        retrieve: true,
        paging: true,       // Habilita paginación
        ordering: true,
        order: [[6, "desc"]],
        lengthMenu: [20, 40, 60], // Opciones de cantidad de filas por página
        pageLength: 20,      // Número de filas por defecto
        language: {
            processing: "En curso...",
            search: "Buscar:",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
        },
        layout: {
            topStart: {
                buttons: ['csv', 'excel']
            }
        }

    });


});






// Obtener los elementos
var modal = document.getElementById("myModal");
var modalImg = document.getElementById("img01");
var span = document.getElementsByClassName("close")[0];

// Función para abrir el modal y enviar el parámetro de la imagen
function openModal(imageUrl) {
    modal.style.display = "block";
    modalImg.src = imageUrl;
}

// Cuando el usuario haga clic en <span> (x), cierra el modal
span.onclick = function () {
    modal.style.display = "none";
}

// Cuando el usuario haga clic fuera del modal, cierra el modal
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}



