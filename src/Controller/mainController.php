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

    #[Route('/', name:'homepage', methods: ['POST','GET'])]
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        //nombre de la pagina
        $nombre="Ultimos Sismos Registrados";
        $salida="";

        //Uso el repository PGA para traer los datos de los sismos
        //$masDatos = $this->repository->findSismo();
        $masDatos = $this->historicoSismosRepository->findHistoricoSismos();
        //Iteracion para agregar el epicentro a cada resultado del arreglo
        foreach ($masDatos as &$dato) {
            $dato['epi'] = $this->CalculaEpicentro($dato['latitudEvento'], $dato['longitudEvento']);
        }

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$masDatos, 'salida'=>$salida] );
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
            $distancia = $calcularDistanciaHaversine($lat, $lon, $ciudad['lat'], $ciudad['lon']);
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


    #[Route('/indexAA', name:'indexAA', methods: ['POST','GET'])]
    public function indexAA(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        $titulo="Pagina Principal";
        #$mydate = $request->request->get('date');
        #$time = $request->request->get('time');
        $em = $doctrine->getManager('default');
        $seiscomp = $doctrine->getManager('seiscomp');

        //nombre de la pagina
        $nombre="Ultimos Sismos Registrados";
        $datos = "";
        $json= "";
        $salida="";
        $datosSeiscomp="";//se llama a la funcion para sacar los datos de la tabla
        $datos = $this->datosTabla($em);
        //$datosSeiscomp = $this->datosTablaSeiscomp($seiscomp,$mydate);
        $json = json_encode($datos);

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$datos,'json'=>$json, 'salida'=>$salida,
                'seiscomp' =>$datosSeiscomp]);
    }



    public function datosTabla(EntityManagerInterface $em)
    {
        $sql = "select distinct PEvent.publicID,Event._oid, Origin.time_value as hora, Origin._oid, ROUND(M.magnitude_value,1) as magnitud,
                ROUND(Origin.latitude_value,3) as latitud, ROUND(Origin.longitude_value,3) as longitud, ROUND(Origin.depth_value,2) as profundidad
                from Origin,PublicObject as POrigin,Event,PublicObject as PEvent, Magnitude as M
                where POrigin.publicID=Event.preferredOriginID and  M._parent_oid = Origin._oid
                and Origin._oid=POrigin._oid and Event._oid=PEvent._oid
                Order by Origin.time_value DESC;";
        //echo $sql;
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();
        $return = $result->fetchAllAssociative();
        return ($return);
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
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEvento($evento);


        return $this->render('pga.html.twig',
            ['fecha' => $fecha,'datos'=>$datosPga,'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,'epi_long'=>$epi_long]);
    }



    /**
     * @Route("/jma/", name="pga")
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
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de JMA segun el evento
        $datosJma = $this->jmaRepository->findJmaByEvent($evento);


        return $this->render('jma.html.twig',
            ['fecha' => $fecha,'datos'=>$datosJma,'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,'epi_long'=>$epi_long]);
    }







    /**
     * @Route("/informe/", name="informe")
     */
    #[Route('/informe/', name:'informe', methods: ['POST','GET','PUT'])]
    public function informeAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        //if ($request->isMethod('POST')) {
        if ($request->isMethod('POST') or $request->isMethod('GET')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $mag = $request->request->get('mag');
            $lat = $request->request->get('lat');
            $long = $request->request->get('long');
            $informe = $request->request->get('informe');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        return $this->render('informe.html.twig',
            ['title'=> "Datos del Sismo: ", 'fecha' => $fecha,'magnitud'=>$mag,'id'=>$evento,'lat'=>$lat,'long'=>$long,'informe'=>$informe,'epi'=>$epi]);
    }



    /**
     * @Route("/epicentro/", name="epicentro")
     */
    #[Route('/epicentro/', name:'epicentro', methods: ['POST','GET','PUT'])]
    public function epicentroAction(Request $request, EntityManagerInterface $em): Response
    {
        //Chequeo los datos que llegan por post del ID y la Fecha
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $mag = $request->request->get('mag');
            $lat = $request->request->get('lat');
            $long = $request->request->get('long');
        }else{echo "NO HAY NADA";}

        $ciudades1 = [];
        // IF para leer el archivo de distritos y calcular el epicentro
        if (($handle = fopen("distritos.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $ciudades1[] = ["nombre" => $data[2], "lat" => (float)$data[1], "lon" => (float)$data[0]];
            }
            fclose($handle);
        }
        return $this->render('epicentro.html.twig',
            ['title'=> "Ciudades cercanas al Epicentro: ", 'fecha' => $fecha,'magnitud'=>$mag,'id'=>$evento, 'lat'=>$lat,'long'=>$long , 'ciudadesp' =>$ciudades1]);
    }

    public function datosTablaSeiscomp(EntityManagerInterface $seiscomp, $mydate)
    {
        $sql = "SELECT r.startTime_value, r.waveformID_stationCode, 
                    r.waveformID_channelCode, r.gainUnit, p.type, ROUND(p.motion_value*100,4) as pga 
                    from Record r join PeakMotion p ON r._oid = p._parent_oid 
                    where (r.startTime_value > '".$mydate." 00:00:00' and r.startTime_value < '".$mydate." 23:59:59' ) and p.type='pga'";
        //echo $sql;

        $stmt = $seiscomp->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();
        $return = $result->fetchAllAssociative();
        return ($return);
    }


}