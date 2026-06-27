<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function processPayment(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|in:PIX,CARTAO_CREDITO,CARTAO_DEBITO,DINHEIRO,MOCK',
        ]);

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'error' => 'PEDIDO_NAO_ENCONTRADO',
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        if ($order->status !== 'AGUARDANDO_PAGAMENTO') {
            return response()->json([
                'error' => 'PEDIDO_JA_PROCESSADO',
                'message' => 'Este pedido já foi processado.',
            ], 409);
        }

        $mockResult = $this->mockPaymentGateway($order->total_price);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'status' => $mockResult['status'],
            'transaction_id' => $mockResult['transaction_id'],
        ]);

        if ($mockResult['status'] === 'APROVADO') {
            $order->update(['status' => 'COZINHA']);

            return response()->json([
                'message' => 'Pagamento aprovado com sucesso.',
                'payment' => $payment,
                'order_status' => 'COZINHA',
            ]);
        }

        $order->update(['status' => 'CANCELADO']);

        return response()->json([
            'error' => 'PAGAMENTO_RECUSADO',
            'message' => 'Pagamento recusado pelo gateway.',
            'payment' => $payment,
            'order_status' => 'CANCELADO',
        ], 422);
    }

    private function mockPaymentGateway($amount)
    {
        $approved = $amount < 1000;

        return [
            'status' => $approved ? 'APROVADO' : 'RECUSADO',
            'transaction_id' => $approved ? 'TXN-' . strtoupper(uniqid()) : null,
        ];
    }

    public function show($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'error' => 'PEDIDO_NAO_ENCONTRADO',
                'message' => 'Pedido não encontrado.',
            ], 404);
        }

        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json([
                'error' => 'PAGAMENTO_NAO_ENCONTRADO',
                'message' => 'Pagamento não encontrado para este pedido.',
            ], 404);
        }

        return response()->json($payment);
    }
}