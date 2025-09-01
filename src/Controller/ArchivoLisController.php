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
    private string $outputDir;



    public function __construct(PgaRepository $PGArepository, HistoricoSismosRepository $historicoSismosRepository)
    {
        $this->PGArepository = $PGArepository;
        //$this->historicoSismosRepository = $historicoSismosRepository;
        // Directorio donde se guardan los archivos generados
        $this->outputDir =  '../public/descargas/lis'; // expuesto vía web

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


    #[Route('/archivo/lis/generar', name: 'archivo_lis_generar', methods: ['POST'])]
    public function generar(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $items   = $payload['items'] ?? [];

        if (empty($items)) {
            return new JsonResponse(['ok' => false, 'error' => 'Sin ítems'], 400);
        }

        // 1) Lecturas desde BD (ejemplo: buscar metadatos por estación)
        //    Adapte el repositorio y la entidad a su esquema.
        // foreach ($items as &$it) {
        //     $meta = $em->getRepository(Estacion::class)->findOneBy(['codigo' => $it['estacion']]);
        //     $it['ubicacion'] = $meta?->getUbicacion();
        // }

        // 2) Ejecutar script Python externo por cada ítem o en bloque:
        //    Suponga un script: /usr/local/bin/genera_lis.py que recibe un JSON por STDIN
        $fs = new Filesystem();
        if (!$fs->exists($this->outputDir)) {
            $fs->mkdir($this->outputDir, 0775);
        }

        $inputJson = json_encode([
            'items'   => $items,
            'fecha'   => $payload['fecha'] ?? null,
            'magnitud'=> $payload['magnitud'] ?? null,
            'out_dir' => $this->outputDir
        ], JSON_UNESCAPED_UNICODE);

        $process = Process::fromShellCommandline('/usr/bin/python3 /usr/local/bin/genera_lis.py');
        $process->setInput($inputJson);
        $process->setTimeout(600); // 10 min, ajuste según su caso
        $process->run();

        if (!$process->isSuccessful()) {
            return new JsonResponse([
                'ok' => false,
                'error' => 'Python error',
                'stderr' => $process->getErrorOutput()
            ], 500);
        }

        // 3) Tras la ejecución, enumerar archivos generados recientes en $this->outputDir
        $archivos = $this->listarArchivosGenerados();

        return new JsonResponse(['ok' => true, 'archivos' => $archivos], 200);
    }

    #[Route('/archivo/lis/listar', name: 'listado_Lis', methods: ['GET','POST'])]
    public function listar(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $evento = $request->request->get('id');
            $fecha = $request->request->get('fecha');
            $magnitud = $request->request->get('mag');
            $epi_lat = $request->request->get('lat');
            $epi_long = $request->request->get('long');
        }else{echo "NO HAY NADA";}

        //Activo el repositorio para traer los datos de PGA segun el evento
        $archivos = $this->PGArepository->findArchivoLisByEvento($evento);
        foreach ($archivos as &$dato) {
            $dato['urlDescarga'] = "URL de prueba";
            $dato['carpeta'] = "Carpeta";
        }


        return $this->render('archivo_lis/listadoLis.html.twig', [
            'controller_name' => 'ArchivoLisController', 'fecha' => $fecha,
            'id'=>$evento,'magnitud'=>$magnitud,'epi_lat'=>$epi_lat,'epi_long'=>$epi_long,
            'archivos'=>$archivos
        ]);
    }

    private function listarArchivosGenerados(): array
    {
        $baseUrl = '/descargas/lis'; // relativo a public/
        $result  = [];

        if (!is_dir($this->outputDir)) {
            return $result;
        }

        $files = array_values(array_filter(scandir($this->outputDir), fn($f) =>
            !in_array($f, ['.', '..']) && is_file($this->outputDir . DIRECTORY_SEPARATOR . $f)
        ));

        // Opcional: ordenar por fecha de modificación descendente
        usort($files, function ($a, $b) {
            return filemtime($this->outputDir . "/$b") <=> filemtime($this->outputDir . "/$a");
        });

        foreach ($files as $f) {
            $result[] = [
                'nombre' => $f,
                'urlDescarga' => $baseUrl . '/' . rawurlencode($f),
            ];
        }

        return $result;
    }


}
