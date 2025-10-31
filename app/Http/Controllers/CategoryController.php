<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\ItemUnit;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = $this->categoryRepository->all();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            $category = $this->categoryRepository->create($request->validated());

            return redirect()->route('categories.index')
                ->with('success', "Category '{$category->name}' created successfully!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load('items');

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->categoryRepository->update($category->id, $request->validated());

            return redirect()->route('categories.index')
                ->with('success', "Category '{$category->name}' updated successfully!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            // Check if category has items
            if ($category->items()->count() > 0) {
                return back()->with('error', 'Cannot delete category with existing items. Please reassign or delete items first.');
            }

            $this->categoryRepository->delete($category->id);

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Export category report to PDF.
     */
    public function exportPdf()
    {
        $categories = Category::with(['items.units'])
            ->withCount('items')
            ->get()
            ->map(function ($category) {
                $availableStock = 0;
                $soldStock = 0;
                $inventoryValue = 0;

                foreach ($category->items as $item) {
                    $itemAvailable = $item->units()->where('status', ItemUnit::STATUS_AVAILABLE)->count();
                    $itemSold = $item->units()->where('status', ItemUnit::STATUS_SOLD)->count();
                    
                    $availableStock += $itemAvailable;
                    $soldStock += $itemSold;
                    $inventoryValue += $item->price * $itemAvailable;
                }

                $category->available_stock = $availableStock;
                $category->sold_stock = $itemSold;
                $category->inventory_value = $inventoryValue;
                $category->total_units = $availableStock + $soldStock;

                return $category;
            });

        // Calculate totals
        $totalCategories = $categories->count();
        $totalItems = $categories->sum('items_count');
        $totalInventoryValue = $categories->sum('inventory_value');
        $totalAvailableUnits = $categories->sum('available_stock');
        $totalSoldUnits = $categories->sum('sold_stock');

        $pdf = Pdf::loadView('admin.categories.pdf', compact(
            'categories',
            'totalCategories',
            'totalItems',
            'totalInventoryValue',
            'totalAvailableUnits',
            'totalSoldUnits'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('categories_report_' . now()->format('Y-m-d') . '.pdf');
    }
}
