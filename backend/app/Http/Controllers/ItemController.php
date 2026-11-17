<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $items = Item::latest()->get();

        return response()->json([
            'message' => 'Daftar inventaris barang berhasil diambil.',
            'data' => $items,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'sku' => ['required', 'string', 'max:50', 'unique:items,sku'],
                'category' => ['required', 'string', 'max:100'],
                'stock' => ['required', 'integer', 'min:0'],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string'],
            ]);
        } catch (ValidationException $e) {
            // LOG TERSTRUKTUR: Validasi Gagal (WARNING)
            Log::warning('Validasi penambahan barang inventaris gagal', [
                'user_id' => $request->user()?->id ?? 'guest',
                'action' => 'validate_item_input',
                'reason' => 'Data input tidak memenuhi batasan validasi skema',
                'invalid_fields' => array_keys($e->errors()),
            ]);
            throw $e;
        }

        $item = Item::create($validated);

        // LOG TERSTRUKTUR: Sukses Menyimpan Data (INFO)
        Log::info('Inventaris barang baru berhasil ditambahkan', [
            'user_id' => $request->user()->id,
            'action' => 'store_inventory_item',
            'item_id' => $item->id,
            'sku' => $item->sku,
            'category' => $item->category,
            'status' => 'success',
        ]);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan ke inventaris.',
            'data' => $item,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): JsonResponse
    {
        return response()->json([
            'message' => 'Detail barang berhasil diambil.',
            'data' => $item,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50', Rule::unique('items', 'sku')->ignore($item->id)],
            'category' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Barang berhasil diperbarui.',
            'data' => $item,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus dari inventaris.',
        ], 200);
    }
}
