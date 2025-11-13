# 04 - Application Architecture

## 🏗️ Architectural Overview

The Lumi POS system follows a **layered architecture** with strict separation of concerns, implementing **SOLID principles** and industry-standard **design patterns**.

---

## 📐 Layered Architecture

```
┌───────────────────────────────────────────────────────────┐
│                    Presentation Layer                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │   Routes     │  │ Controllers  │  │ Blade Views  │   │
│  │  (web.php)   │  │   (HTTP)     │  │  (Templates) │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
└───────────────────────────────────────────────────────────┘
                           ↓
┌───────────────────────────────────────────────────────────┐
│                  Application Layer                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │  Middleware  │  │   Services   │  │ FormRequests │   │
│  │ (AdminAuth)  │  │   (Logic)    │  │ (Validation) │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
└───────────────────────────────────────────────────────────┘
                           ↓
┌───────────────────────────────────────────────────────────┐
│                   Domain Layer                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │   Models     │  │  Interfaces  │  │   Events     │   │
│  │  (Eloquent)  │  │ (Contracts)  │  │  (Future)    │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
└───────────────────────────────────────────────────────────┘
                           ↓
┌───────────────────────────────────────────────────────────┐
│                Data Access Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │ Repositories │  │ Query Scopes │  │  Migrations  │   │
│  │  (Database)  │  │   (Filters)  │  │   (Schema)   │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
└───────────────────────────────────────────────────────────┘
                           ↓
┌───────────────────────────────────────────────────────────┐
│                 Infrastructure Layer                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │
│  │   SQLite     │  │  Filesystem  │  │  PDF Engine  │   │
│  │  (Database)  │  │   (Storage)  │  │   (DomPDF)   │   │
│  └──────────────┘  └──────────────┘  └──────────────┘   │
└───────────────────────────────────────────────────────────┘
```

---

## 🎯 SOLID Principles Implementation

### S - Single Responsibility Principle
**Definition**: Each class should have only one reason to change.

**Implementation Examples**:

```php
// ❌ BAD: Controller doing too much
class OrderController {
    public function store(Request $request) {
        // Validation
        $validated = $request->validate([...]);
        
        // Business logic
        $subtotal = 0;
        foreach ($request->items as $item) {
            // Complex calculation...
        }
        
        // Database operation
        Order::create([...]);
        
        // Email notification
        Mail::send(...);
    }
}

// ✅ GOOD: Separated responsibilities
class OrderController {
    public function __construct(
        private OrderService $orderService
    ) {}
    
    public function store(StoreOrderRequest $request) {
        // Controller only handles HTTP request/response
        $order = $this->orderService->createOrder(
            $request->validated()
        );
        
        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully');
    }
}

class StoreOrderRequest extends FormRequest {
    // Only handles validation
    public function rules(): array {
        return [
            'items' => 'required|array|min:1',
            // ... more rules
        ];
    }
}

class OrderService {
    // Only handles business logic
    public function createOrder(array $data): Order {
        // Complex order creation logic
    }
}
```

**Our Implementation**:
- **Controllers**: Handle HTTP only (request → response)
- **Services**: Handle business logic
- **Repositories**: Handle database operations
- **FormRequests**: Handle validation
- **Models**: Handle data structure and relationships

---

### O - Open/Closed Principle
**Definition**: Open for extension, closed for modification.

**Implementation Example**:

```php
// ✅ Interface allows new payment methods without modifying core
interface PaymentMethodInterface {
    public function process(Order $order): bool;
}

class CashPayment implements PaymentMethodInterface {
    public function process(Order $order): bool {
        // Cash payment logic
        return true;
    }
}

class CardPayment implements PaymentMethodInterface {
    public function process(Order $order): bool {
        // Card payment logic (future: integrate with payment gateway)
        return true;
    }
}

// New payment method? Just add new class, no changes to existing code
class OnlinePayment implements PaymentMethodInterface {
    public function process(Order $order): bool {
        // Online payment logic
        return true;
    }
}
```

**Our Current Approach**:
- Repository interfaces allow swapping database implementations
- Service layer can be extended with new services
- Middleware can be added without modifying routes

---

### L - Liskov Substitution Principle
**Definition**: Subtypes must be substitutable for their base types.

**Implementation Example**:

```php
// ✅ Any repository implementation can replace the interface
interface OrderRepositoryInterface {
    public function find(int $id): ?Order;
    public function create(array $data): Order;
}

// Implementation 1: Eloquent
class OrderRepository implements OrderRepositoryInterface {
    public function find(int $id): ?Order {
        return Order::find($id);
    }
    
    public function create(array $data): Order {
        return Order::create($data);
    }
}

// Implementation 2: Could use different database (future)
class MongoOrderRepository implements OrderRepositoryInterface {
    public function find(int $id): ?Order {
        // MongoDB query
    }
    
    public function create(array $data): Order {
        // MongoDB insert
    }
}

// Service works with ANY implementation
class OrderService {
    public function __construct(
        private OrderRepositoryInterface $repo  // Works with any implementation!
    ) {}
}
```

**Our Implementation**:
- All repositories implement interfaces
- Controllers depend on interfaces, not concrete classes
- Easy to swap implementations for testing

---

### I - Interface Segregation Principle
**Definition**: Many client-specific interfaces are better than one general-purpose interface.

**Implementation Example**:

```php
// ❌ BAD: Fat interface forces implementation of unused methods
interface RepositoryInterface {
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function search(string $query);
    public function paginate(int $perPage);
    public function export();
    public function import();
}

// ✅ GOOD: Small, focused interfaces
interface ReadableRepositoryInterface {
    public function find(int $id);
    public function paginate(int $perPage);
}

interface WritableRepositoryInterface {
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}

interface SearchableRepositoryInterface {
    public function search(string $query);
}

// Only implement what you need
class CategoryRepository implements 
    ReadableRepositoryInterface, 
    WritableRepositoryInterface {
    // Only these methods, no unused ones
}
```

**Our Implementation**:
- Each repository interface is specific to its domain
- `OrderRepositoryInterface` ≠ `ItemRepositoryInterface`
- Methods are minimal and focused

---

### D - Dependency Inversion Principle
**Definition**: Depend on abstractions, not concretions.

**Implementation Example**:

```php
// ❌ BAD: Controller depends on concrete class
class OrderController {
    private $orderService;
    
    public function __construct() {
        $this->orderService = new OrderService();  // Hard dependency!
    }
}

// ✅ GOOD: Controller depends on interface
class OrderController {
    public function __construct(
        private OrderServiceInterface $orderService  // Abstraction!
    ) {}
}

// Service bound in provider
class AppServiceProvider extends ServiceProvider {
    public function register() {
        $this->app->bind(
            OrderServiceInterface::class, 
            OrderService::class
        );
    }
}
```

**Our Implementation**:
```php
// app/Providers/RepositoryServiceProvider.php
public function register(): void {
    $this->app->bind(
        CategoryRepositoryInterface::class, 
        CategoryRepository::class
    );
    
    $this->app->bind(
        ItemRepositoryInterface::class, 
        ItemRepository::class
    );
    
    $this->app->bind(
        OrderRepositoryInterface::class, 
        OrderRepository::class
    );
}
```

All controllers and services use dependency injection with interfaces!

---

## 🎨 Design Patterns

### 1. Repository Pattern

**Purpose**: Abstract database operations behind an interface.

**Structure**:
```
app/Repositories/
├── Contracts/
│   ├── CategoryRepositoryInterface.php
│   ├── ItemRepositoryInterface.php
│   └── OrderRepositoryInterface.php
├── CategoryRepository.php
├── ItemRepository.php
└── OrderRepository.php
```

**Example**:
```php
// Interface (contract)
namespace App\Repositories\Contracts;

interface OrderRepositoryInterface {
    public function paginate(int $perPage = 20): LengthAwarePaginator;
    public function find(int $id): ?Order;
    public function create(array $data): Order;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByDay(int $dayId): mixed;
    public function getPending(): mixed;
}

// Implementation
namespace App\Repositories;

class OrderRepository implements OrderRepositoryInterface {
    public function paginate(int $perPage = 20): LengthAwarePaginator {
        return Order::with(['day', 'items.itemUnit.item'])
            ->latest()
            ->paginate($perPage);
    }
    
    public function find(int $id): ?Order {
        return Order::with(['day', 'items.itemUnit.item.category'])
            ->find($id);
    }
    
    // ... other methods
}
```

**Benefits**:
- ✅ Database logic in one place
- ✅ Easy to test (mock repositories)
- ✅ Can swap database implementation
- ✅ Consistent query patterns

---

### 2. Service Layer Pattern

**Purpose**: Encapsulate business logic separate from controllers and models.

**Structure**:
```
app/Services/
├── DayService.php
├── InventoryService.php
├── OrderService.php
└── PdfGenerator.php
```

**Example**:
```php
namespace App\Services;

class OrderService {
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private InventoryService $inventoryService,
        private DayService $dayService
    ) {}
    
    public function createOrder(array $orderData): Order {
        return DB::transaction(function () use ($orderData) {
            // 1. Verify business day is open
            $currentDay = $this->dayService->getCurrentOpenDay();
            if (!$currentDay) {
                throw new \Exception('No business day is currently open.');
            }
            
            // 2. Create order
            $order = $this->orderRepository->create([
                'day_id' => $currentDay->id,
                'status' => Order::STATUS_PENDING,
                // ... more fields
            ]);
            
            // 3. Process each item
            foreach ($orderData['items'] as $itemData) {
                $this->processOrderItem($order, $itemData);
            }
            
            // 4. Calculate totals
            $this->calculateOrderTotals($order);
            
            // 5. Mark as completed
            $order->markAsCompleted();
            
            return $order;
        });
    }
    
    private function processOrderItem(Order $order, array $itemData): void {
        // Complex item processing logic
    }
    
    private function calculateOrderTotals(Order $order): void {
        // Complex calculation logic
    }
}
```

**Benefits**:
- ✅ Controllers stay thin (just routing)
- ✅ Reusable business logic
- ✅ Easier to test
- ✅ Single source of truth

---

### 3. Form Request Pattern

**Purpose**: Separate validation logic from controllers.

**Structure**:
```
app/Http/Requests/
├── StoreCategoryRequest.php
├── UpdateCategoryRequest.php
├── StoreItemRequest.php
├── UpdateItemRequest.php
└── StoreOrderRequest.php
```

**Example**:
```php
namespace App\Http\Requests;

class StoreOrderRequest extends FormRequest {
    public function authorize(): bool {
        return true; // Handled by middleware
    }
    
    public function rules(): array {
        return [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'nullable|in:cash,card,other',
            'customer_email' => 'nullable|email|max:255',
        ];
    }
    
    public function messages(): array {
        return [
            'items.required' => 'At least one item is required.',
            'items.*.item_id.exists' => 'Selected item does not exist.',
        ];
    }
}
```

**Usage in Controller**:
```php
public function store(StoreOrderRequest $request) {
    // Request is already validated!
    $order = $this->orderService->createOrder(
        $request->validated()
    );
    
    return redirect()->route('orders.show', $order);
}
```

**Benefits**:
- ✅ Clean controllers
- ✅ Reusable validation
- ✅ Custom error messages
- ✅ Automatic validation

---

### 4. Dependency Injection

**Purpose**: Provide dependencies through constructors (IoC container).

**Example**:
```php
// Controller receives dependencies automatically
class OrderController extends Controller {
    public function __construct(
        private OrderService $orderService,
        private PdfGenerator $pdfGenerator
    ) {}
    
    public function show(int $id) {
        // Use injected dependencies
        $order = $this->orderService->getOrderById($id);
        return view('orders.show', compact('order'));
    }
    
    public function downloadInvoice(int $id) {
        // Use injected PDF generator
        return $this->pdfGenerator->generateInvoice($id);
    }
}
```

**Benefits**:
- ✅ Loose coupling
- ✅ Easy testing (mock dependencies)
- ✅ Laravel handles instantiation
- ✅ Type safety

---

## 📁 Directory Structure Deep Dive

### Controllers (app/Http/Controllers/)

**Purpose**: Handle HTTP requests and return responses (thin layer).

**Naming Convention**: `{Entity}Controller` (e.g., `OrderController`)

**Responsibilities**:
- ✅ Receive HTTP request
- ✅ Validate input (via FormRequest)
- ✅ Call service layer
- ✅ Return view or redirect
- ❌ NO business logic
- ❌ NO database queries

**Example**:
```php
class OrderController extends Controller {
    public function __construct(
        private OrderService $orderService
    ) {}
    
    public function index() {
        $orders = $this->orderService->getAllOrders();
        return view('admin.orders.index', compact('orders'));
    }
    
    public function store(StoreOrderRequest $request) {
        $order = $this->orderService->createOrder($request->validated());
        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order created successfully');
    }
}
```

---

### Services (app/Services/)

**Purpose**: Contain business logic and orchestrate operations.

**Naming Convention**: `{Domain}Service` (e.g., `OrderService`)

**Responsibilities**:
- ✅ Business logic
- ✅ Orchestrate multiple repositories
- ✅ Transaction management
- ✅ Complex calculations
- ❌ NO HTTP concerns (request/response)
- ❌ NO direct database queries (use repositories)

**Example**:
```php
class InventoryService {
    public function addItemUnits(Item $item, int $quantity, ?float $price = null): Collection {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }
        
        DB::beginTransaction();
        try {
            $units = collect();
            for ($i = 0; $i < $quantity; $i++) {
                $units->push(ItemUnit::create([
                    'item_id' => $item->id,
                    'quantity' => 1,
                    'price' => $price ?? $item->price,
                    'status' => ItemUnit::STATUS_AVAILABLE,
                ]));
            }
            DB::commit();
            return $units;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

---

### Repositories (app/Repositories/)

**Purpose**: Abstract database operations.

**Naming Convention**: `{Entity}Repository` + Interface

**Responsibilities**:
- ✅ Database queries
- ✅ Eloquent operations
- ✅ Query optimization (eager loading)
- ❌ NO business logic
- ❌ NO validation

**Example**:
```php
class ItemRepository implements ItemRepositoryInterface {
    public function findWithStock(int $id): ?Item {
        return Item::with(['category', 'units' => function ($query) {
                $query->available();
            }])
            ->find($id);
    }
    
    public function getLowStock(int $threshold = 10): Collection {
        return Item::select('items.*')
            ->leftJoin('item_units', function ($join) {
                $join->on('items.id', '=', 'item_units.item_id')
                    ->where('item_units.status', '=', 'available');
            })
            ->groupBy('items.id')
            ->havingRaw('COUNT(item_units.id) < ?', [$threshold])
            ->get();
    }
}
```

---

### Models (app/Models/)

**Purpose**: Represent database tables and define relationships.

**Naming Convention**: Singular noun (e.g., `Order`, `Item`, `Category`)

**Responsibilities**:
- ✅ Define relationships
- ✅ Define casts and attributes
- ✅ Query scopes
- ✅ Accessors/Mutators
- ❌ NO complex business logic
- ❌ NO HTTP concerns

**Example**:
```php
class Order extends Model {
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['day_id', 'subtotal', 'total', ...];
    
    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
    ];
    
    // Relationships
    public function items(): HasMany {
        return $this->hasMany(OrderItem::class);
    }
    
    public function day(): BelongsTo {
        return $this->belongsTo(Day::class);
    }
    
    // Query Scopes
    public function scopeCompleted($query) {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    // Business Methods
    public function markAsCompleted(): void {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }
}
```

---

## 🔄 Request Flow

### Example: Creating an Order

```
1. User submits form
   POST /admin/orders
   ↓
2. Route matches
   Route::post('orders', [OrderController::class, 'store'])
   ↓
3. Middleware executes
   AdminAuth → Check session
   ↓
4. FormRequest validates
   StoreOrderRequest → Validate input
   ↓
5. Controller receives request
   OrderController@store(StoreOrderRequest $request)
   ↓
6. Controller calls service
   $orderService->createOrder($request->validated())
   ↓
7. Service orchestrates
   - DayService → Get current day
   - OrderRepository → Create order
   - InventoryService → Decrease stock
   - Calculate totals
   ↓
8. Service returns result
   return Order $order
   ↓
9. Controller returns response
   return redirect()->route('orders.show', $order)
   ↓
10. View renders
    Blade template → HTML
```

---

## 🧪 Testability

### Unit Testing Services

```php
class OrderServiceTest extends TestCase {
    public function test_creates_order_with_valid_data() {
        // Arrange: Mock dependencies
        $orderRepo = Mockery::mock(OrderRepositoryInterface::class);
        $inventoryService = Mockery::mock(InventoryService::class);
        $dayService = Mockery::mock(DayService::class);
        
        $service = new OrderService($orderRepo, $inventoryService, $dayService);
        
        // Act: Call method
        $order = $service->createOrder([...]);
        
        // Assert: Verify result
        $this->assertInstanceOf(Order::class, $order);
    }
}
```

### Feature Testing Controllers

```php
class OrderControllerTest extends TestCase {
    public function test_authenticated_user_can_create_order() {
        // Arrange: Login
        $this->actingAsAdmin();
        
        // Act: Submit form
        $response = $this->post('/admin/orders', [
            'items' => [...]
        ]);
        
        // Assert: Order created
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [...]);
    }
}
```

---

**Next**: [05-REPOSITORY-PATTERN.md](./05-REPOSITORY-PATTERN.md) - Deep dive into repositories
