<?php

namespace App\Deserialization\ControllerTraits;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;
use League\Fractal\Serializer\ArraySerializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait WorksWithFractalTrait
{
    private function fractal(array $includes, array $excludes): Manager
    {
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());

        $manager->parseIncludes($includes);

        $manager->parseExcludes($excludes);

        return $manager;
    }

    public function createJsonResponse(
        ResourceAbstract $data,
        $statusCode = Response::HTTP_OK,
        $includes = [],
        $excludes = [],
        $headers = [],
    ): JsonResponse
    {
        return new JsonResponse(
            $this->fractal($includes, $excludes)->createData($data)->toArray(),
            $statusCode,
            $headers
        );
    }
}