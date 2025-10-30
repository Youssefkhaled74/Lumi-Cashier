<?php

namespace App\Repositories\Contracts;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

interface ItemRepositoryInterface
{
    public function all(): Collection;
    
    public function find(int $id): ?Item;
    
    public function create(array $data): Item;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getInStock(): Collection;
    
    public function findBySku(string $sku): ?Item;
    
    public function findByBarcode(string $barcode): ?Item;
}
