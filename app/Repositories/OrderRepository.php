<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Order::with(['day', 'items.itemUnit.item'])
            ->latest()
            ->paginate($perPage);
    }
    
    public function find(int $id): ?Order
    {
        return Order::with(['day', 'items.itemUnit.item.category'])->find($id);
    }
    
    public function create(array $data): Order
    {
        return Order::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $order = Order::find($id);
        
        if (!$order) {
            return false;
        }
        
        return $order->update($data);
    }
    
    public function delete(int $id): bool
    {
        $order = Order::find($id);
        
        if (!$order) {
            return false;
        }
        
        return $order->delete();
    }
    
    public function getByDay(int $dayId): mixed
    {
        return Order::where('day_id', $dayId)->get();
    }
    
    public function getPending(): mixed
    {
        return Order::where('status', Order::STATUS_PENDING)->get();
    }
}
