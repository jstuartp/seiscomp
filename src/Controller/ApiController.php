<?php

namespace App\Controller;

use App\Repository\PgaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private PgaRepository $repository;

    public function __construct(PgaRepository $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/api/earthquakes', name: 'api_earthquakes', methods: ['GET'])]
    public function earthquakes(): JsonResponse
    {
        $data = $this->repository->findSismo();
        return $this->json($data);
    }
}
