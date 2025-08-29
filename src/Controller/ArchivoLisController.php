<?php

namespace App\Controller;

use App\Repository\TodosSismosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\HistoricoSismosRepository;
use App\Repository\PgaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArchivoLisController extends AbstractController
{
    private pgaRepository $PGArepository;  //Variable para inyectar el repositorio se usa PGA pero se puede hacer general



    public function __construct(PgaRepository $PGArepository, HistoricoSismosRepository $historicoSismosRepository)
    {
        $this->PGArepository = $PGArepository;
        //$this->historicoSismosRepository = $historicoSismosRepository;

    }




    #[Route('/archivo/lis', name: 'app_archivo_lis')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $datosPga = $this->PGArepository->findPgaByEvento($evento);

        return $this->render('archivo_lis/archivoLis.html.twig', [
            'controller_name' => 'ArchivoLisController', 'fecha' => $fecha,
            'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,'epi_long'=>$epi_long,
            'datos'=>$datosPga
        ]);
    }
}
