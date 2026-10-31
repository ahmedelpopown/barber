<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $services = Service::query()
            ->with(['subServices' => fn ($query) => $query->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('welcome', compact('services'));
    }
}
