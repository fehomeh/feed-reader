<?php

declare(strict_types=1);

namespace FeedReader\Application\EventListener;

use FeedReader\Domain\User\Exception\UserExists;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Throwable;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();
        $data = ['success' => false];
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $data['error'] = $exception->getMessage();
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        } elseif ($exception instanceof ValidationFailedException) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $data['error'] = $this->flattenValidationErrors($exception);
        } elseif ($exception instanceof HandlerFailedException) {
            foreach ($exception->getNestedExceptions() as $nestedException) {
                $data = $this->parseNestedMessengerExceptions($data, $response, $nestedException);
            }
        }
        $response->setStatusCode($statusCode);
        $response->setData($data);

        $event->setResponse($response);
    }

    /**
     * @param ValidationFailedException $exception
     *
     * @return array<array>
     */
    private function flattenValidationErrors(ValidationFailedException $exception): array
    {
        $result = [];
        /** @var ConstraintViolation $violation */
        foreach ($exception->getViolations() as $violation) {
            $result[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $result;
    }

    /**
     * @param array<array|string|false> $data
     * @param JsonResponse $response
     * @param Throwable $nestedException
     *
     * @return array<array|false|string>
     */
    private function parseNestedMessengerExceptions(
        array $data,
        JsonResponse $response,
        Throwable $nestedException
    ): array {
        if ($nestedException instanceof UserExists) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $data['error'] = $nestedException->getMessage();
        }

        return $data;
    }
}
