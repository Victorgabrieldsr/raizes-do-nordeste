<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'branch', 'orderItems.product', 'payment']);

        if ($request->order_channel) {
            $query->where('order_channel', $request->order_channel);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $orders = $query->paginate(10);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'order_channel' => 'required|in:APP,TOTEM,BALCAO,PICKUP,WEB',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = JWTAuth::user();
        $totalPrice = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || !$product->is_active) {
                return response()->json([
                    'error' => 'PRODUTO_NAO_ENCONTRADO',
                    'message' => "Produto {$item['product_id']} não encontrado ou inativo.",
                ], 404);
            }

            $inventory = Inventory::where('branch_id', $request->branch_id)
                ->where('product_id', $item['product_id'])
                ->first();

            if (!$inventory || $inventory->quantity < $item['quantity']) {
                return response()->json([
                    'error' => 'ESTOQUE_INSUFICIENTE',
                    'message' => "Estoque insuficiente para o produto {$product->name}.",
                    'details' => [
                        [
                            'product_id' => $item['product_id'],
                            'product_name' => $product->name,
                            'requested' => $item['quantity'],
                            'available' => $inventory ? $inventory->quantity : 0,
                        ]
                    ],
                ], 409);
            }

            $totalPrice += $product->price * $item['quantity'];
            $itemsData[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $order = Order::create([
            'user_id' => $user->id,
            'branch_id' => $request->branch_id,
            'order_channel' => $request->order_channel,
            'total_price' => $totalPrice,
            'status' => 'AGUARDANDO_PAGAMENTO',
        ]);

        foreach ($itemsData as $itemData) {
            OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));

            $inventory = Inventory::where('branch_id', $request->branch_id)
                ->where('product_id', $itemData['product_id'])
                ->first();

            $inventory->update([
                'quantity' => $inventory->quantity - $itemData['quantity'],
            ]);
        }

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'points' => (int)($totalPrice / 10),
            'type' => 'EARNED',
        ]);

        return response()->json([
            'message' => 'Pedido criado com sucesso.',
            'order' => $order->load(['orderItems.product', 'payment']),
        ], 201);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'branch', 'orderItems.product', 'payment'])->find($id);

        if (!$order) {
            return response()->json([
                'error' => 'PEDIDO_NAO_ENCONTRADO',
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:AGUARDANDO_PAGAMENTO,PAGO,COZINHA,PRONTO,ENTREGUE,CANCELADO',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'error' => 'PEDIDO_NAO_ENCONTRADO',
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status do pedido atualizado com sucesso.',
            'order' => $order,
        ]);
    }
}