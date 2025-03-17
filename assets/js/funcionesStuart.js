function NOTenviarDatos(id, fecha) {
    // Construir el objeto de datos a partir de los parámetros
    const params = new URLSearchParams();
    params.append('id', id);
    params.append('fecha', fecha);


    // Realizar la solicitud POST
    fetch('/lisOne/public/pga/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString(),
    })
        .then(response => response.text())
        .then(data => alert('Respuesta del servidor: ' + data))
        .catch(error => console.error('Error:', error));
}






function enviaDatos1 (id, fecha) {
    var theForm, newInput1, newInput2;
    // Start by creating a <form>
    theForm = document.createElement('form');
    theForm.action = 'pga/';
    theForm.method = 'post';
    // Next create the <input>s in the form and give them names and values
    newInput1 = document.createElement('input');
    newInput1.type = 'hidden';
    newInput1.name = 'id';
    newInput1.value = id;
    newInput2 = document.createElement('input');
    newInput2.type = 'hidden';
    newInput2.name = 'fecha';
    newInput2.value = fecha;
    // Now put everything together...
    theForm.appendChild(newInput1);
    theForm.appendChild(newInput2);
    // ...and it to the DOM...
    document.getElementById('hidden_form_container').appendChild(theForm);
    // ...and submit it
    theForm.submit();
}


function epicentro(lat,lon){

    var puntoInicial = {
        lat: lat,
        lon: lon
    };
    alert(puntoInicial);
    let ciudadCercana = calcularCiudadMasCercana(puntoInicial);
    let direccion = obtenerDireccion(puntoInicial.lat, puntoInicial.lon, ciudadCercana.lat, ciudadCercana.lon);

    //document.getElementById("epicentro").textContent =
      //  ciudadCercana.distancia.toFixed(2) + "km" + direccion + " de" + ciudadCercana.nombre;
    return ciudadCercana.distancia.toFixed(2) + "km" + direccion + " de" + ciudadCercana.nombre;
}


// Función para calcular la distancia en km con la fórmula de Haversine
function calcularDistanciaHaversine(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // Distancia en km
}

// Función para determinar la dirección cardinal
function obtenerDireccion(lat1, lon1, lat2, lon2) {
    let direccion = "";

    if (lat2 > lat1) {
        direccion += "Norte";
    } else if (lat2 < lat1) {
        direccion += "Sur";
    }

    if (lon2 > lon1) {
        direccion += "Este";
    } else if (lon2 < lon1) {
        direccion += "Oeste";
    }

    return direccion || "Mismo punto";
}

// Función para encontrar la ciudad más cercana
function calcularCiudadMasCercana(punto) {
    let ciudades = [
        { nombre: "San José", lat: 9.935, lon: -84.092 },
        { nombre: "Cartago", lat: 9.864, lon: -83.919 },
        { nombre: "Alajuela", lat: 10.016, lon: -84.215 },
        { nombre: "Heredia", lat: 10.002, lon: -84.116 },
        { nombre: "Liberia", lat: 10.635, lon: -85.437 }
    ];
    let ciudadMasCercana = null;
    let distanciaMinima = Infinity;

    ciudades.forEach(ciudad => {
        let nombre;
        let distancia = calcularDistanciaHaversine(punto.lat, punto.lon, ciudad.lat, ciudad.lon);

        if (distancia < distanciaMinima) {
            distanciaMinima = distancia;
            nombre = ciudad.nombre;
            ciudadMasCercana = { ...ciudad, nombre,distancia };
        }
    });

    return ciudadMasCercana;
}




