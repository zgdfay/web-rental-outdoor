<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gear;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GearController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/gears', [
            'gears' => Gear::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'price_per_day' => 'required|integer',
            'stock' => 'required|integer',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('gears', 'public');
        }

        Gear::create($validated);

        return back()->with('success', 'Gear added successfully.');
    }

    public function update(Request $request, Gear $gear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'price_per_day' => 'required|integer',
            'stock' => 'required|integer',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($gear->image_path) {
                Storage::disk('public')->delete($gear->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('gears', 'public');
        }

        $gear->update($validated);

        return back()->with('success', 'Gear updated successfully.');
    }

    public function destroy(Gear $gear)
    {
        if ($gear->image_path) {
            Storage::disk('public')->delete($gear->image_path);
        }
        $gear->delete();
        return back()->with('success', 'Gear deleted successfully.');
    }
}
