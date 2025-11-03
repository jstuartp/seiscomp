<?php
/* 
 * Template base de la app.
 * - No envía headers (CSP/Permissions-Policy vienen desde App/Core/SecurityHeaders.php).
 * - Usa el nonce global definido en index.php (APP_NONCE) para scripts inline.
 * - Este archivo debe limitarse a estructura HTML + echo del contenido.
 */
$nonce = defined('APP_NONCE') ? APP_NONCE : (\App\Core\SecurityHeaders::nonce());
$title = $title ?? 'Oficina-LIS';
$content = $content ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="Description" content="Instituto de Investigaciones en Ingeniería, Universidad de Costa Rica">
    
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

    <base href="/">
    
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    
    <!-- Google Tag Manager (Con manejo de errores) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SWFBVM2C07"></script>
    <script nonce="<?= $nonce ?>">
        try {
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-SWFBVM2C07');
        } catch (e) {
            console.warn("Google Tag Manager bloqueado por el navegador o AdBlock.");
        }
    </script>
</head>

<body>
<!-- Google Tag Manager -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=G-SWFBVM2C07"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- Fin Google Tag Manager -->

    <div class="container-fluid" >
        <!-- ENCABEZADO -->
        <header class="row" style="border-bottom: 6px solid #efb810">
            <div class="col-lg-1"></div>

            <div class="col-12 col-lg-7">
                <img src="assets/img/firma_horizontal_continua_blanco.svg" alt="firma horizontal continua blanco">
            </div>

            <div class="col-lg-3 d-none d-lg-block">
                <form class="d-flex" method="GET" action="/Buscar" style="margin-top:12px;">
                    <input
                        class="form-control me-2"
                        type="search"
                        name="q"
                        placeholder="Buscar..."
                        aria-label="Buscar"
                        value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                    >
                    <button class="btn btn-light" type="submit">Buscar</button>
                </form>
            </div>

            <div class="col-lg-1"></div>
        </header>
        <!-- FIN ENCABEZADO -->

        <!-- MENÚ -->
        <nav class="row">
            <div class="col-lg-1"></div>
            <div class="col-12 col-lg-10">
                <nav class="navbar navbar-expand-xl" style="background-color: #FFF;">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="https://inii.ucr.ac.cr">
                            <img src="assets/img/INII.png" alt="INII logo">
                        </a>
                        <a class="navbar-brand" href="https://lis.ucr.ac.cr">
                            <img src="assets/img/LIS.png" alt="LIS logo">
                        </a>

                        <button
                            class="navbar-toggler"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown"
                            aria-expanded="false"
                            aria-label="Toggle navigation"
                        >
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarNavDropdown">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="/">Inicio</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="/Blog">Blog</a>
                                </li>
                                
                                <!-- Dropdown Convenio -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Convenios
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="https://mep.lis.ucr.ac.cr/" target="black">M.E.P.</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://ins-bomberos.lis.ucr.ac.cr/" target="black">INS-Bomberos</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://ccss.lis.ucr.ac.cr/" target="black">C.C.S.S.</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://crc.lis.ucr.ac.cr/" target="black">Cruz Roja</a>
                                        </li>
                                        <!--<li>
                                            <a class="dropdown-item" href="/Ovsicori">OVSICORI</a>
                                        </li> -->                                     
                                    </ul>
                                </li>
                                
                                <!-- Dropdown Estructuras -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Estructuras
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="https://estructuras.lis.ucr.ac.cr/" target="black">Estructuras</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://puentes.lis.ucr.ac.cr/" target="black">Puentes</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://pozos.lis.ucr.ac.cr/" target="black">Pozos</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://estructuras.lis.ucr.ac.cr/UCR" target="black">PROSIME (UCR)</a>
                                        </li>                                                                             
                                    </ul>
                                </li>
                                
                                <!-- Dropdown Educativo -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Educativo
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="/Sismologia" target="black">Sísmología</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/Ingenieria" target="black">Ingeniería</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/Mitos" target="black">Mitos</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/Preguntas" target="black">Preguntas</a>
                                        </li>     
                                        <li>
                                            <a class="dropdown-item" href="/Tesis" target="black">Tesis</a>
                                        </li>
                                        <!--<li>
                                            <a class="dropdown-item" href="https://www.lis.ucr.ac.cr/3D" target="black">Animaciones (3D)</a>
                                        </li>  --> 
                                    </ul>
                                </li>
                                
                                <!-- Dropdown Investigación -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Investigación
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!--<li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Proyectos-en-desarrollo" target="black">Proyectos en desarrollo</a>
                                        </li>-->
                                        <li>
                                            <a class="dropdown-item" href="/Publicaciones">Publicaciones</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/Fallas">Fallas activas</a>
                                        </li>
                                       <!-- <li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Escenarios-Sismicos" target="black">Escenarios sísmicos</a>
                                        </li>     
                                        <li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Mapas-de-Intensidad" target="black">Mapas de Intensidades</a>
                                        </li>-->
                                        <li>
                                            <a class="dropdown-item" href="https://mas.lis.ucr.ac.cr/" target="black">MAS - LIS</a>
                                        </li>   
                                        <li>
                                            <a class="dropdown-item" href="https://crsmd.lis.ucr.ac.cr/" target="black">Base de datos CRSMD</a>
                                        </li> 
                                        <!--<li>
                                            <a class="dropdown-item" href="https://geotecnia.lis.ucr.ac.cr/" target="black">Geotecnia</a>
                                        </li>   -->                                      
                                    </ul>
                                </li>
                                

                                <!-- Dropdown principal -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Laboratorio
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Estaciones">Estaciones</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/Trazas">Trazas</a>
                                        </li>
                                        <!--<li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Registros" target="black">Registros (24 horas)</a>
                                        </li>-->
                                        <li>
                                            <a class="dropdown-item" href="https://servicios.lis.ucr.ac.cr/" target="black">Servicios</a>
                                        </li>
                                       <!-- <li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Descargas" target="black">Descargas</a>
                                        </li>-->
                                        <!--<li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Personal" target="black">Personal</a>
                                        </li>-->
                                        <!--<li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Publicaciones" target="black">Publicaciones</a>
                                        </li>-->

                                        <!-- Submenú (segundo nivel) -->
                                        <li class="dropdown-submenu">
                                            <a
                                                class="dropdown-item dropdown-toggle"
                                                href="#"
                                            >
                                                Informes especiales
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/mayo13/mayo13.html" target="black">Sismo 13 de mayo, 2001 (Costa Rica)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/ago23/us_ago23.html" target="black">Sismo 23 de agosto, 2011 (USA)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/cartago_anim/tobosi2011.html" target="black">Enjambre sísmico Tobosi de Cartago, 2012</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/dominical2012/dominical2012.html" target="black">Sismo 13 de febrero, 2012 (Costa Rica, Dominical)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/grietas_sismo_japon/grietas.html" target="black">#1 Sismo 11 de marzo, 2011 (Japón)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/japweb3/Especial3.html" target="black">#3 Sismo 11 de marzo, 2011 (Japón)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/guana2011/Guanacaste2011.html" target="black">Sismo 23 de julio, 2011 (Costa Rica, Guanacaste)</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/monitoreo/monitoreo.html" target="black">Monitoreo sísmico con acelerógrafos</a>
                                                </li>  
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/_vista/pdf/set052012/" target="black">Sismo 05 de setiembre, 2012 (Costa Rica, Samara)</a>
                                                </li> 
                                            </ul>
                                        </li>                                        
                                    </ul>
                                </li>

                                <!-- Dropdown Investigación -->
                                <li class="nav-item dropdown">
                                    <a
                                        class="nav-link dropdown-toggle"
                                        href="#"
                                        data-bs-toggle="dropdown"
                                    >
                                        Aplicaciones
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="https://gesdi.lis.ucr.ac.cr/" target="black">GEsDI - CSCR10</a>
                                        </li>
                                        <!--<li>
                                            <a class="dropdown-item" href="https://lis.ucr.ac.cr/Descargas" target="black">Descargas registros en formato LIS </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="https://www.lis.ucr.ac.cr/OSM" target="black">Mapa de sismos en OpenStreet Maps</a>
                                        </li>-->
                                        <li>
                                            <a class="dropdown-item" href="https://querymap.lis.ucr.ac.cr/" target="black">QueryMap - Descarga de datos sismologicos</a>
                                        </li>
                                        <!-- Submenú (segundo nivel) -->
                                        <!--<li class="dropdown-submenu">
                                            <a
                                                class="dropdown-item dropdown-toggle"
                                                href="#"
                                            >
                                                Mapas de sismos 3D
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/Sismos3d-todos" target="black">Todos los sísmos</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="https://lis.ucr.ac.cr/Sismos3d-ultimos" target="black">Últimos sísmos</a>
                                                </li>                                                
                                            </ul>
                                        </li>                          -->            
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://mas.lis.ucr.ac.cr/" target="black">MAS-LIS</a>
                                </li>
                            </ul>
                        </div> <!-- /.collapse -->
                    </div> <!-- /.container-fluid -->
                </nav>
            </div>
            <div class="col-lg-1"></div>
        </nav>
        <!-- FIN MENÚ -->

        <!-- CUERPO/CONTENIDO -->
        <section class="row">
            <div class="col-lg-1" style="padding-top:5px;"></div>
            <div
                class="col-12 col-lg-10"
                style="margin-top:10px; background-color:#FFF; min-height:600px; padding-bottom:10px; margin-bottom:40px;"
            >
                <?= $content; ?>
            </div>
            <div class="col-lg-1" style="padding-top:5px;"></div>

            <div class="col-lg-1"></div>
            <div class="col-12 col-lg-10">
                <div class="row" style="border-top: 1px solid red;">
                    <div class="col-12 col-lg-3" style="padding-top:5px; font-size:11px;">
                        <p>INFORMACIÓN DE CONTACTO</p>
                        <p><b>Instituto de Investigaciones en Ingeniería</b></p>
                        <p><b>Laboratorio de Ingeniería Sísmica</b></p>
                        <p>Ciudad de la Investigación, Universidad de Costa Rica<br/>San Pedro, San José, Costa Rica</br>11501-2060 UCR</p>
                        <p>Teléfono: (506) 2511-6661</br>Correo electrónico: <a href="mailto: lis.inii@ucr.ac.cr">lis.inii@ucr.ac.cr</a></p>
                        <p>
                        <div class="d-flex align-items-center">                
                            <ul style="list-style-type: none; padding: 0;">
                                <li><a href="https://www.facebook.com/lis.ucr.ac.cr"><i class="bi bi-facebook" style="font-size: 1rem;"></i> Facebook</a></li>
                                <li><a href="https://www.instagram.com/lis.ucr.ac.cr/"><i class="bi bi-instagram" style="font-size: 1rem;"></i> Instagram</a></li>
                                <li><a href="https://www.youtube.com/@UCRLIS"><i class="bi bi-youtube" style="font-size: 1rem;"></i> YouTube</a></li>
                                <li><a href="https://x.com/LISUCR"><i class="bi bi-twitter" style="font-size: 1rem;"></i> X (Noticias)</a></li>
                                <li><a href="https://x.com/MASLISUCR"><i class="bi bi-twitter" style="font-size: 1rem;"></i> X (MAS-LIS)</a></li>
                                <li><a href="https://t.me/ingenieriasismica"><i class="bi bi-telegram" style="font-size: 1rem;"></i> Telegram (Noticias)</a></li>
                                <li><a href="https://t.me/maslisucr"><i class="bi bi-telegram" style="font-size: 1rem;"></i> Telegram (MAS-LIS)</a></li>
                            </ul>
                        </div>

                        </p>
                        </div>
                        <div class="col-12 col-lg-3" style="padding-top:5px;font-size:11px;">
                        <p>ENLACES DE INTERES</p>
                        <p><a href="https://ucr.ac.cr">Universidad de Costa Rica</a></p>
                        <p><a href="https://rectoria.ucr.ac.cr">Rectoria</a></p>
                        <p><a href="https://www.sep.ucr.ac.cr/">Sistema de estudios de Posgrados</a></p>
                        <p><a href="https://fing.ucr.ac.cr/">Facultad de Ingeniería</a></p>
                        
                        </div>
                        <div class="col-12 col-lg-3" style="padding-top:5px;">
                        
                        </div>
                        <div class="col-12 col-lg-3" style="padding-top:5px;">
                        
                        </div>
                </div>			
            <div class="col-lg-1"></div>            
            
        </section>        
        <!-- FIN CUERPO/CONTENIDO -->

        <!-- PIE DE PÁGINA -->
        <footer class="row">
            <div class="col-lg-1"></div>
            <div class="col-12 col-lg-10">
               
            </div>
            <div class="col-lg-1"></div>
        </footer>
        <!-- FIN PIE DE PÁGINA -->
    </div>

    <!-- Bootstrap 5.3 (JS) -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Script inline de la plantilla: añade nonce -->
    <script nonce="<?= $nonce ?>">
    document.addEventListener('DOMContentLoaded', function () {
        // Manejador para abrir submenús en pantallas grandes y móviles
        document.querySelectorAll('.dropdown-submenu > a').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let submenu = this.nextElementSibling;
                if (submenu) {
                    submenu.classList.toggle('show');
                }
            });
        });

        // Evitar que otros submenús abiertos interfieran
        document.querySelectorAll('.dropdown').forEach(function(menu) {
            menu.addEventListener('hide.bs.dropdown', function(e) {
                let submenus = menu.querySelectorAll('.dropdown-menu.show');
                submenus.forEach(function(submenu) {
                    submenu.classList.remove('show');
                });
            });
        });
    });
    </script>

</body>
</html>
