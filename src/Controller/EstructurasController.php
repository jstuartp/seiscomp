<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EstructurasRepository;
use App\Repository\PgaEstructurasRepository;


final class EstructurasController extends AbstractController
{
    private EstructurasRepository $estructurasRepository;
    private PgaEstructurasRepository $pgaEstructurasRepository;

    public function __construct(EstructurasRepository $estructurasRepository, PgaEstructurasRepository $PgaEstructurasRepository)
    {
        $this->estructurasRepository = $estructurasRepository; //control para el repositorio de la tabla estructuras
        $this->PgaEstructurasRepository = $PgaEstructurasRepository; //control para el repositorio de la tabla PGAEstructuras

    }


    #[Route('/estructuras', name: 'app_estructuras', methods: ['POST','GET','PUT'])]
    public function index(Request $request): Response
    {

        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
            $epi = $request->request->get('epi');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datos = $this->estructurasRepository->findAllEstructuras();

        //Extraigo todas las maximas pgas del evento
        $pgas = $this->estructurasRepository->findMaxPgaFromEvent($evento);
        $pgasAsociativo = [];
        foreach ($pgas as $row) {
            $pgasAsociativo[$row['estacion']] = $row['maximo'];
        }

        return $this->render('estructuras/index.html.twig', ['fecha' => $fecha,'datos'=>$datos,'id'=>$evento,
            'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,
            'epi_long'=>$epi_long,'epi'=>$epi, 'pgas'=>$pgasAsociativo,
            'controller_name' => 'EstructurasController',
        ]);
    }


    #[Route('/estructuras/estructura', name: 'estructura', methods: ['POST','GET','PUT'])]
    public function estructuraAction(Request $request): Response
    {

        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi = $request->request->get('epi');
            $estructura = $request->request->get('estructura');
        }else{echo "NO HAY NADA";}
        $datos = $this->estructurasRepository->findUbicacionEstructura($estructura);

        //Extraigo todas las graficas  del evento
        $graficas = $this->estructurasRepository->findGraficaFromEvent($evento);
        dump($graficas);
        $graficasAsociativo = [];

        foreach ($graficas as $row) {
            $graficasAsociativo[$row['estacion']] = $row['grafica'];
        }



        return $this->render('estructuras/estructura.html.twig', ['fecha' => $fecha,'datos'=>$datos,'id'=>$evento,
            'magnitud'=>$magnitud,'epi'=>$epi, 'estructura'=>$datos, 'grafica' => $graficasAsociativo,
            'controller_name' => 'EstructurasController',
        ]);
    }



    #[Route('/estructuras/formasonda', name: 'formasonda', methods: ['POST','GET','PUT'])]
    public function formasondaAction(Request $request): Response
    {

        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi = $request->request->get('epi');
            $estructuraid = $request->request->get('estructuraid');
            $estructuraNombre = $request->request->get('estructuraNombre');
        }else{echo "NO HAY NADA";}
        $datos = $this->estructurasRepository->findUbicacionEstructura($estructuraid);

        //Traigo las estaciones que tiene el edificio
        $estacionesArray = $this->estructurasRepository->findEstacionByEdificio($estructuraNombre);
        #dd($estacionesArray);
        //Extraigo todas las graficas  del evento
        $graficasEdificio = $this->PgaEstructurasRepository->findGraficasByEventoYEstaciones($evento, $estacionesArray);
        //dd($graficasEdificio);



        return $this->render('estructuras/formasonda.html.twig', ['fecha' => $fecha,'datos'=>$datos,'id'=>$evento,
            'magnitud'=>$magnitud,'epi'=>$epi, 'estructura'=>$datos, 'graficas' => $graficasEdificio,
            'controller_name' => 'EstructurasController',
        ]);
    }

}
