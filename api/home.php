<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicaciones</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/f2271d3f87.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Barra de navegacion -->
    <nav class="navbar bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href=""> <img class="logo" src="img/logo-aplicaciones.png" alt=""> </a>
        </div>
    </nav>
    <!-- Fin barra de navegacion -->

    <section class="seccion pb-5">

        <div class="container">
            <!-- Seccion del titulo y descripcion -->
            <div class="col-lg-12">
                <div class="row"> 
                    <div class="d-flex justify-content-center m-3">
                        <h1> Aplicaciones </h1>
                    </div>
                    <p> En esta API podes obtener datos de inmuebles buscandolos a traves del numero de inmueble, o comercios buscandolos por el CUIT. </p>
                </div>
            </div>
            <!-- FIN Seccion del titulo -->

            <!-- Seccion BOTONES -->
            <div class="col-lg-12 mt-4">
                <div class="row  d-flex"> 
                    <div class=" col-lg-6"> 
                        <div class="d-flex justify-content-end me-5">

                            <!-- BOTON ANIMADO INMUEBLE -->
                            <div class="box mt-5">
                                <form name="search" action="inmueble" id="formInmueble" class="form" method="GET">
                                    <input type="number" class="input" name="inmueble" id="valorInmueble"  >
                                
                                    <p class="text-button"> Numero de Inmueble</p>
                                    <button id="botonInmueble" type="submit"><i class="fas fa-search"></i></button>
                                </form>
                            </div>
                            <!-- FIN BOTON ANIMADO INMUEBLE -->
                        </div>    
                    </div>
                    <div class=" col-lg-6"> 
                        <div class="d-flex  ms-5">

                        <!-- BOTON ANIMADO COMERCIO  -->
                        <div class="box mt-5">
                            <form name="buscador" action="comercio" class="form" id="formComercio"  >
                                <input type="text" class="input" name="comercio" >
                                
                                <p class="text-button"> CUIT del Comercio</p>
                                <button id="botonInmueble" type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                        <!-- FIN BOTON ANIMADO COMERCIO  -->

                            <!-- INPUT CON BOTON COMUN
                                <form action="inmueble" id="formInmueble">
                                <div class="row"> 
                                    <div class="row g-col-lg-6"> 
                                        <label class="p-0">Comercio</label>
                                        <input type="number" name="inmueble" id="valorInmueble">
                                    
                                        <button id="botonInmueble" class="btn btn-dark mt-4" type="submit"> Buscar Inmueble </button>
                                    </div>
                                </div>
                            </form> -->

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="section-container" class=" mt-3">
        <div class="container">

            <pre id="container-response" class="mt-4">

</pre>

        </div>

    </section>

    



    <script src="js/api-aplicaciones.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>
</html>
