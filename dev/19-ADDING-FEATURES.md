# 19 - Adding New Features

## 🚀 Step-by-Step Guide to Adding Features

This guide provides a structured approach to extending the Lumi POS system with new features while maintaining code quality and architectural consistency.

---

## 📋 Feature Development Workflow

### Phase 1: Planning & Analysis

#### 1. Define the Feature
- **What**: Clear description of functionality
- **Why**: Business value and user benefit
- **Who**: Target users (admin, cashier, customer)
- **When**: Priority and dependencies

#### 2. Design the Solution
- **Database**: What tables/columns needed?
- **UI/UX**: What screens/forms required?
- **Business Logic**: What rules and calculations?
- **Integration**: How does it fit with existing features?

#### 3. Break Down Tasks
Create a checklist:
- [ ] Database migration
- [ ] Model creation
- [ ] Repository interface & implementation
- [ ] Service layer
- [ ] Controller methods
- [ ] FormRequests (validation)
- [ ] Routes
- [ ] Blade views
- [ ] Tests
- [ ] Documentation

---

## 🎯 Example: Adding Customer Management

Let's walk through adding a complete "Customer Management" feature.

### Step 1: Database Design

**Create Migration**:
```bash
php artisan make:migration create_customers_table
```

**Edit Migration** (`database/migrations/YYYY_MM_DD_create_customers_table.php`):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Add customer_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('day_id')
                ->constrained('customers')
                ->onDelete('set null');
        });
    }
    
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
        
        Schema::dropIfExists('customers');
    }
};
```

**Run Migration**:
```bash
php artisan migrate
```

---

### Step 2: Create Model

```bash
php artisan make:model Customer
```

**Edit Model** (`app/Models/Customer.php`):
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'total_spent',
        'total_orders',
    ];
    
    protected $casts = [
        'total_spent' => 'decimal:2',
        'total_orders' => 'integer',
    ];
    
    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    // Query Scopes
    public function scopeTopSpenders($query, int $limit = 10)
    {
        return $query->orderBy('total_spent', 'desc')->limit($limit);
    }
    
    // Business Methods
    public function updateOrderStats(): void
    {
        $this->update([
            'total_orders' => $this->orders()->completed()->count(),
            'total_spent' => $this->orders()->completed()->sum('total'),
        ]);
    }
}
```

**Update Order Model** (`app/Models/Order.php`):
```php
// Add to Order model
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}
```

---

### Step 3: Create Repository

**Create Interface** (`app/Repositories/Contracts/CustomerRepositoryInterface.php`):
```php
<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator;
    public function find(int $id): ?Customer;
    public function findByEmail(string $email): ?Customer;
    public function create(array $data): Customer;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getTopSpenders(int $limit = 10): Collection;
}
```

**Create Implementation** (`app/Repositories/CustomerRepository.php`):
```php
<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Customer::withCount('orders')
            ->latest()
            ->paginate($perPage);
    }
    
    public function find(int $id): ?Customer
    {
        return Customer::with('orders')->find($id);
    }
    
    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }
    
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }
    
    public function update(int $id, array $data): bool
    {
        $customer = Customer::find($id);
        return $customer ? $customer->update($data) : false;
    }
    
    public function delete(int $id): bool
    {
        $customer = Customer::find($id);
        return $customer ? $customer->delete() : false;
    }
    
    public function getTopSpenders(int $limit = 10): Collection
    {
        return Customer::topSpenders($limit)->get();
    }
}
```

**Register in Service Provider** (`app/Providers/RepositoryServiceProvider.php`):
```php
public function register(): void
{
    // ... existing bindings
    
    $this->app->bind(
        \App\Repositories\Contracts\CustomerRepositoryInterface::class,
        \App\Repositories\CustomerRepository::class
    );
}
```

---

### Step 4: Create Service Layer

**Create Service** (`app/Services/CustomerService.php`):
```php
<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}
    
    public function createCustomer(array $data): Customer
    {
        // Check for existing customer by email
        if (isset($data['email'])) {
            $existing = $this->customerRepository->findByEmail($data['email']);
            if ($existing) {
                throw new \Exception('Customer with this email already exists.');
            }
        }
        
        return $this->customerRepository->create($data);
    }
    
    public function updateCustomer(int $id, array $data): Customer
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            throw new \Exception('Customer not found.');
        }
        
        // Check email uniqueness if changed
        if (isset($data['email']) && $data['email'] !== $customer->email) {
            $existing = $this->customerRepository->findByEmail($data['email']);
            if ($existing) {
                throw new \Exception('Email already in use by another customer.');
            }
        }
        
        $this->customerRepository->update($id, $data);
        
        return $this->customerRepository->find($id);
    }
    
    public function deleteCustomer(int $id): bool
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            throw new \Exception('Customer not found.');
        }
        
        // Don't delete if has orders (business rule)
        if ($customer->orders()->count() > 0) {
            throw new \Exception('Cannot delete customer with existing orders.');
        }
        
        return $this->customerRepository->delete($id);
    }
}
```

---

### Step 5: Create FormRequests

```bash
php artisan make:request StoreCustomerRequest
php artisan make:request UpdateCustomerRequest
```

**StoreCustomerRequest**:
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handled by middleware
    }
    
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'email.unique' => 'A customer with this email already exists.',
        ];
    }
}
```

**UpdateCustomerRequest**:
```php
public function rules(): array
{
    $customerId = $this->route('customer'); // Get ID from route
    
    return [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:customers,email,' . $customerId,
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
    ];
}
```

---

### Step 6: Create Controller

```bash
php artisan make:controller CustomerController --resource
```

**Edit Controller** (`app/Http/Controllers/CustomerController.php`):
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerService $customerService
    ) {}
    
    public function index()
    {
        $customers = $this->customerRepository->paginate(20);
        return view('admin.customers.index', compact('customers'));
    }
    
    public function create()
    {
        return view('admin.customers.create');
    }
    
    public function store(StoreCustomerRequest $request)
    {
        try {
            $customer = $this->customerService->createCustomer(
                $request->validated()
            );
            
            return redirect()
                ->route('customers.show', $customer)
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    public function show(int $id)
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            abort(404);
        }
        
        return view('admin.customers.show', compact('customer'));
    }
    
    public function edit(int $id)
    {
        $customer = $this->customerRepository->find($id);
        
        if (!$customer) {
            abort(404);
        }
        
        return view('admin.customers.edit', compact('customer'));
    }
    
    public function update(UpdateCustomerRequest $request, int $id)
    {
        try {
            $customer = $this->customerService->updateCustomer(
                $id,
                $request->validated()
            );
            
            return redirect()
                ->route('customers.show', $customer)
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    public function destroy(int $id)
    {
        try {
            $this->customerService->deleteCustomer($id);
            
            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
```

---

### Step 7: Add Routes

**Edit** `routes/web.php`:
```php
// Inside admin middleware group
Route::prefix('admin')->middleware(AdminAuth::class)->group(function () {
    // ... existing routes
    
    // Customer CRUD
    Route::resource('customers', CustomerController::class);
});
```

---

### Step 8: Create Views

**Index View** (`resources/views/admin/customers/index.blade.php`):
```blade
@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Customers</h1>
        <a href="{{ route('customers.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded">
            Add Customer
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Phone</th>
                    <th class="px-6 py-3 text-left">Orders</th>
                    <th class="px-6 py-3 text-left">Total Spent</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4">{{ $customer->name }}</td>
                    <td class="px-6 py-4">{{ $customer->email ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $customer->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $customer->orders_count }}</td>
                    <td class="px-6 py-4">{{ config('cashier.currency') }}{{ number_format($customer->total_spent, 2) }}</td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="{{ route('customers.show', $customer) }}" 
                           class="text-blue-500">View</a>
                        <a href="{{ route('customers.edit', $customer) }}" 
                           class="text-green-500">Edit</a>
                        <form action="{{ route('customers.destroy', $customer) }}" 
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-500"
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No customers found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
```

**Create/Edit Forms**: Similar patterns, use Blade components for form fields.

---

### Step 9: Add Navigation Link

**Edit** `resources/views/layouts/admin.blade.php`:
```blade
<!-- In navigation menu -->
<a href="{{ route('customers.index') }}" 
   class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
    <i class="bi bi-people"></i>
    Customers
</a>
```

---

### Step 10: Add Translations

**Edit** `resources/lang/en/messages.php`:
```php
'customers' => 'Customers',
'add_customer' => 'Add Customer',
'customer_name' => 'Customer Name',
'customer_email' => 'Email',
// ... more translations
```

**Edit** `resources/lang/ar/messages.php`:
```php
'customers' => 'العملاء',
'add_customer' => 'إضافة عميل',
// ... Arabic translations
```

---

### Step 11: Write Tests

**Feature Test** (`tests/Feature/CustomerTest.php`):
```php
<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_list_customers()
    {
        Customer::factory()->count(3)->create();
        
        $response = $this->actingAsAdmin()
            ->get(route('customers.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('customers');
    }
    
    public function test_can_create_customer()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ];
        
        $response = $this->actingAsAdmin()
            ->post(route('customers.store'), $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
    }
    
    public function test_cannot_create_duplicate_email()
    {
        Customer::factory()->create(['email' => 'john@example.com']);
        
        $response = $this->actingAsAdmin()
            ->post(route('customers.store'), [
                'name' => 'Jane Doe',
                'email' => 'john@example.com',
            ]);
        
        $response->assertSessionHasErrors('email');
    }
}
```

**Run Tests**:
```bash
php artisan test --filter CustomerTest
```

---

## ✅ Feature Checklist

Before considering a feature complete:

- [ ] **Database**
  - [ ] Migration created and tested
  - [ ] Rollback tested
  - [ ] Foreign keys properly set
  - [ ] Indexes on frequently queried columns

- [ ] **Models**
  - [ ] Fillable/guarded defined
  - [ ] Casts defined
  - [ ] Relationships defined
  - [ ] Query scopes added
  - [ ] Factory created (for testing)

- [ ] **Repository**
  - [ ] Interface created
  - [ ] Implementation created
  - [ ] Registered in service provider
  - [ ] Eager loading for relationships

- [ ] **Service Layer**
  - [ ] Business logic encapsulated
  - [ ] Validation rules enforced
  - [ ] Transactions used where needed
  - [ ] Error handling implemented

- [ ] **Controllers**
  - [ ] RESTful methods implemented
  - [ ] FormRequests used for validation
  - [ ] Services injected via DI
  - [ ] Proper HTTP responses

- [ ] **Routes**
  - [ ] Routes registered
  - [ ] Middleware applied
  - [ ] Route names follow convention

- [ ] **Views**
  - [ ] All CRUD views created
  - [ ] Forms have CSRF protection
  - [ ] Validation errors displayed
  - [ ] Success/error messages shown
  - [ ] Responsive design

- [ ] **Translations**
  - [ ] English translations added
  - [ ] Arabic translations added
  - [ ] All UI text translatable

- [ ] **Tests**
  - [ ] Feature tests written
  - [ ] Unit tests written (if complex logic)
  - [ ] All tests passing

- [ ] **Documentation**
  - [ ] Feature documented
  - [ ] API endpoints documented (if any)
  - [ ] Complex logic explained

---

## 🎓 Best Practices

### DO ✅
- Follow existing patterns
- Use dependency injection
- Validate all input
- Handle errors gracefully
- Write descriptive commit messages
- Add comments for complex logic
- Test your changes

### DON'T ❌
- Put business logic in controllers
- Direct database queries in controllers
- Skip validation
- Ignore error handling
- Hard-code values
- Copy-paste code
- Skip testing

---

## 🔧 Common Patterns

### Adding a New Report

1. Create method in ReportController
2. Add service method for data aggregation
3. Create Blade view with Chart.js
4. Add route
5. Add navigation link

### Adding New PDF Export

1. Create method in PdfGenerator service
2. Create Blade view for PDF layout
3. Add controller method
4. Add route and link

### Adding New Setting

1. Add column to shop_settings table
2. Update ShopSettings model
3. Add form field in settings view
4. Update controller to handle new field

---

**Next**: [20-TROUBLESHOOTING.md](./20-TROUBLESHOOTING.md) - Common issues and solutions
