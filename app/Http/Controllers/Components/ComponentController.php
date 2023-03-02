<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $productVariantsCollections = new Collection();
        for($i = 0; $i < 5; $i++){
            $productVariant = (object)[
                'label'           => 'Product Variant '.$i,
                'value'           => '1',
                'extraAttributes' => (object)[
                    'color' => 'black'.$i,
                    'size'  => 'S'.$i,
                ],
            ];
            $productVariantsCollections->push($productVariant);
        }
        $productVariantsCollections = json_encode($productVariantsCollections);

        return view('dashboard.demo_components.vs-dropdown', compact('productVariantsCollections'));
    }

    /**
     * Display the resource page of checkbox.
     * @return \Illuminate\Http\Response
     */
    public function checkbox(){
        return view('dashboard.demo_components.vs-checkbox');
    }

    /**
     * Display the resource page of table.
     * @return \Illuminate\Http\Response
     */
    public function table(){
        return view('dashboard.demo_components.vs-table');
    }

}
