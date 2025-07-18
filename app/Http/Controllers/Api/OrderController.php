<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\QueryBuilders\OrderQueryBuilder;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Получить список заказов с фильтрами и пагинацией.
     *
     * @param Request $request HTTP-запрос с параметрами фильтрации.
     * @return JsonResponse JSON-ответ со списком заказов.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items.product', 'warehouse']);
        $query = OrderQueryBuilder::apply($query, $request);

        $orders = $query->paginate($request->input('per_page', 10));

        return response()->json(OrderResource::collection($orders));
    }

    /**
     * Создание нового заказа.
     *
     * @param StoreOrderRequest $request Валидированный запрос на создание заказа.
     * @return JsonResponse JSON-ответ с созданным заказом или ошибкой.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->create($request->validated());

            return response()->json([
                'message' => 'Order created',
                'data' => new OrderResource($order->load(['items.product', 'warehouse']))
            ], 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Обновление заказа (имя клиента и позиции).
     *
     * @param UpdateOrderRequest $request Валидированный запрос на обновление заказа.
     * @param Order $order Модель заказа.
     * @return JsonResponse JSON-ответ с обновлённым заказом или ошибкой.
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        try {
            $this->orderService->update($order, $request->validated());

            return response()->json([
                'message' => 'Order updated',
                'data' => new OrderResource($order->refresh()->load(['items.product', 'warehouse']))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Завершение заказа (списание остатков).
     *
     * @param Order $order Модель заказа.
     * @return JsonResponse JSON-ответ с обновлённым заказом или ошибкой.
     */
    public function complete(Order $order): JsonResponse
    {
        try {
            $this->orderService->complete($order);

            return response()->json([
                'message' => 'Order completed',
                'data' => new OrderResource($order->refresh()->load(['items.product', 'warehouse']))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Отмена заказа (возврат остатков).
     *
     * @param Order $order Модель заказа.
     * @return JsonResponse JSON-ответ с обновлённым заказом или ошибкой.
     */
    public function cancel(Order $order): JsonResponse
    {
        try {
            $this->orderService->cancel($order);

            return response()->json([
                'message' => 'Order canceled',
                'data' => new OrderResource($order->refresh()->load(['items.product', 'warehouse']))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Возобновление отменённого заказа (повторное списание остатков).
     *
     * @param Order $order Модель заказа.
     * @return JsonResponse JSON-ответ с обновлённым заказом или ошибкой.
     */
    public function resume(Order $order): JsonResponse
    {
        try {
            $this->orderService->resume($order);

            return response()->json([
                'message' => 'Order resumed',
                'data' => new OrderResource($order->refresh()->load(['items.product', 'warehouse']))
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
