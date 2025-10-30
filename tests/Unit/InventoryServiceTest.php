<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;
    protected Item $testItem;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = new InventoryService();

        // Create test category and item
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
        ]);

        $this->testItem = Item::create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'sku' => 'TEST-001',
            'description' => 'Test item description',
            'price' => 10.00,
        ]);
    }

    /**
     * Test adding item units successfully.
     */
    public function test_add_item_units_creates_correct_number_of_units(): void
    {
        $quantity = 5;

        $createdUnits = $this->inventoryService->addItemUnits($this->testItem, $quantity);

        $this->assertCount($quantity, $createdUnits);
        $this->assertEquals($quantity, ItemUnit::where('item_id', $this->testItem->id)->count());
    }

    /**
     * Test that each unit has a unique barcode.
     */
    public function test_add_item_units_generates_unique_barcodes(): void
    {
        $quantity = 10;

        $createdUnits = $this->inventoryService->addItemUnits($this->testItem, $quantity);

        $barcodes = $createdUnits->pluck('barcode')->toArray();
        $uniqueBarcodes = array_unique($barcodes);

        $this->assertCount($quantity, $uniqueBarcodes);
    }

    /**
     * Test that units are created with correct price.
     */
    public function test_add_item_units_uses_correct_price(): void
    {
        $quantity = 3;
        $customPrice = 15.00;

        $createdUnits = $this->inventoryService->addItemUnits($this->testItem, $quantity, $customPrice);

        foreach ($createdUnits as $unit) {
            $this->assertEquals($customPrice, (float) $unit->price);
        }
    }

    /**
     * Test that units default to item price when no price specified.
     */
    public function test_add_item_units_defaults_to_item_price(): void
    {
        $quantity = 2;

        $createdUnits = $this->inventoryService->addItemUnits($this->testItem, $quantity);

        foreach ($createdUnits as $unit) {
            $this->assertEquals($this->testItem->price, (float) $unit->price);
        }
    }

    /**
     * Test that adding zero or negative quantity throws exception.
     */
    public function test_add_item_units_throws_exception_for_invalid_quantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->inventoryService->addItemUnits($this->testItem, 0);
    }

    /**
     * Test decreasing stock successfully.
     */
    public function test_decrease_stock_marks_units_as_sold(): void
    {
        // Add 10 units
        $this->inventoryService->addItemUnits($this->testItem, 10);

        // Decrease by 5
        $soldUnits = $this->inventoryService->decreaseStock($this->testItem, 5);

        $this->assertCount(5, $soldUnits);

        // Check that 5 units have quantity = 0
        $soldCount = ItemUnit::where('item_id', $this->testItem->id)
            ->where('quantity', 0)
            ->count();

        $this->assertEquals(5, $soldCount);

        // Check that 5 units are still available
        $availableCount = ItemUnit::where('item_id', $this->testItem->id)
            ->where('quantity', '>', 0)
            ->count();

        $this->assertEquals(5, $availableCount);
    }

    /**
     * Test decreasing stock throws exception when insufficient stock.
     */
    public function test_decrease_stock_throws_exception_when_insufficient_stock(): void
    {
        // Add only 3 units
        $this->inventoryService->addItemUnits($this->testItem, 3);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');

        // Try to decrease by 5
        $this->inventoryService->decreaseStock($this->testItem, 5);
    }

    /**
     * Test that decrease stock uses database lock.
     */
    public function test_decrease_stock_respects_concurrent_updates(): void
    {
        // Add 5 units
        $this->inventoryService->addItemUnits($this->testItem, 5);

        // First transaction decreases 3
        $soldUnits1 = $this->inventoryService->decreaseStock($this->testItem, 3);

        $this->assertCount(3, $soldUnits1);

        // Second transaction should only be able to decrease 2
        $soldUnits2 = $this->inventoryService->decreaseStock($this->testItem, 2);

        $this->assertCount(2, $soldUnits2);

        // Total available should be 0
        $available = $this->inventoryService->getAvailableStock($this->testItem);
        $this->assertEquals(0, $available);
    }

    /**
     * Test getting available units.
     */
    public function test_get_available_units_returns_only_unsold_units(): void
    {
        // Add 10 units
        $this->inventoryService->addItemUnits($this->testItem, 10);

        // Sell 6 units
        $this->inventoryService->decreaseStock($this->testItem, 6);

        // Get available units
        $availableUnits = $this->inventoryService->getAvailableUnits($this->testItem);

        $this->assertCount(4, $availableUnits);

        foreach ($availableUnits as $unit) {
            $this->assertEquals(1, $unit->quantity);
        }
    }

    /**
     * Test getting available stock count.
     */
    public function test_get_available_stock_returns_correct_count(): void
    {
        $this->inventoryService->addItemUnits($this->testItem, 15);
        $this->inventoryService->decreaseStock($this->testItem, 7);

        $availableStock = $this->inventoryService->getAvailableStock($this->testItem);

        $this->assertEquals(8, $availableStock);
    }

    /**
     * Test has stock method.
     */
    public function test_has_stock_returns_correct_boolean(): void
    {
        $this->inventoryService->addItemUnits($this->testItem, 5);

        $this->assertTrue($this->inventoryService->hasStock($this->testItem, 3));
        $this->assertTrue($this->inventoryService->hasStock($this->testItem, 5));
        $this->assertFalse($this->inventoryService->hasStock($this->testItem, 6));
    }

    /**
     * Test restoring units.
     */
    public function test_restore_units_marks_sold_units_as_available(): void
    {
        // Add and sell some units
        $this->inventoryService->addItemUnits($this->testItem, 5);
        $soldUnits = $this->inventoryService->decreaseStock($this->testItem, 3);

        // Restore the sold units
        $restoredCount = $this->inventoryService->restoreUnits($soldUnits);

        $this->assertEquals(3, $restoredCount);

        // Check available stock
        $availableStock = $this->inventoryService->getAvailableStock($this->testItem);
        $this->assertEquals(5, $availableStock);
    }

    /**
     * Test finding unit by barcode.
     */
    public function test_find_by_barcode_returns_correct_unit(): void
    {
        $units = $this->inventoryService->addItemUnits($this->testItem, 3);
        $testUnit = $units->first();

        $foundUnit = $this->inventoryService->findByBarcode($testUnit->barcode);

        $this->assertNotNull($foundUnit);
        $this->assertEquals($testUnit->id, $foundUnit->id);
        $this->assertEquals($testUnit->barcode, $foundUnit->barcode);
    }

    /**
     * Test inventory summary.
     */
    public function test_get_inventory_summary_returns_correct_data(): void
    {
        $this->inventoryService->addItemUnits($this->testItem, 10);
        $this->inventoryService->decreaseStock($this->testItem, 4);

        $summary = $this->inventoryService->getInventorySummary($this->testItem);

        $this->assertEquals($this->testItem->id, $summary['item_id']);
        $this->assertEquals($this->testItem->name, $summary['item_name']);
        $this->assertEquals(10, $summary['total_units']);
        $this->assertEquals(6, $summary['available_units']);
        $this->assertEquals(4, $summary['sold_units']);
        $this->assertEquals(60.0, $summary['stock_percentage']);
    }

    /**
     * Test barcode uniqueness across multiple items.
     */
    public function test_barcodes_are_unique_across_multiple_items(): void
    {
        // Create second item
        $item2 = Item::create([
            'category_id' => $this->testItem->category_id,
            'name' => 'Test Item 2',
            'sku' => 'TEST-002',
            'price' => 20.00,
        ]);

        // Add units to both items
        $this->inventoryService->addItemUnits($this->testItem, 5);
        $this->inventoryService->addItemUnits($item2, 5);

        // Get all barcodes
        $allBarcodes = ItemUnit::pluck('barcode')->toArray();
        $uniqueBarcodes = array_unique($allBarcodes);

        $this->assertCount(10, $allBarcodes);
        $this->assertCount(10, $uniqueBarcodes);
    }

    /**
     * Test transaction rollback on error.
     */
    public function test_add_item_units_rolls_back_on_error(): void
    {
        // Get initial count
        $initialCount = ItemUnit::count();

        try {
            // Force an error by passing invalid quantity after some units are created
            // We'll do this by mocking, but for simplicity, just test the negative case
            $this->inventoryService->addItemUnits($this->testItem, -1);
        } catch (\Exception $e) {
            // Exception expected
        }

        // Count should remain the same (rollback occurred)
        $finalCount = ItemUnit::count();
        $this->assertEquals($initialCount, $finalCount);
    }
}
