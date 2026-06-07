<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = FeedService::paginate($request->query('cursor'));

        return response()->json($page);
    }
}
