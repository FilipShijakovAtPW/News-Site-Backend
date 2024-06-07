<?php

namespace App\Deserialization\ControllerTraits;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait WorksWithFractalTrait
{
    private function fractal(): Manager
    {
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());

        return $manager;
    }

    public function createJsonResponse(
        ResourceAbstract $data,
        $statusCode = Response::HTTP_OK,
        $headers = [],
    ): JsonResponse
    {
        return new JsonResponse(
            $this->fractal()->createData($data)->toArray(),
            $statusCode,
            $headers
        );
    }
}