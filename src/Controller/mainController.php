<?php

namespace App\Controller;

use App\Repository\PgaRepository;
use App\Repository\HistoricoSismosRepository;
use App\Repository\TodosSismosRepository;
use App\Repository\JmaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class mainController extends AbstractController
{
    private pgaRepository $repository;  //Variable para inyectar el repositorio se usa PGA pero se puede hacer general
    private HistoricoSismosRepository $historicoSismosRepository;  //Variable para inyectar el repositorio se usa PGA pero se puede hacer general
    private TodosSismosRepository $todoSismoRepository;  //Variable para inyectar el repositorio se usa PGA pero se puede hacer general

    private JmaRepository $jmaRepository;  //Variable para inyectar el repositorio de la tabla JMA

    public function __construct(PgaRepository $repository, HistoricoSismosRepository $historicoSismosRepository,
                                TodosSismosRepository $todosSismosRepository, JmaRepository $jmaRepository)
    {
        $this->repository = $repository;
        $this->historicoSismosRepository = $historicoSismosRepository;
        $this->todoSismoRepository = $todosSismosRepository;
        $this->jmaRepository = $jmaRepository;
    }

    #[Route('/', name: 'homepage', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        // Nombre de la página
        $nombre = "Últimos Sismos Registrados";
        $salida = "";

        // Obtiene el parámetro 'tipo' desde GET; si no existe, usa 1 por defecto
        $tipo = $request->query->get('tipo', 1);

        // Según el valor de 'tipo', ejecuta la consulta correspondiente
        if ($tipo == 1) {
            $masDatos = $this->historicoSismosRepository->findHistoricoSismos();
        } elseif ($tipo == 2) {
            $masDatos = $this->historicoSismosRepository->findTodosSismos();
        } else {
            // En caso de recibir otro valor no válido, por seguridad usa la opción por defecto
            $masDatos = $this->historicoSismosRepository->findHistoricoSismos();
            $tipo = 1;
        }

        // Itera para agregar el epicentro calculado a cada registro
        foreach ($masDatos as &$dato) {
            $lat = (float)$dato['latitudEvento'];
            $lon = (float)$dato['longitudEvento'];

            $dato['epi'] = $this->CalculaEpicentro($dato['latitudEvento'], $dato['longitudEvento']);
            $dato['ubicacion'] = $this->formatoDecimalConHemisferio($lat, $lon, 3, "false");
        }
        unset($dato);

        $datosfiltro=$this->filtrarPorCR_BBox($masDatos);



        // Devuelve la vista Twig con los datos y el parámetro 'tipo' reenviado
        return $this->render('index.html.twig', [
            'title'  => $nombre,
            'datos'  => $datosfiltro,
            'salida' => $salida,
            'tipo'   => $tipo, // reenviado al Twig
        ]);
    }

    private function formatoDecimalConHemisferio(float $lat, float $lon, int $precision = 3, bool $html = false): string {
        $latHem = ($lat >= 0) ? 'N' : 'S';
        $lonHem = ($lon >= 0) ? 'E' : 'O';

        $latAbs = number_format(abs($lat), $precision, '.', '');
        $lonAbs = number_format(abs($lon), $precision, '.', '');

        $deg = '°';
        return "{$latAbs}{$deg}{$latHem}  {$lonAbs}{$deg}{$lonHem}";
    }


    /*
     * Controlador para el template de todos los sismos
     * Muestra la tabla con todos los sismos registrados por el seiscomp
    */
    #[Route('/todos', name:'todos', methods: ['POST','GET'])]
    public function todosAction(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        //nombre de la pagina
        $nombre="Todos los Sismos Registrados";
        $salida="";

        //Uso el repository PGA para traer los datos de los sismos
        //$masDatos = $this->repository->findSismo();
        $masDatos = $this->historicoSismosRepository->findTodosSismos();
        //Iteracion para agregar el epicentro a cada resultado del arreglo
        foreach ($masDatos as &$dato) {
            $dato['epi'] = $this->CalculaEpicentro($dato['latitudEvento'], $dato['longitudEvento']);
        }

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('todos.html.twig',
            ['title'=> $nombre, 'datos'=>$masDatos, 'salida'=>$salida] );
    }

    //Calcula el epicentro para una ubicacion especifica y retorna el epicento en strig
    private function CalculaEpicentro($lat, $lon): string
    {
        $ciudades = [];
        // IF para leer el archivo de distritos y calcular el epicentro
        if (($handle = fopen("distritos.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $ciudades[] = ["nombre" => $data[2], "lat" => (float)$data[1], "lon" => (float)$data[0]];
            }
            fclose($handle);
        }


        // --- Función para calcular dirección cardinal ---
        $obtenerDireccion = function($lat1, $lon1, $lat2, $lon2) {
            $direccion = "";
            if ($lat2 > $lat1) {
                $direccion .= "S.";
            } elseif ($lat2 < $lat1) {
                $direccion .= "N.";
            }
            if ($lon2 > $lon1) {
                $direccion .= "E.";
            } elseif ($lon2 < $lon1) {
                $direccion .= "O.";
            }
            return $direccion !== "" ? $direccion : "Mismo punto";
        };

        // --- Encontrar ciudad más cercana ---
        $ciudadMasCercana = null;
        $distanciaMinima = INF;

        foreach ($ciudades as $ciudad) {
            $distancia = $this->calcularDistanciaHaversine($lat, $lon, $ciudad['lat'], $ciudad['lon']);
            if ($distancia < $distanciaMinima) {
                $distanciaMinima = $distancia;
                $ciudadMasCercana = [
                    'nombre' => $ciudad['nombre'],
                    'lat' => $ciudad['lat'],
                    'lon' => $ciudad['lon'],
                    'distancia' => $distancia
                ];
            }
        }

        if ($ciudadMasCercana === null) {
            return "No hay ciudades disponibles";
        }

        // --- Calcular dirección respecto a la ciudad ---
        $direccion = $obtenerDireccion($lat, $lon, $ciudadMasCercana['lat'], $ciudadMasCercana['lon']);

        return number_format($ciudadMasCercana['distancia'], 2) . " km " . $direccion . " de " . $ciudadMasCercana['nombre'];

        //$epicentro = "Este es el epicentro de ".$lat.", ".$lon;
        //return $epicentro;
    }


    /*
     * Funcion para calcular la distancia Haversine
     * @param lat1 y long1 del punto origina, lat2 y long2 del punto que se quiere medir
    */
    public function calcularDistanciaHaversine($lat1, $lon1, $lat2, $lon2): float|int
    {
        $R = 6371; // Radio de la Tierra en km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }




    /**
     * @Route("/pga/", name="pga")
     */
    #[Route('/pga/', name:'pga', methods: ['POST','GET','PUT'])]
    public function pgaAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEventoconNombre($evento);

        //Activo el repositorio para traer los datos de PGV segun el evento
        $datosPgv = $this->repository->findPgvByEventoConNombre($evento);

        //Activo el repositorio para traer los datos de PGD segun el evento
        $datosPgd = $this->repository->findPgdByEventoConNombre($evento);

        // Listado SMHR a excluir de la lista
        $estacionesExcluir = ['AALA','ACLH','AGRH','ACOY','CTEC','CTUH','GCNS','GLIH','LLIH','LVES','PJMH','PQSH','PRCH','SASR',
            'SCNE','SCOH','SISD','SISH','SMSO','SPCH','STRN','TB05','TB11','TBS2'];

        $datosPgaFiltrados = array_filter($datosPga, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });


        $datosPgvFiltrados = array_filter($datosPgv, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });

        $datosPgdFiltrados = array_filter($datosPgd, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });

        $estacionesConDistancia = [];

        foreach ($datosPgaFiltrados as $estacion) {
            // Se calcula la distancia asegurando que los valores se pasen como float
            $distancia = $this->calcularDistanciaHaversine(
                (float) $epi_lat,
                (float) $epi_long,
                (float) $estacion['latitud'],
                (float) $estacion['longitud']
            );

            // Se guarda la estación completa y se le añade la nueva llave de distancia
            $estacion['distancia'] = $distancia;
            $estacionesConDistancia[] = $estacion;
        }

        // Ordenar el arreglo resultante por la distancia (de menor a mayor) usando usort
        usort($estacionesConDistancia, function ($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });

        // Extraer únicamente las primeras 20 posiciones (las más cercanas)
        $top20Estaciones = array_slice($estacionesConDistancia, 0, 20);

        // Crear un arreglo simplificado solo con los datos requeridos (estacion, distancia y maximo)
        $resumenTop20 = array_map(function($estacion) {
            return [
                'estacion' => $estacion['estacion'],
                'distancia' => round($estacion['distancia'], 2), // Redondeado a 2 decimales para mejor lectura
                'aceleracion_maxima' => $estacion['maximo'] ?? null // Asegúrate que 'maximo' sea la llave correcta
            ];
        }, $top20Estaciones);

        return $this->render('pga.html.twig',
            ['fecha' => $fecha,'datos'=>$datosPgaFiltrados,'datosPgv'=>$datosPgvFiltrados,
                'datosPgd'=>$datosPgdFiltrados,'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,
                'epi_long'=>$epi_long,'epi'=>$epi,'top20_cercanas' => $resumenTop20,'todas_estaciones' => $estacionesConDistancia]);
    }



    /**
     * @Route("/espectros/", name="espectros")
     */
    #[Route('/espectros/', name:'espectros', methods: ['POST','GET','PUT'])]
    public function espectrosAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEventoconNombre($evento);

        // Listado SMHR a excluir de la lista
        $estacionesExcluir = ['AALA','ACLH','ACOY','CTEC','CTUH','GCNS','GLIH','LLIH','LVES','PJMH','PQSH','PRCH','SASR',
            'SCNE','SCOH','SISD','SISH','SMSO','SPCH','STRN','TB05','TB11','TBS2'];

        $datosPgaFiltrados = array_filter($datosPga, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });
        return $this->render('espectros.html.twig',
            ['fecha' => $fecha,'datos'=>$datosPgaFiltrados,'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,
                'epi_long'=>$epi_long,'epi'=>$epi]);
    }



    /**
     * @Route("/movil/", name="pga_movil")
     */
    #[Route('/movil/pga/', name:'pga_movil', methods: ['POST','GET','PUT'])]
    public function pgaMobileAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha

        $evento = $request->query->get('id');

        //Activo el repositorio para traer todos los datos del evento buscado
        $MyEvento = $this->historicoSismosRepository->findOneByIdEvento($evento);
        // Formateamos la respuesta usando los getters de la entidad
        $datosEvento = [
            'idEvento'    => $MyEvento->getIdEvento(),
            'fecha'       => $MyEvento->getFechaEvento()->format('Y-m-d H:i:s'),
            'latitud'     => $MyEvento->getLatitudEvento(),
            'longitud'    => $MyEvento->getLongitudEvento(),
            'magnitud'    => $MyEvento->getMagnitudEvento(),
            'lugar'       => $this->CalculaEpicentro($MyEvento->getLatitudEvento(),$MyEvento->getLongitudEvento()),
        ];

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEventoconNombre($evento);

        // Listado SMHR a excluir de la lista
        $estacionesExcluir = ['AALA','ACLH','ACOY','CTEC','CTUH','GCNS','GLIH','LLIH','LVES','PJMH','PQSH','PRCH','SASR',
            'SCNE','SCOH','SISD','SISH','SMSO','SPCH','STRN','TB05','TB11','TBS2'];

        $datosPgaFiltrados = array_filter($datosPga, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });

        // 3. Ordenar de mayor a menor PGA y extraer el TOP 5
        usort($datosPgaFiltrados, function($a, $b) {
            return $b['maximo'] <=> $a['maximo'];
        });
        $top5Pga = array_slice($datosPgaFiltrados, 0, 5);


        return $this->render('pga_movil.html.twig', [
            'id' => $datosEvento['idEvento'],
            'fecha' => $datosEvento['fecha'],
            'magnitud' => $datosEvento['magnitud'],
            'epi_lat' => $datosEvento['latitud'],
            'epi_long' => $datosEvento['longitud'],
            'epi' => $datosEvento['lugar'],
            'datos' => $datosPgaFiltrados, // Todos los datos para mapear
            'top5' => $top5Pga             // Solo 5 para el listado
        ]);
    }





    /**
     * @Route("/movil/", name="jma_movil")
     */
    #[Route('/movil/jma/', name:'jma_movil', methods: ['POST','GET','PUT'])]
    public function jmaMobileAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha

        $evento = $request->query->get('id');

        //Activo el repositorio para traer todos los datos del evento buscado
        $MyEvento = $this->historicoSismosRepository->findOneByIdEvento($evento);
        // Formateamos la respuesta usando los getters de la entidad
        $datosEvento = [
            'idEvento'    => $MyEvento->getIdEvento(),
            'fecha'       => $MyEvento->getFechaEvento()->format('Y-m-d H:i:s'),
            'latitud'     => $MyEvento->getLatitudEvento(),
            'longitud'    => $MyEvento->getLongitudEvento(),
            'magnitud'    => $MyEvento->getMagnitudEvento(),
            'lugar'       => $this->CalculaEpicentro($MyEvento->getLatitudEvento(),$MyEvento->getLongitudEvento()),
        ];

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosJma = $this->jmaRepository->findJmaWithNameByEvent($evento);

        // Listado SMHR a excluir de la lista
        $estacionesExcluir = ['AALA','ACLH','ACOY','CTEC','CTUH','GCNS','GLIH','LLIH','LVES','PJMH','PQSH','PRCH','SASR',
            'SCNE','SCOH','SISD','SISH','SMSO','SPCH','STRN','TB05','TB11','TBS2'];

        $datosJmaFiltrados = array_filter($datosJma, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });

        // Ordenar de mayor a menor JMA y extraer el TOP 5
        usort($datosJmaFiltrados, function($a, $b) {
            return $b['threshold_a0'] <=> $a['threshold_a0'];
        });
        $top5Jma = array_slice($datosJmaFiltrados, 0, 5);

        //se invierte el orden para enviar el arreglo y que se dibuje el mas debil primero
        usort($datosJmaFiltrados, function($a, $b) {
            return $a['threshold_a0'] <=> $b['threshold_a0'];
        });

        return $this->render('jma_movil.html.twig', [
            'id' => $datosEvento['idEvento'],
            'fecha' => $datosEvento['fecha'],
            'magnitud' => $datosEvento['magnitud'],
            'epi_lat' => $datosEvento['latitud'],
            'epi_long' => $datosEvento['longitud'],
            'epi' => $datosEvento['lugar'],
            'datos' => $datosJmaFiltrados, // Todos los datos para mapear
            'top5' => $top5Jma             // Solo 5 para el listado
        ]);
    }






    /**
     * Filtrado por “bounding box” (AABB) con margen amplio para Costa Rica.
     * Mantiene únicamente los puntos dentro del rectángulo [minLat,maxLat] x [minLon,maxLon].
     */
    private function filtrarPorCR_BBox(array $datos): array {
        // Margen amplio alrededor de Costa Rica
        $bbox = [
            'minLat' => 7.8,
            'maxLat' => 11.5,
            'minLon' => -86.9,
            'maxLon' => -81.6,
        ];

        return array_values(array_filter($datos, function ($item) use ($bbox) {
            if (!isset($item['latitudEvento'], $item['longitudEvento'])) return false;
            $lat = (float)$item['latitudEvento'];
            $lon = (float)$item['longitudEvento'];

            return $lat >= $bbox['minLat'] && $lat <= $bbox['maxLat']
                && $lon >= $bbox['minLon'] && $lon <= $bbox['maxLon'];
        }));
    }


    /**
     * @Route("/jma/", name="jma")
     */
    #[Route('/jma/', name:'jma', methods: ['POST','GET','PUT'])]
    public function jmaAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de JMA segun el evento
        #$datosJma = $this->jmaRepository->findJmaByEvent($evento);
        $datosJma = $this->jmaRepository->findJmaWithNameByEvent($evento);


        // Filtra los elementos cuyo campo 'estacion' NO termine en 'h' eliminando la CCSS
        $datosJmaFiltrados = array_filter($datosJma, function ($item) {
            return !str_ends_with($item['estacion'], 'H');
        });


        return $this->render('jma.html.twig',
            ['fecha' => $fecha,'datos'=>$datosJmaFiltrados,'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,
                'epi_long'=>$epi_long,'epi'=>$epi]);
    }







    /**
     * @Route("/informe/", name="informe")
     */
    #[Route('/informe/', name:'informe', methods: ['POST','GET','PUT'])]
    public function informeAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha

        if ($request->isMethod('POST') ) {
            $datosEvento = [
                'idEvento'    => $request->get('id'),
                'fecha'       => $request->get('fecha'),
                'latitud'     => $request->get('lat'),
                'longitud'    => $request->get('long'),
                'magnitud'    => $request->get('mag'),
                'informe'     => $request->get('informe'),
                'profundidad'     => $request->get('profundidad'),
                'lugar'       => $request->get('epi'),
            ];
            if (empty($epi)) {
                // se recalcula el epicentro
                $epi = $this->CalculaEpicentro($datosEvento['latitud'], $datosEvento['longitud']);
            }

        }else if ($request->isMethod('GET')) {
            $evento = $request->query->get('id');
            //Activo el repositorio para traer todos los datos del evento buscado
            $MyEvento = $this->historicoSismosRepository->findOneByIdEvento($evento);
            // 3. Formateamos la respuesta usando los getters de la entidad
            $datosEvento = [
                'idEvento'    => $MyEvento->getIdEvento(),
                'fecha'       => $MyEvento->getFechaEvento()->format('Y-m-d H:i:s'),
                'latitud'     => $MyEvento->getLatitudEvento(),
                'longitud'    => $MyEvento->getLongitudEvento(),
                'magnitud'    => $MyEvento->getMagnitudEvento(),
                'informe'     => $MyEvento->getInforme(),
                'profundidad'  => $MyEvento->getProfundidadEvento(),
                'lugar'       => $this->CalculaEpicentro($MyEvento->getLatitudEvento(),$MyEvento->getLongitudEvento()),
            ];
        }

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEventoconNombre($datosEvento['idEvento']);

        // Listado SMHR a excluir de la lista
        $estacionesExcluir = ['AALA','ACLH','ACOY','CTEC','CTUH','GCNS','GLIH','LLIH','LVES','PJMH','PQSH','PRCH','SASR',
            'SCNE','SCOH','SISD','SISH','SMSO','SPCH','STRN','TB05','TB11','TBS2'];

        $datosPgaFiltrados = array_filter($datosPga, function ($item) use ($estacionesExcluir) {
            // Se excluyen únicamente las estaciones en la lista,
            return !in_array($item['estacion'], $estacionesExcluir, true);
        });

        return $this->render('informe.html.twig',
            ['title'=> "Datos del Sismo: ",
                'id' => $datosEvento['idEvento'],
                'fecha' => $datosEvento['fecha'],
                'magnitud' => $datosEvento['magnitud'],
                'lat' => $datosEvento['latitud'],
                'long' => $datosEvento['longitud'],
                'informe' => $datosEvento['informe'],
                'profundidad' => $datosEvento['profundidad'],
                'epi' => $datosEvento['lugar'],
                'datos' => $datosPgaFiltrados,
                ]);
    }



    /**
     * @Route("/epicentro/", name="epicentro")
     */
    #[Route('/epicentro/', name:'epicentro', methods: ['POST','GET','PUT'])]
    public function epicentroAction(Request $request, EntityManagerInterface $em): Response
    {
        // Inicializamos variables por defecto para evitar errores si entran por GET
        //$evento = $fecha = $mag = $epi = "";
        //$lat = 0.0;
        //$long = 0.0;

        // Chequeo los datos que llegan por POST del ID y la Fecha
        if ($request->isMethod('POST')) {
            $datosEvento = [
                'idEvento'    =>  $request->request->get('id'),
                'fecha'       => $request->request->get('fecha'),
                'magnitud'    => $request->request->get('mag'),
                'latitud'     => (float)$request->request->get('lat'),
                'longitud'    => (float)$request->request->get('long'),
                'lugar'       =>    $request->request->get('epi')];
        }elseif ($request->isMethod('GET')){
            $evento = $request->query->get('id');
            //Activo el repositorio para traer todos los datos del evento buscado
            $MyEvento = $this->historicoSismosRepository->findOneByIdEvento($evento);
            // Formateamos la respuesta usando los getters de la entidad
            $datosEvento = [
                'idEvento'    => $MyEvento->getIdEvento(),
                'fecha'       => $MyEvento->getFechaEvento()->format('Y-m-d H:i:s'),
                'latitud'     => $MyEvento->getLatitudEvento(),
                'longitud'    => $MyEvento->getLongitudEvento(),
                'magnitud'    => $MyEvento->getMagnitudEvento(),
                'lugar'       => $this->CalculaEpicentro($MyEvento->getLatitudEvento(),$MyEvento->getLongitudEvento()),
            ];
        }

        // --- Función para calcular distancia Haversine en PHP ---
        $calcularDistanciaHaversine = function($lat1, $lon1, $lat2, $lon2) {
            $R = 6371; // Radio de la Tierra en km
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $R * $c;
        };

        $todasLasCiudades = [];
        $ciudadesImportantes = [];

        // Leer el archivo de distritos y calcular el epicentro
        if (($handle = fopen("distritos.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Asumimos: 0 = lon, 1 = lat, 2 = nombre, 3 = importante (1 o 0)
                $ciudadLat = (float)$data[1];
                $ciudadLon = (float)$data[0];
                $distancia = $calcularDistanciaHaversine($datosEvento['latitud'], $datosEvento['longitud'], $ciudadLat, $ciudadLon);

                // Verificamos si existe la columna de "importante", por defecto 0
                $esImportante = isset($data[3]) ? (int)$data[3] : 0;

                $ciudad = [
                    "nombre"    => $data[2],
                    "lat"       => $ciudadLat,
                    "lon"       => $ciudadLon,
                    "distancia" => $distancia,
                    "importante"=> $esImportante
                ];

                $todasLasCiudades[] = $ciudad;

                if ($esImportante === 1) {
                    $ciudadesImportantes[] = $ciudad;
                }
            }
            fclose($handle);
        }

        // Ordenar arreglos por distancia ascendente
        usort($todasLasCiudades, function($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });
        usort($ciudadesImportantes, function($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });

        // Retornar solo las 20 más cercanas de la lista general
        $ciudadesCercanas = array_slice($todasLasCiudades, 0, 20);

        return $this->render('epicentro.html.twig', [
            'title'                => "Ciudades cercanas al Epicentro: ",
            'fecha'                => $datosEvento['fecha'],
            'magnitud'             => $datosEvento['magnitud'],
            'id'                   => $datosEvento['idEvento'],
            'lat'                  => $datosEvento['latitud'],
            'long'                 => $datosEvento['longitud'],
            'epi'                  => $datosEvento['lugar'],
            'ciudades_cercanas'    => $ciudadesCercanas,     // Array para Pestaña 1
            'ciudades_importantes' => $ciudadesImportantes   // Array para Pestaña 2
        ]);
    }


    // =========================================================================
    // NUEVAS FUNCIONES PARA EL SHAKEMAP
    // =========================================================================

    #[Route('/shakemaps/', name: 'shakemaps', methods: ['POST','GET','PUT'])]
    public function shakemap(ManagerRegistry $doctrine, Request $request): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        // 1. Obtener la información del evento y de las estaciones (Misma lógica que en pga)
        //$todosSismos = $doctrine->getRepository(\App\Entity\TodosSismos::class)->find($id_evento);
        $jmaData = $doctrine->getRepository(\App\Entity\Jma::class)->findBy(['idEvento' => $evento]);
        //$pgaData =$this->repository->findPgaByEventoconNombre($evento);
/*
        $epi_lat = $todosSismos->getLatitud();
        $epi_long = $todosSismos->getLongitud();
        $magnitud = $todosSismos->getMagnitud();
        $fecha = $todosSismos->getFecha();
*/
        // 2. Preparar el arreglo de estaciones para el algoritmo IDW
        $estaciones = [];
        foreach ($jmaData as $jma) {
            $estaciones[] = [
                'latitud' => (float)$jma->getLat(),
                'longitud' => (float)$jma->getLon(),
                'maximo' => (float)$jma->getJma(), // Asumiendo que esta es la aceleración (PGA)
                'estacion' => $jma->getEstacion()
            ];
        }

        // 3. Generar el archivo GeoJSON (malla de interpolación)
        $shakeMapGeoJson = $this->generateShakeMapData($estaciones, (float)$epi_lat, (float)$epi_long);

        // 4. Renderizar la nueva vista
        return $this->render('shakemaps.html.twig', [
            'epi_lat' => $epi_lat,
            'epi_long' => $epi_long,
            'magnitud' => $magnitud,
            'fecha' => $fecha,
            'pgaData' => $jmaData, // Pasamos las estaciones para dibujar los triángulos
            'shakemap_json' => json_encode($shakeMapGeoJson), // Pasamos el GeoJSON generado
            'id_evento' => $evento
        ]);
    }

    /**
     * Genera una colección de polígonos GeoJSON que representan la intensidad del sismo
     */
    private function generateShakeMapData(array $estaciones, float $epiLat, float $epiLong): array
    {
        // Definimos un margen de grados alrededor del epicentro para crear la malla
        $margin = 1.2;
        $minLat = $epiLat - $margin; $maxLat = $epiLat + $margin;
        $minLng = $epiLong - $margin; $maxLng = $epiLong + $margin;

        $paso = 0.05; // Resolución de la malla (0.05 grados). Si es muy lento, súbelo a 0.1
        $features = [];

        for ($lat = $minLat; $lat <= $maxLat; $lat += $paso) {
            for ($lng = $minLng; $lng <= $maxLng; $lng += $paso) {

                $pgaEstimado = $this->calculateIDW($lat, $lng, $estaciones);

                // Omitir zonas donde la aceleración sea prácticamente nula para ahorrar memoria
                if ($pgaEstimado > 0.5) {
                    $features[] = [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [[
                                [$lng, $lat],
                                [$lng + $paso, $lat],
                                [$lng + $paso, $lat + $paso],
                                [$lng, $lat + $paso],
                                [$lng, $lat]
                            ]]
                        ],
                        'properties' => [
                            'pga' => round($pgaEstimado, 2)
                        ]
                    ];
                }
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    /**
     * Algoritmo de Interpolación IDW (Inverse Distance Weighting)
     */
    private function calculateIDW(float $lat, float $lng, array $estaciones): float
    {
        $numerador = 0;
        $denominador = 0;
        $p = 2; // Potencia del peso

        foreach ($estaciones as $est) {
            $dist = sqrt(pow($lat - $est['latitud'], 2) + pow($lng - $est['longitud'], 2));

            if ($dist < 0.001) return $est['maximo']; // Evitar división por 0 si está sobre la estación

            $peso = 1 / pow($dist, $p);
            $numerador += $peso * $est['maximo'];
            $denominador += $peso;
        }

        return ($denominador == 0) ? 0 : ($numerador / $denominador);
    }


}