<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SubscriptionGatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function test_basic_plan_cannot_add_more_than_100_products()
    {
        // 1. Setup Company with BASIC plan
        $company = Company::create([
            'name' => 'Basic Co', 
            'legal_entity' => 'PT',
            'subscription_plan' => 'basic',
            'max_products' => 5 // artificially low for test
        ]);
        
        $user = User::factory()->create(['company_id' => $company->id, 'role' => 'manager']);
        Auth::login($user);

        $category = Category::create(['name' => 'Test', 'slug' => 'test', 'company_id' => $company->id]);

        // 2. Fill to limit
        for ($i = 0; $i < 5; $i++) {
            Product::create([
                'company_id' => $company->id,
                'category_id' => $category->id,
                'name' => "Product $i",
                'sku' => "SKU-$i",
                'price' => 100
            ]);
        }

        // 3. Try to exceed limit
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Subscription limit reached');

        // Logic should be in Product model or an observer or a service
        Product::create([
            'company_id' => $company->id,
            'category_id' => $category->id,
            'name' => "Illegal Product",
            'sku' => "SKU-ILLEGAL",
            'price' => 100
        ]);
    }
}
