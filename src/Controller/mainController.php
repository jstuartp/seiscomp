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


    #[Route('/', name:'homepage', methods: ['POST','GET'])]
    public function index(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        $titulo="Pagina Principal";

        #$mydate = $request->request->get('date');
        #$time = $request->request->get('time');
        $em = $doctrine->getManager();
        //$seiscomp = $doctrine->getManager('seiscomp');

        //nombre de la pagina
        $nombre="Ultimos Sismos Registrados";
        $datos = "";
        $json= "";
        $salida="";
        $datosSeiscomp="";

        $ciudades1 = [];
        if (($handle = fopen("distritos.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $ciudades1[] = ["nombre" => $data[2], "lat" => (float)$data[1], "lon" => (float)$data[0]];
            }
            fclose($handle);
        }

        //se llama a la funcion para sacar los datos de la tabla
        $datos = $this->datosTabla($em);
        // intento de usar el entity
        //$masDatos= $em->getRepository(PgaRepository::class)->findSismo();

        //$datosSeiscomp = $this->datosTablaSeiscomp($seiscomp,$mydate);

        //Devuelvo todo el entity, lo que me permite usarlo en el twigg
        return $this->render('index.html.twig',
            ['title'=> $nombre, 'datos'=>$datos, 'salida'=>$salida,
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
        #$evento = $id;
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
        }else{echo "NO HAY NADA";}
        #echo $evento;
        #$evento="UCR_lis2024nuom";


        $sql = "select distinct Pga.idpga as id, Pga.estacion, ROUND(Pga.latitud,3) as latitud, ROUND(Pga.longitud,3) as longitud, ROUND(Pga.hne_pga*100,4) as hne, ROUND(Pga.hnn_pga*100,4) as hnn, ROUND(Pga.hnz_pga*100,4) as hnz,
                ROUND(Pga.maximo*100,4) as maximo, Pga.rutaWaveform as grafica
                From Pga Where Pga.tipo_estacion != 2 and Pga.nombre_evento = '".$evento."'";
        //echo $sql;
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();
        $return = $result->fetchAllAssociative();

        return $this->render('pga.html.twig',
            ['title'=> "Aceleraciones Maximas del Sismo de: ", 'fecha' => $fecha,'datos'=>$return,'id'=>$evento]);
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




    /**
     *
     * @return array con los datos parseados
     * del archivo .lis
     */
    public function parser(){

        //obtengo la lista de archivos .lis del directorio
        $arrFiles = glob('../public/lis/*.lis');
        $lista = [];  //matriz vacia
        //var_dump($arrFiles);
        $i=0; //iterador
        $listajson="";

            //itera por cada archivo que encuentra
            foreach($arrFiles as $file) {
                //defino los dos array que se utilizan
                $datos= array();
                $aux = array();
                //defino variable numeradora
                $numlinea =0;
                //extraigo el nombre del archivo a procesar
                $name = $file;
                //ejecuto la funcion fopen que me extrae los datos completos del archivo
                $archivo = fopen($name, 'r');

                //iteracion por todo el archivo abierto
                while ($linea = fgets($archivo)) {

                    //agrego al array aux cada linea del archivo
                    $aux[] = $linea;
                    if ($linea)
                        $numlinea++;

                }
                fclose($archivo); //cierro el archivo
                /**
                 * Cada linea del archivo queda almacenada en el array aux
                 * Como todos los archivos tienen el mismo formato, extraigo las lineas de interés
                 * recortandolas con la función explode, lo que me da la información después del los dos puntos
                 */

                $partes = explode(":", $aux[12]);
                $datos += [$partes[0] => $partes[1]];
                $partes = explode(":", $aux[15]);
                $datos += [$partes[0] => $partes[1]];
                $partes = explode(":", $aux[16]);
                $datos += [$partes[0] => $partes[1]];
                $partes = explode(":", $aux[24]);
                $datos += [$partes[0] => $partes[1]];
                $partes = explode(":", $aux[25]);
                $datos += [$partes[0] => $partes[1]];
                $partes = explode(":", $aux[26]);
                $datos += [$partes[0] => $partes[1]];
                /**
                 * Esta parte esta muy fijada, pero es posible gracias a que se sabe como esta el formato
                 * del documento en cuestion
                 */

    //obtengo los datos usando los indices de lo que necesito recuperar
                /**
                 * Lleno la matriz lista para cada archivo
                 * posterior la envio al final de la funcion
                 */
                $lista[$i]['station']=$datos['Station Code'];
                $lista[$i]['latitud'] = $datos['Station Latitude'];
                $lista[$i]['longitud'] = $datos['Station Longitude'];
                //uso la funcion maximo para calcular el valor maximo entre los 3 valores
                $lista[$i]['maximo'] = max($datos['PGA-N00E'], $datos['PGA-UPDO'], $datos['PGA-N90E']);
                //itero el contador
                $i++;
        }

            return ($lista);

    }
}