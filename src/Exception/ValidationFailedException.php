<?php

namespace App\Exception;

use App\Exception\ExceptionTypes\BadRequestExceptionInterface;
use App\Validation\Model\ErrorBag;

class ValidationFailedException extends BaseException implements BadRequestExceptionInterface
{
    private ErrorBag $errorBag;

    public function __construct(ErrorBag $errorBag, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $this->errorBag = $errorBag;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ErrorBag
     */
    public function getErrorBag(): ErrorBag
    {
        return $this->errorBag;
    }

    public function getErrors()
    {
        return $this->errorBag->getErrors();
    }
}