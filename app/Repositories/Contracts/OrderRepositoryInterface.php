<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator;
    
    public function find(int $id): ?Order;
    
    public function create(array $data): Order;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function getByDay(int $dayId): mixed;
    
    public function getPending(): mixed;
}
