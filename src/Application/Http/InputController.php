<?php

declare(strict_types=1);

namespace App\Application\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class InputController extends AbstractController
{
    public const ROUTE = '/input';

    public function __construct(
    ) {
    }

    #[Route(path: self::ROUTE, name: 'create', methods: ['POST'])]
    public function newOrdersAction(Request $request): Response
    {
        foreach ($request->toArray() as $order) {
            if ($this->validation->validate($order)) {
                $jsonNewOrder = json_encode(value: $order, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->makeResponse();
    }

    #[Route(path: self::ROUTE, name: 'update', methods: ['PUT'])]
    public function updateOrdersAction(Request $request): Response
    {
        foreach ($request->toArray() as $order) {
            if ($this->validation->validate(order: $order)) {
                $this->saveLog('update_orders', $order);
                $jsonUpdatedOrder = json_encode(value: $order, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            }
        }

        return $this->makeResponse();
    }

    private function makeResponse(): Response
    {
        $response = new JsonResponse();
        $messages = [
            'Accepted orders' => $this->validation->getSuccess(),
            'Failures validation' => $this->validation->getFailures()
        ];
        $response->setJson(json_encode(value: $messages, flags: JSON_THROW_ON_ERROR));

        return $response;
    }
}
