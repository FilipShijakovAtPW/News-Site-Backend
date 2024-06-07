<?php

namespace App\Deserialization\ControllerTraits;


use Symfony\Component\HttpFoundation\Request;

trait WorksWithJsonDecoderTrait
{
    public function getJsonDataFromRequest(Request $request) {
        $requestContent = $request->getContent();

        return json_decode($requestContent, true);
    }
}