<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::orderBy('name')->get();
    }
    
    public function find(int $id): ?Category
    {
        return Category::find($id);
    }
    
    public function create(array $data): Category
    {
        return Category::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }
        
        return $category->update($data);
    }
    
    public function delete(int $id): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }
        
        return $category->delete();
    }
    
    public function findBySlug(string $slug): ?Category
    {
        return Category::where('slug', $slug)->first();
    }
}
