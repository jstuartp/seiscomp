<?php

namespace App\Controller;

use App\Repository\PgaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class mainController extends AbstractController
{
    private pgaRepository $repository;  //Variable para inyectar el repositorio se usa PGA pero se puede hacer general

    public function __construct(PgaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/', name:'homepage', methods: ['POST','GET'])]
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {


        //nombre de la pagina
        $nombre="Ultimos Sismos Registrados";

        $salida="";
        $datosSeiscomp="";

        $ciudades1 = [];
        // IF para leer el archivo de distritos y calcular el epicentro
        if (($handle = fopen("distritos.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $ciudades1[] = ["nombre" => $data[2], "lat" => (float)$data[1], "lon" => (float)$data[0]];
            }
            fclose($handle);
        }

        //Uso el repository PGA para traer los datos de los sismos
        $masDatos = $this->repository->findSismo();

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$masDatos, 'salida'=>$salida,
                 'seiscomp' =>$datosSeiscomp, 'ciudadesp' =>$ciudades1] );
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
        $datosSeiscomp="";




        //se llama a la funcion para sacar los datos de la tabla
        $datos = $this->datosTabla($em);

        //$datosSeiscomp = $this->datosTablaSeiscomp($seiscomp,$mydate);
        $json = json_encode($datos);



//print_r($salida);

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$datos,'json'=>$json, 'salida'=>$salida,
                'seiscomp' =>$datosSeiscomp]);
    }



    /**
     * @Route("/llenaTabla", name="llenaTabla")
     */
    public function llenaTabla(Request $request, EntityManagerInterface $em)
    {
        $titulo="Pagina Principal";
        //$request = $this->get('request_stack')->getCurrentRequest();
        $date = $request->get('date');
        $time = $request->get('time');



        //nombre de la pagina
        $nombre="Datos Obtenidos";
        $salida = array(); //contendrá cada linea salida desde la aplicación en Python

        //exec("python3 /var/www/html/prueba/main.py", $salida); //llamada a python


        $datos = $this->datosTabla($em);
        $json= json_encode($datos);

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$datos,'json'=>$json, 'salida'=>$salida]);
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
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->repository->findPgaByEvento($evento);


        return $this->render('pga.html.twig',
            ['title'=> "Aceleraciones Maximas del Sismo de: ", 'fecha' => $fecha,'datos'=>$datosPga,'id'=>$evento]);
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