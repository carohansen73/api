"use strict"

document.addEventListener('DOMContentLoaded', evento => {

    /*Agrego la transformacion al estilo cuando clickea sobre un input*/
    let input = document.querySelectorAll('input');
    input.forEach(function (inp) {
        inp.addEventListener('click', e=> {
            e.preventDefault();
            let form = inp.parentNode;
            modificarEstuloInput(inp, form);
        }) 
    })

    let texto = document.querySelectorAll('.text-button');
    texto.forEach(function (txt) {
        txt.addEventListener('click', e=> {
            e.preventDefault();
            let form = txt.parentNode;
            console.log(txt.parentNode);
            modificarEstuloInput(txt, form);
        }) 
    })


    /*Llamado AJAX para traer datos al buscar inmueble o comercio*/
    const formInmueble = document.querySelector("#formInmueble");
    formInmueble.addEventListener('submit', e => {
        e.preventDefault();
       ajax(formInmueble)
    });
   
    const formComercio = document.querySelector("#formComercio");
    formComercio.addEventListener('submit', e => {
        e.preventDefault();
       ajax(formComercio)
    });
    
});

function  modificarEstuloInput(input, form){
//    let form = input.parentNode;
   let text = form.querySelector('.text-button');
   let button = form.querySelector('button');
   let i = form.querySelector('i');
   input = form.querySelector('input');

   text.classList.add('transform-text');
   input.classList.add('transform-input');
   button.classList.add('button-transform');
   i.classList.add('i-transform');
   input.focus();
   
}

function ajax(form){
    let type = form.querySelector(".input").name;
    let number = form.querySelector(".input").value;

    const http = new XMLHttpRequest();
    const comienzoUrl = window.location.pathname;
    const url = comienzoUrl+type+'/'+number;
    
    http.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            console.log(this.response);

            //agrego la seccion para la respuesta
            document.getElementById("section-container").classList.add("seccion");
            let container = document.querySelector("#container-response");
            let subtitulo =`<h2>Especificaciones del ${type} `;
            if(type == 'comercio'){
                subtitulo += 'con cuil ';
            }
            subtitulo += `numero: ${number} </h2> <br>`;
            let response =  this.response;
            
           
        //    for(let i = 0; i < response.length; i++){
        //         console.log(response[i]);
        //         container.innerHTML += `<p> ${response[i]} -  </p>`;
        //     }
           
            container.innerHTML = subtitulo + `<h3>Url:  <a class="" href="${url}"> ${url} </a></h3> <br> <p class="response">${response} </p>`;
        }
    }

    http.open("GET", url);
    http.send();
}




/*------------------------- no me muestra los datoss!!!!! --------------------------- */

async function buscarInmueble() {
    try {
        // let container = document.querySelector("#container-response");
        let id = window.location.pathname;
        //.substr(window.location.pathname.lastIndexOf('/') + 1);
        console.log(id);
        const url = 'inmueble';
        let nro_inmueble = document.getElementById("valorInmueble").value;
        console.log(url);
        console.log(nro_inmueble);
        //hace una peticion y obtiene una respuesta
        const response = await fetch(url + "/" + nro_inmueble);

        //transforma la respuesta a json
        const datos = await response.json();
        console.log(datos);
        //llamo a la funcion que muestra las tareas
        // showPersonas(personas, drop,clase);
        console.log(datos['NRO_INMUEBLE'], datos['TIPO'], datos['anotaciones']);
        // container.innerHTML = datos['NRO_INMUEBLE'];
       showData(datos);
        //return personas;
    } catch (e) {
        console.log(e);
    }
}

function showData(datos){
    console.log(datos);
    let container = document.querySelector("#container-response");
    let nro = datos['NRO_INMUEBLE'];
    container.innerHTML = nro;

   
    for(let i = 0; i < datos['anotaciones'].lenght; i++){
        
        container.innerHTML += `<p> ${datos['anotaciones']} -  </p>`;
    }

       
    
   // container.innerHTML = "respuesta = "+data+".";
}






// async function getAll() {
//     try {
//         //let id = window.location.pathname.substr(window.location.pathname.lastIndexOf('/') + 1);
//         const url = 'api/personas';
//         //hace una peticion y obtiene una respuesta
//         const response = await fetch(url + "/");

//         //transforma la respuesta a json
//         const personas = await response.json();
//         //llamo a la funcion que muestra las tareas
//         // showPersonas(personas, drop,clase);
//         return personas;
//     } catch (e) {
//         console.log(e);
//     }
// }





