<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Http\Resources\Faculty\FacultyResource;
use App\Helpers\Constants;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $faculties = Faculty::query()
            ->search($request->search)
            ->paginate(Constants::PER_PAGE);
        
        return FacultyResource::collection($faculties);
    }
}
