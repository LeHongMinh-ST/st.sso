<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Http\Requests\Faculty\StoreFacultyRequest;
use App\Http\Requests\Faculty\UpdateFacultyRequest;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FacultyController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $faculties = Faculty::all();
        return Inertia::render('faculties/index', [
            'faculties' => $faculties
        ]);
    }

    public function create()
    {
        return Inertia::render('faculties/create');
    }

    public function store(StoreFacultyRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('faculties', 'public');
            $validated['logo'] = Storage::url($path);
        }

        Faculty::create($validated);

        return redirect()->route('faculties.index')
            ->with('message', 'Khoa đã được tạo thành công.');
    }

    public function edit(Faculty $faculty)
    {
        return Inertia::render('faculties/edit', [
            'faculty' => $faculty
        ]);
    }

    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            if ($faculty->logo) {
                Storage::delete(str_replace('/storage/', 'public/', $faculty->logo));
            }
            $path = $request->file('logo')->store('faculties', 'public');
            $validated['logo'] = Storage::url($path);
        }

        $faculty->update($validated);

        return redirect()->route('faculties.index')
            ->with('message', 'Khoa đã được cập nhật thành công.');
    }

    public function destroy(Faculty $faculty)
    {
        if ($faculty->logo) {
            Storage::delete(str_replace('/storage/', 'public/', $faculty->logo));
        }

        $faculty->delete();

        return redirect()->route('faculties.index')
            ->with('message', 'Khoa đã được xóa thành công.');
    }
}