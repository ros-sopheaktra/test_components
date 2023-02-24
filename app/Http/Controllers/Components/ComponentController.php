<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    /**
     * Display the resource page of button.
     * @return \Illuminate\Http\Response
     */
    public function button(){
        return view('dashboard.demo_components.vs-button');
    }

    /**
     * Display the resource page of button.
     * @return \Illuminate\Http\Response
     */
    public function dropdown(){
        return view('dashboard.demo_components.vs-dropdown');
    }

    /**
     * Display the resource page of button.
     * @return \Illuminate\Http\Response
     */
    public function table(){
        return view('dashboard.demo_components.vs-table');
    }

}
