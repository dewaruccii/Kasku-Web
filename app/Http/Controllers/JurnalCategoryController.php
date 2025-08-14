<?php

namespace App\Http\Controllers;

use App\Models\JurnalCategory;
use Illuminate\Http\Request;


class JurnalCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $category = JurnalCategory::get();
        return view('master.jurnal-category.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('master.jurnal-category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'is_active' => 'required'
        ]);
        $category = new JurnalCategory();
        $category->uuid = uuidGenerator();
        $category->name = $request->name;
        $category->is_active = $request->is_active;
        $category->save();
        return redirect()->route('master.jurnal-category.index')->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $category = JurnalCategory::where('uuid', $id)->first();
        if (!$category) {
            return redirect()->route('master.jurnal-category.index')->with('error', 'Data tidak ditemukan');
        }
        return view('master.jurnal-category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $category = JurnalCategory::where('uuid', $id)->first();
        if (!$category) {
            return redirect()->route('master.jurnal-category.index')->with('error', 'Data tidak ditemukan');
        }
        $category->name = $request->name;
        $category->is_active = $request->is_active;
        $category->save();
        return redirect()->route('master.jurnal-category.index')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
