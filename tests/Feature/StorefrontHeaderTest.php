<?php

use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Settings\Models\Setting;

function storefrontHeaderXPath(string $html): DOMXPath
{
    $previous = libxml_use_internal_errors(true);
    $document = new DOMDocument;
    $document->loadHTML($html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return new DOMXPath($document);
}

afterEach(fn () => Cache::forget('settings'));

test('the header navbar shows all active top-level categories in configured order', function () {
    $parent = ProductCategory::factory()->create(['name' => 'Parent', 'sort_order' => 0]);
    ProductCategory::factory()->featured()->create(['name' => 'Zulu', 'sort_order' => 2]);
    ProductCategory::factory()->featured()->create(['name' => 'Alpha', 'sort_order' => 2]);
    ProductCategory::factory()->featured()->create(['name' => 'Early', 'sort_order' => 1]);
    ProductCategory::factory()->create(['name' => 'Late', 'sort_order' => 3]);
    ProductCategory::factory()->active(false)->create(['name' => 'Inactive']);
    ProductCategory::factory()->create(['name' => 'Child', 'parent_id' => $parent->id, 'sort_order' => 4]);
    ProductCategory::factory()->create(['name' => 'Deleted'])->delete();

    $xpath = storefrontHeaderXPath($this->get('/shop')->assertOk()->getContent());
    $names = [];
    foreach ($xpath->query('//*[@data-category-nav]/a') as $link) {
        $names[] = trim($link->textContent);
    }

    expect($names)->toBe(['Parent', 'Early', 'Alpha', 'Zulu', 'Late']);
    // The drawer keeps its curated featured section.
    expect($xpath->query('//*[@data-mobile-featured-categories]/a')->length)->toBe(3);
});

test('header categories are available outside the homepage and update without a stale cache', function () {
    $category = ProductCategory::factory()->featured()->create(['name' => 'Header Category']);
    Setting::updateOrCreate(['group' => 'homepage', 'key' => 'show_featured_categories'], ['value' => '0', 'type' => 'boolean']);
    Cache::forget('settings');

    foreach (['/', '/login'] as $path) {
        $xpath = storefrontHeaderXPath($this->get($path)->assertOk()->getContent());
        expect(trim($xpath->query('//*[@data-category-nav]/a')->item(0)->textContent))->toBe('Header Category');
    }

    $category->update(['active' => false]);
    $xpath = storefrontHeaderXPath($this->get('/login')->assertOk()->getContent());
    expect(trim($xpath->query('//*[@data-category-nav]/a')->item(0)->textContent))->toBe('Shop');
});

test('category navigation identifies the current category', function () {
    $category = ProductCategory::factory()->featured()->create();
    $xpath = storefrontHeaderXPath($this->get(route('shop.category', [$category->id, $category->slug]))->assertOk()->getContent());

    expect($xpath->query('//*[@data-category-nav]/a[@aria-current="page"]')->length)->toBe(1)
        ->and(trim($xpath->query('//*[@data-category-nav]/a[@aria-current="page"]')->item(0)->textContent))->toBe($category->name)
        ->and($xpath->query('//*[@data-mobile-all-categories]/a[@aria-current="page"]')->length)->toBe(1);
});

test('the category sidebar lists all active categories including empty and nonfeatured categories', function () {
    ProductCategory::factory()->featured()->create(['name' => 'Featured', 'sort_order' => 2]);
    $ordinary = ProductCategory::factory()->create(['name' => 'Ordinary', 'sort_order' => 1]);
    ProductCategory::factory()->create(['name' => 'Alpha', 'sort_order' => 1]);
    ProductCategory::factory()->active(false)->create(['name' => 'Inactive']);
    ProductCategory::factory()->create(['name' => 'Deleted'])->delete();

    foreach (['/', '/shop', '/login'] as $path) {
        $xpath = storefrontHeaderXPath($this->get($path)->assertOk()->getContent());
        $links = $xpath->query('//*[@data-mobile-all-categories]/a');
        $names = [];
        foreach ($links as $link) {
            $names[] = trim($link->textContent);
        }

        expect($names)->toBe(['Alpha', 'Ordinary', 'Featured'])
            ->and($links->item(1)->getAttribute('href'))->toBe(route('shop.category', [$ordinary->id, $ordinary->slug]))
            ->and($xpath->query('//*[@data-category-nav]/a')->length)->toBe(3)
            ->and($xpath->query('//*[@data-mobile-category-panel][@hidden]')->length)->toBe(1);
    }

    $ordinary->update(['active' => false]);
    $xpath = storefrontHeaderXPath($this->get('/shop')->assertOk()->getContent());
    expect($xpath->query('//*[@data-mobile-all-categories]/a')->length)->toBe(2);
});

test('the empty category sidebar still provides a link to browse all products', function () {
    $xpath = storefrontHeaderXPath($this->get('/')->assertOk()->getContent());

    expect($xpath->query('//*[@data-mobile-all-categories]/a')->length)->toBe(0)
        ->and($xpath->query('//*[@data-mobile-category-panel]/a')->item(0)->getAttribute('href'))->toBe(route('shop.index'));
});

test('the mobile logo row has expandable search while preferences live in the sidebar', function () {
    $xpath = storefrontHeaderXPath($this->get('/')->assertOk()->getContent());

    expect($xpath->query('//*[@data-mobile-header]//*[@data-theme-toggle]')->length)->toBe(0)
        ->and($xpath->query('//*[@data-mobile-header]//form')->length)->toBe(0)
        ->and($xpath->query('//*[@data-mobile-header]//*[@data-mobile-search-toggle][@aria-expanded="false"]')->length)->toBe(1)
        ->and($xpath->query('//*[@id="mobile-header-search"][@hidden]')->length)->toBe(1)
        ->and($xpath->query('//*[@data-mobile-preferences]//*[@data-theme-toggle]')->length)->toBe(1)
        ->and($xpath->query('//*[@data-mobile-preferences]//form')->length)->toBe(1)
        ->and($xpath->query('//*[@data-desktop-header]//*[@data-header-logo]')->length)->toBe(1)
        ->and($xpath->query('//*[@data-desktop-header]//*[@data-desktop-search]')->length)->toBe(1)
        ->and($xpath->query('//*[@data-desktop-header]//*[@data-header-actions]')->length)->toBe(1)
        ->and($xpath->query('//shop-search')->length)->toBe(2);
});

test('native header search filters products and preserves the query for pagination', function () {
    $category = ProductCategory::factory()->create();
    $matching = Product::factory()->for($category, 'category')->create(['name' => 'Header Honey Jar']);
    Product::factory()->for($category, 'category')->create(['name' => 'Header Tea']);
    Product::factory()->for($category, 'category')->active(false)->create(['name' => 'Hidden Honey']);

    $response = $this->get('/shop/search?q=%20Honey%20&rowsPerPage=1')->assertOk()
        ->assertViewHas('searchText', 'Honey')
        ->assertViewHas('products', fn ($products) => $products->total() === 1 && $products->first()->id === $matching->id);

    $xpath = storefrontHeaderXPath($response->getContent());
    expect($xpath->query('//shop-search')->item(0)->getAttribute(':initial-query'))->toBe('"Honey"');

    Product::factory()->for($category, 'category')->create(['name' => 'Another Honey Jar']);
    $this->get('/shop/search?q=Honey&rowsPerPage=1')->assertOk()
        ->assertViewHas('products', fn ($products) => str_contains($products->nextPageUrl(), 'q=Honey'));
});

test('legacy search paths still work and array search input is rejected', function () {
    $category = ProductCategory::factory()->create();
    Product::factory()->for($category, 'category')->create(['name' => 'Legacy Honey']);

    $this->get('/shop/search/Legacy')->assertOk()->assertViewHas('searchText', 'Legacy')
        ->assertViewHas('products', fn ($products) => $products->total() === 1);
    $this->getJson('/shop/search?q[]=bad')->assertUnprocessable()->assertJsonValidationErrors('q');
    $this->get('/shop/search?q=%20%20')->assertOk()->assertViewHas('searchText', '');
});
