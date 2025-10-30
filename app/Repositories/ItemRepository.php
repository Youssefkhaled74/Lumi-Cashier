<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository implements ItemRepositoryInterface
{
    public function all(): Collection
    {
        return Item::with('category')->orderBy('name')->get();
    }
    
    public function find(int $id): ?Item
    {
        return Item::with('category', 'units')->find($id);
    }
    
    public function create(array $data): Item
    {
        return Item::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $item = Item::find($id);
        
        if (!$item) {
            return false;
        }
        
        return $item->update($data);
    }
    
    public function delete(int $id): bool
    {
        $item = Item::find($id);
        
        if (!$item) {
            return false;
        }
        
        return $item->delete();
    }
    
    public function getInStock(): Collection
    {
        return Item::with('category')->inStock()->get();
    }
    
    public function findBySku(string $sku): ?Item
    {
        return Item::where('sku', $sku)->first();
    }
    
    public function findByBarcode(string $barcode): ?Item
    {
        return Item::where('barcode', $barcode)->first();
    }
}
