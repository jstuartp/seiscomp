<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EstructurasRepository;


final class EstructurasController extends AbstractController
{
    private EstructurasRepository $estructurasRepository;

    public function __construct(EstructurasRepository $estructurasRepository)
    {
        $this->estructurasRepository = $estructurasRepository;

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




        return $this->render('estructuras/index.html.twig', ['fecha' => $fecha,'datos'=>$datos,'id'=>$evento,
            'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,
            'epi_long'=>$epi_long,'epi'=>$epi,
            'controller_name' => 'EstructurasController',
        ]);
    }
}
