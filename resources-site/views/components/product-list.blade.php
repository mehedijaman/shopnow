<section
    class="grid grid-cols-1 justify-center justify-items-center gap-x-8 gap-y-16 py-4 md:grid-cols-2 lg:grid-cols-4"
>
    @foreach ($products as $product)
        <product-card :product="{{ $product }}"></product-card>
    @endforeach
</section>
