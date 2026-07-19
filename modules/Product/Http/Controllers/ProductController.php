<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;
use Modules\Product\Http\Requests\ProductValidate;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductAttribute;
use Modules\Product\Models\ProductBundleItem;
use Modules\Product\Services\GetProductBrandOptions;
use Modules\Product\Services\GetProductCategoryOptions;
use Modules\Product\Services\GetProductTagOptions;
use Modules\Product\Services\SyncProductTags;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\EditorImage;
use Modules\Support\Traits\UploadFile;

class ProductController extends BackendController
{
    use EditorImage, UploadFile;

    protected string $uploadImagePath = 'storage/app/public/product';

    public function index(
        Request $request,
        GetProductBrandOptions $getProductBrandOptions,
        GetProductCategoryOptions $getProductCategoryOptions,
        GetProductTagOptions $getProductTagOptions
    ): Response {
        $activeFilter = $request->input('active');
        $featuredFilter = $request->input('featured');
        $stockFilter = $request->input('stock');
        $brandFilter = $request->input('brand');
        $categoryFilter = $request->input('category');
        $tagFilter = $request->input('tag');
        $attributeFilter = $request->input('attribute');
        $attributeValueFilter = $request->input('attribute_value');

        $products = Product::orderByDesc('id')
            ->with(['category:id,name', 'brand:id,name', 'variations'])
            ->search($request->input('searchContext'), $request->input('searchTerm'))
            ->when($activeFilter !== null && $activeFilter !== '', fn ($q) => $q->where('active', (bool) $activeFilter))
            ->when($featuredFilter !== null && $featuredFilter !== '', fn ($q) => $q->where('featured', (bool) $featuredFilter))
            ->when($stockFilter === 'low', fn ($q) => $q->where('quantity', '<', 10))
            ->when($stockFilter === 'out', fn ($q) => $q->where('quantity', '<=', 0))
            ->when($brandFilter, fn ($q) => $q->where('brand_id', $brandFilter))
            ->when($categoryFilter, fn ($q) => $q->where('category_id', $categoryFilter))
            ->when($tagFilter, fn ($q) => $q->whereRelation('tags', 'product_tags.id', $tagFilter))
            ->when($attributeFilter, fn ($q) => $q->whereRelation('attributeValues', 'product_attribute_id', $attributeFilter))
            ->when($attributeValueFilter, fn ($q) => $q->whereRelation('attributeValues', 'product_attribute_values.id', $attributeValueFilter))
            ->paginate($request->input('rowsPerPage', 15))
            ->withQueryString()
            ->through(fn ($product) => $this->formatProductForIndex($product));

        return inertia('Product/ProductIndex', [
            'products' => $products,
            'brands' => $getProductBrandOptions->get(),
            'categories' => $getProductCategoryOptions->get(),
            'tags' => $getProductTagOptions->get(),
            'attributes' => ProductAttribute::with('values')->orderBy('name')->get(),
            'filters' => [
                'active' => $activeFilter,
                'featured' => $featuredFilter,
                'stock' => $stockFilter,
                'brand' => $brandFilter,
                'category' => $categoryFilter,
                'tag' => $tagFilter,
                'attribute' => $attributeFilter,
                'attribute_value' => $attributeValueFilter,
                'searchTerm' => $request->input('searchTerm'),
            ],
        ]);
    }

    private function formatProductForIndex(Product $product): array
    {
        $type = $product->type?->value ?? 'simple';

        $data = [
            'id' => $product->id,
            'image_url' => $product->image_url,
            'name' => $product->name,
            'unit' => $product->unit,
            'category' => $product->category?->name,
            'brand' => $product->brand?->name,
            'active' => $product->active,
            'featured' => $product->featured,
            'type' => $type,
        ];

        if ($type === 'variable') {
            $activeVariations = $product->variations->where('active', true);

            // Compute effective price per variation: sale_price if set, else price
            $prices = $activeVariations->map(fn ($v) => $v->sale_price > 0 ? $v->sale_price : $v->price)
                ->filter()
                ->values();

            $data['price_min'] = $prices->min();
            $data['price_max'] = $prices->max();
            $data['price'] = null;
            $data['sale_price'] = null;
            $data['stock_variations'] = $activeVariations->where('quantity', '>', 0)->count();
            $data['quantity'] = null;
        } else {
            $data['price'] = $product->price;
            $data['sale_price'] = $product->sale_price;
            $data['quantity'] = $product->quantity;
            $data['price_min'] = null;
            $data['price_max'] = null;
            $data['stock_variations'] = null;
        }

        return $data;
    }

    public function create(GetProductCategoryOptions $getProductCategoryOptions, GetProductTagOptions $getProductTagOptions, GetProductBrandOptions $getProductBrandOptions): Response
    {
        return inertia('Product/ProductForm', [
            'categories' => $getProductCategoryOptions->get(),
            'tags' => $getProductTagOptions->get(),
            'brands' => $getProductBrandOptions->get(),
            'productFiles' => [],
            'allAttributes' => ProductAttribute::with('values')->orderBy('name')->get(),
            'allProducts' => Product::where('type', '!=', 'bundle')->orderBy('name')->get(['id', 'name', 'price', 'sale_price', 'type', 'quantity']),
        ]);
    }

    public function store(ProductValidate $request, SyncProductTags $syncProductTags)
    {
        $productData = $request->validated();

        if ($request->hasFile('image')) {
            $productData = array_merge($productData, $this->uploadFile('image', 'product', 'originalUUID', 'public'));
        }

        $product = Product::create($productData);

        if (is_array($request->input('tags')) and count($request->input('tags'))) {
            $syncProductTags->sync($product, $request->input('tags'));
        }

        foreach ($request->file('gallery_images', []) as $file) {
            $product->addMedia($file)->toMediaCollection('gallery');
        }

        // Variable/bundle products need immediate editing for variations/items
        $typeValue = $product->type?->value ?? 'simple';
        if ($typeValue !== 'simple') {
            return redirect()->route('product.edit', $product->id)
                ->with('success', 'Product created. You can now manage variations or bundle items.');
        }

        return redirect()->route('product.index')
            ->with('success', 'Product created.');
    }

    public function edit(GetProductCategoryOptions $getProductCategoryOptions, GetProductTagOptions $getProductTagOptions, GetProductBrandOptions $getProductBrandOptions, int $id)
    {
        $product = Product::with(['tags' => function ($query) {
            $query->select('product_tags.id', 'product_tags.name');
        }, 'productFiles', 'attributeValues', 'variations'])->find($id);

        $gallery = $product->getMedia('gallery')->map(fn ($m) => [
            'id' => $m->id,
            'url' => $m->getUrl(),
            'name' => $m->file_name,
        ])->values();

        $productFiles = $product->productFiles->map(fn ($pf) => [
            'id' => $pf->id,
            'name' => $pf->name,
            'sort_order' => $pf->sort_order,
            'file_url' => $pf->media?->getUrl(),
            'file_name' => $pf->media?->file_name,
            'file_size' => $pf->media?->human_readable_size,
        ]);

        return inertia('Product/ProductForm', [
            'product' => $product,
            'gallery' => $gallery,
            'productFiles' => $productFiles,
            'categories' => $getProductCategoryOptions->get(),
            'tags' => $getProductTagOptions->get(),
            'brands' => $getProductBrandOptions->get(),
            'allAttributes' => ProductAttribute::with('values')->orderBy('name')->get(),
            'allProducts' => Product::where('id', '!=', $product->id)->where('type', '!=', 'bundle')->orderBy('name')->get(['id', 'name', 'price', 'sale_price', 'type', 'quantity']),
        ]);
    }

    public function update(ProductValidate $request, SyncProductTags $syncProductTags, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $productData = $request->validated();

        if ($request->hasFile('image')) {
            $productData = array_merge($productData, $this->uploadFile('image', 'product', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $productData['image'] = null;
        } else {
            unset($productData['image']);
        }

        $product->update($productData);

        if ($request->has('tagsHasChanged')) {
            $syncProductTags->sync($product, $request->input('tags'));
        }

        foreach ($request->file('gallery_images', []) as $file) {
            $product->addMedia($file)->toMediaCollection('gallery');
        }

        return redirect()->route('product.edit', $id)
            ->with('success', 'Product has been updated.');
    }

    public function show(int $id): Response
    {
        $product = Product::with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'tags:id,name,slug',
            'variations.attributeValues.attribute',
            'bundleItems.childProduct',
            'productFiles.media',
        ])->findOrFail($id);

        $gallery = $product->getMedia('gallery')->map(fn ($m) => [
            'id' => $m->id,
            'url' => $m->getUrl(),
            'name' => $m->file_name,
        ])->values();

        $productFiles = $product->productFiles->map(fn ($pf) => [
            'id' => $pf->id,
            'name' => $pf->name,
            'file_name' => $pf->media?->file_name,
            'file_size' => $pf->media?->human_readable_size,
            'file_url' => $pf->media?->getUrl(),
        ]);

        $variations = $product->variations->map(fn ($v) => [
            'id' => $v->id,
            'sku' => $v->sku,
            'price' => (float) $v->price,
            'sale_price' => (float) $v->sale_price,
            'quantity' => $v->quantity,
            'active' => $v->active,
            'variation_key' => $v->variation_key,
            'attributes' => $v->attributeValues->map(fn ($av) => [
                'attribute' => $av->attribute?->name,
                'value' => $av->value,
            ]),
            'image_url' => $v->getFirstMediaUrl('image'),
        ]);

        $bundleItems = $product->bundleItems->map(fn ($bi) => [
            'id' => $bi->id,
            'child_product_name' => $bi->childProduct?->name ?? "Product #{$bi->child_product_id}",
            'child_product_price' => (float) ($bi->childProduct?->sale_price ?? $bi->childProduct?->price ?? 0),
            'quantity' => $bi->quantity,
            'is_optional' => $bi->is_optional,
            'price_override' => (float) $bi->price_override,
        ]);

        return inertia('Product/ProductShow', [
            'product' => $product,
            'gallery' => $gallery,
            'productFiles' => $productFiles,
            'variations' => $variations,
            'bundleItems' => $bundleItems,
        ]);
    }

    public function destroyGalleryImage(int $id, int $mediaId): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $media = $product->getMedia('gallery')->firstWhere('id', $mediaId);

        if ($media) {
            $media->delete();
        }

        return redirect()->route('product.edit', $id)
            ->with('success', 'Gallery image removed.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('product.index')
            ->with('success', 'Product deleted.');
    }

    public function recycleBin(): Response
    {
        $products = Product::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'name' => Str::limit($product->name, 50),
            ]);

        return inertia('Product/ProductRecycleBin', [
            'products' => $products,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Product restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $product = Product::onlyTrashed()->findOrFail($id);

        $inBundle = ProductBundleItem::where('child_product_id', $id)->exists();
        if ($inBundle) {
            return redirect()->route('product.recycleBin.index')
                ->with('error', 'Cannot delete. This product is used in active bundles.');
        }

        $product->forceDelete();

        return redirect()->route('product.recycleBin.index')->with('success', 'Product deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $products = Product::onlyTrashed()->get();

        foreach ($products as $product) {
            $product->forceDelete();
        }

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Product::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('product.recycleBin.index')
            ->with('success', 'Product restored.');
    }
}
