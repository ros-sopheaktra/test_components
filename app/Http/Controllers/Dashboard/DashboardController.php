<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Shop;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get first shop
        $shop = Shop::first();
        // get product variant record
        $productVariants = ProductVariant::getProductVariantBasedOnAssociatedShopRecord($shop->id);
        if( !$productVariants->data ){
            return back()->with('error', $productVariants->messange);
        }
        $productVariants = $productVariants->data;

        return view('dashboard.drop-down', compact('productVariants'));
    }

}
