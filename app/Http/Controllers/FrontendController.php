<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

class FrontendController extends Controller
{
    public function loadCampaign(Campaign $campaign)
    {
        $game = null;

        return view('frontend.index')
            ->with('data', $game);
    }

    public function placeholder()
    {
        return view('frontend.placeholder');
    }
}
