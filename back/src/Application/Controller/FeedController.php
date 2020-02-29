<?php

declare(strict_types=1);

namespace FeedReader\Application\Controller;

use FeedReader\Application\Feed\Command\GetFeed;
use FeedReader\Application\Feed\DTO\FeedList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class FeedController extends AbstractController
{
    /**
     * @Route("/feeds", methods={"GET"})
     *
     * @param MessageBusInterface $bus
     *
     * @return JsonResponse
     */
    public function feed(MessageBusInterface $bus): JsonResponse
    {
        $envelope = $bus->dispatch(new GetFeed());
        $handledStamp = $envelope->last(HandledStamp::class);
        $data = ['success' => false, 'feed' => []];
        if ($handledStamp instanceof HandledStamp) {
            $feedList = $handledStamp->getResult();
            assert($feedList instanceof FeedList);
            $data['feed'] = $feedList;
            $data['success'] = true;
        }

        return $this->json($data);
    }
}
