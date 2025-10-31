<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ItemRepositoryInterface;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        private ItemRepositoryInterface $itemRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private InventoryService $inventoryService
    ) {}

    /**
     * Display a listing of items.
     */
    public function index(): View
    {
        $items = $this->itemRepository->all();

        return view('admin.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin.items.create', compact('categories'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(StoreItemRequest $request): RedirectResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $initialStock = $data['initial_stock'] ?? 0;
                unset($data['initial_stock']); // Remove from item data

                // Create the item
                $item = $this->itemRepository->create($data);

                // Add initial stock if specified
                if ($initialStock > 0) {
                    $this->inventoryService->addItemUnits($item, $initialStock);
                }

                return redirect()->route('items.show', $item)
                    ->with('success', "Item '{$item->name}' created successfully with {$initialStock} units in stock!");
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): View
    {
        $item->load(['category', 'units']);

        return view('admin.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin.items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        try {
            $this->itemRepository->update($item->id, $request->validated());

            return redirect()->route('items.show', $item)
                ->with('success', "Item '{$item->name}' updated successfully!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        try {
            // Check if item has units
            if ($item->units()->count() > 0) {
                return back()->with('error', 'Cannot delete item with existing units. Please remove all units first.');
            }

            $this->itemRepository->delete($item->id);

            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete item: ' . $e->getMessage());
        }
    }

    /**
     * Add stock units to an item.
     */
    public function addStock(Request $request, Item $item): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        try {
            $quantity = $request->input('quantity');
            $this->inventoryService->addItemUnits($item, $quantity);

            return back()->with('success', "Added {$quantity} units to '{$item->name}' successfully!");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }

    /**
     * Export all items to PDF.
     */
    public function exportPdf()
    {
        $items = Item::with(['category', 'units'])
            ->withCount([
                'units as available_stock' => function ($query) {
                    $query->where('status', 'available');
                },
                'units as sold_stock' => function ($query) {
                    $query->where('status', 'sold');
                }
            ])
            ->get();

        // Calculate totals
        $totalItems = $items->count();
        $totalInventoryValue = $items->sum(function ($item) {
            return $item->price * $item->available_stock;
        });
        $totalAvailableUnits = $items->sum('available_stock');
        $totalSoldUnits = $items->sum('sold_stock');
        $lowStockItems = $items->filter(fn($item) => $item->available_stock > 0 && $item->available_stock < 5)->count();
        $outOfStockItems = $items->filter(fn($item) => $item->available_stock == 0)->count();

        $pdf = Pdf::loadView('admin.items.pdf', compact(
            'items',
            'totalItems',
            'totalInventoryValue',
            'totalAvailableUnits',
            'totalSoldUnits',
            'lowStockItems',
            'outOfStockItems'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('items_inventory_' . now()->format('Y-m-d') . '.pdf');
    }
}
