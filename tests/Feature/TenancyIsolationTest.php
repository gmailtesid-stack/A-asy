<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TenancyIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function test_user_cannot_see_products_from_another_company()
    {
        // 1. Create Company A and its data
        $companyA = Company::create(['name' => 'Company A', 'legal_entity' => 'PT']);
        $userA = User::factory()->create(['company_id' => $companyA->id]);
        $categoryA = Category::create(['name' => 'Cat A', 'slug' => 'cat-a', 'company_id' => $companyA->id]);
        $productA = Product::create([
            'company_id' => $companyA->id,
            'category_id' => $categoryA->id,
            'name' => 'Secret Product A',
            'sku' => 'SKU-A',
            'price' => 1000
        ]);

        // 2. Create Company B and its data
        $companyB = Company::create(['name' => 'Company B', 'legal_entity' => 'CV']);
        $userB = User::factory()->create(['company_id' => $companyB->id]);
        $categoryB = Category::create(['name' => 'Cat B', 'slug' => 'cat-b', 'company_id' => $companyB->id]);
        $productB = Product::create([
            'company_id' => $companyB->id,
            'category_id' => $categoryB->id,
            'name' => 'Secret Product B',
            'sku' => 'SKU-B',
            'price' => 2000
        ]);

        // 3. Act as User A
        $this->actingAs($userA);

        // 4. Assert: User A can see Product A but NOT Product B
        $visibleProducts = Product::all();
        
        $this->assertTrue($visibleProducts->contains($productA));
        $this->assertFalse($visibleProducts->contains($productB), "SECURITY ALERT: User A can see Company B's product!");
        $this->assertEquals(1, $visibleProducts->count());
    }

    /** @test */
    public function test_user_cannot_update_data_from_another_company()
    {
        $companyA = Company::create(['name' => 'Company A', 'legal_entity' => 'PT']);
        $userA = User::factory()->create(['company_id' => $companyA->id, 'role' => 'manager']);

        $companyB = Company::create(['name' => 'Company B', 'legal_entity' => 'PT']);
        $categoryB = Category::create(['name' => 'Cat B', 'slug' => 'cat-b', 'company_id' => $companyB->id]);
        $productB = Product::create([
            'company_id' => $companyB->id,
            'category_id' => $categoryB->id,
            'name' => 'Original B',
            'sku' => 'SKU-B',
            'price' => 2000
        ]);

        // Login as User A
        Auth::login($userA);

        // Attempting to update product B directly should throw exception
        $this->expectException(\Illuminate\Auth\Access\AuthorizationException::class);
        $productB->update(['name' => 'Hacked by A']);
        
        // Ensure data was NOT changed in DB (this line runs only if exception NOT thrown)
        $this->assertDatabaseHas('products', [
            'id' => $productB->id,
            'name' => 'Original B'
        ]);
    }
}
