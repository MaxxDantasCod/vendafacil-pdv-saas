<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TakePOSController extends Controller
{
    public function __invoke(): View
    {
        return view('pdv.show', [
            'iframeSrc' => config('dolibarr.takepos_url'),
        ]);
    }
}
