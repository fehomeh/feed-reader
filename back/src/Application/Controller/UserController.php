<?php

declare(strict_types=1);

namespace FeedReader\Application\Controller;

use FeedReader\Domain\User\Command\CheckEmailOccupation;
use FeedReader\Domain\User\Command\RegisterUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

final class UserController extends AbstractController
{
    /**
     * @Route("/users/login", methods={"POST"})
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        $success = null !== $user;

        $responseData = [];
        $responseData['success'] = $success;
        if ($success) {
            $responseData['username'] = $user->getUsername();
        }

        return $this->json($responseData);
    }

    /**
     * @Route("/users/email/{email}", methods={"GET"})
     *
     * @param string $email
     * @param MessageBusInterface $bus
     *
     * @return JsonResponse
     */
    public function checkEmailExistence(string $email, MessageBusInterface $bus): JsonResponse
    {
        $envelope = $bus->dispatch(new CheckEmailOccupation($email));
        $handledStamp = $envelope->last(HandledStamp::class);
        $isFree = true;
        if ($handledStamp instanceof HandledStamp) {
            $isFree = $handledStamp->getResult();
        }

        return $this->json(['is_free' => $isFree]);
    }

    /**
     * @Route("/users", methods={"POST"})
     *
     * @param MessageBusInterface $bus
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function register(MessageBusInterface $bus, Request $request): JsonResponse
    {
        $body = json_decode($this->extractBody($request), true, 512, JSON_THROW_ON_ERROR);

        $bus->dispatch(new RegisterUser($body['email'] ?? '', $body['password'] ?? '', $body['repeat'] ?? ''));

        return $this->json(['success' => true], Response::HTTP_CREATED);
    }

    /**
     * @Route("/users/logout", methods={"GET"})
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        return $this->json(['success' => true]);
    }
    /**
     * @Route("/users/logout-message", methods={"GET"})
     * @return JsonResponse
     */
    public function logoutMessage(): JsonResponse
    {
        return $this->json(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function extractBody(Request $request): string
    {
        return (string) $request->getContent();
    }
}
