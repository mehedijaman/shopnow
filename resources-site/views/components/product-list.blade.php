<section
    class="max-w-8xl mx-auto my-10 grid w-fit grid-cols-1 justify-center justify-items-center gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-4"
>
    @foreach ($products as $product)
        <x-product-card :product="$product"></x-product-card>
    @endforeach
</section>
