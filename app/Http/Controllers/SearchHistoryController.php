<?php

namespace App\Http\Controllers;

use App\Contracts\TravelMateServiceInterface;
use Illuminate\Http\Request;

class SearchHistoryController extends Controller
{
    protected TravelMateServiceInterface $travelMateService;

    public function __construct(TravelMateServiceInterface $travelMateService)
    {
        $this->travelMateService = $travelMateService;
    }

    public function index(Request $request)
    {
        $history = $this->travelMateService->getSearchHistory($request->user()->id);
        return response()->json(['status'=>'success','data'=>$history]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword'=>['required','string'],
            'type'=>['required','string'],
        ]);

        $history = $this->travelMateService->saveSearchHistory(
            $request->user()->id,
            $validated['keyword'],
            $validated['type']
        );

        return response()->json(['status'=>'success','message'=>'Search saved','data'=>$history]);
    }
}