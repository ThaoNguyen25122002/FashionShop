<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductOptionValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //
    public function storeOrder(Request $req)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $req->userId,
                'invoice_code' => $this->generateInvoiceCode(),
                'total_amount' => $req->totalAmount,
            ]);
            foreach ($req->order as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                ]);

                $variation = ProductOptionValue::where('product_id', $item['id'])
                    ->whereHas('color', function ($query) use ($item) {
                        $query->where('name', $item['color']);
                    })
                    ->whereHas('size', function ($query) use ($item) {
                        $query->where('name', $item['size']);
                    })->first();

                if (! $variation) {
                    return response()->json(['message' => 'Sản phẩm không tồn tại với màu sắc và kích cỡ này.'], 404);
                }
                // Kiểm tra tồn kho
                if ($variation->quantity < $item['quantity']) {
                    return response()->json(['message' => 'Không đủ hàng trong kho cho sản phẩm: '.$item['name']], 400);
                }
                // Trừ số lượng tồn kho
                ProductOptionValue::where('product_id', $variation->product_id)
                    ->where('color_id', $variation->color_id)
                    ->where('size_id', $variation->size_id)
                    ->update(['quantity' => $variation->quantity - $item['quantity']]);
            }
            DB::commit();

            return response()->json(['message' => 'Đặt hàng thành công!', 'order' => $order], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => 'Lỗi trong quá trình đặt hàng!', 'error' => $e->getMessage()], 500);
            // throw $e;
        }

    }

    public function generateInvoiceCode()
    {
        $date = now()->format('Ymd');

        $latestOrder = Order::whereDate('created_at', now())->latest()->first();
        $orderNumber = $latestOrder ? $latestOrder->id + 1 : 1;

        return 'MDH-'.$date.'-'.str_pad($orderNumber, 4, '0', STR_PAD_LEFT);
    }

    public function orderOfUser()
    {
        $id = auth()->id();
        $orders = Order::where('user_id', $id)->latest()->paginate(5); 
        return OrderResource::collection($orders)->response()->getData(true);
    }

    public function orderDetail($id){
        $order = Order::where('user_id', auth()->id())
              ->with('user','details.product', 'details.product.images')
              ->findOrFail($id);
        return new OrderResource($order);
    }
    public function getPendingOrders(){
        $orders = Order::with('user')->where('status', 'pending')->withCount('details')->latest()->paginate(5); 
        return OrderResource::collection($orders);
    }

    public function confirmOrder($orderId){
        $order = Order::findOrFail($orderId);
        $order->status = 'confirmed';
        $order->save();
        return response()->noContent();
    }
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);
        $order->status = $validated['status'];
        $order->save();
        return response()->noContent();
    }

    public function getOrders(){
        $orders = Order::with('user')->orderByRaw("FIELD(status, 'pending') DESC")->latest()->paginate(10); 
        return OrderResource::collection($orders);
    }
    public function search(Request $request)
    {
        // dd(123);
        $query = $request->input('query');
        // return $query;
        $orders = Order::with('user')->where('invoice_code', 'like', "%$query%")->paginate(5);
        return OrderResource::collection($orders);
    }
    public function orderDetailAdmin($id){
        $order = Order::with('user','details.product', 'details.product.images')->findOrFail($id);
        return new OrderResource($order);
    }
}
