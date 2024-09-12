<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(){
        $ordersCount = Order::count();
        $totalRevenue = Order::where('status','delivered')->sum('total_amount');
        $productsCount = Product::count();
        $data = [
            'ordersCount' => $ordersCount,
            'totalRevenue' => $totalRevenue,
            'productsCount' => $productsCount
        ];
        // dd($orderCount,$totalRevenue,$productCount);
        return response()->json(['data' => $data]);
    }
}
