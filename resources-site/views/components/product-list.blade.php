<section
    class="max-w-8xl mx-auto my-10 grid w-fit grid-cols-1 justify-center justify-items-center gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-4"
>
    @foreach ($products as $product)
        <product-card :product="{{ $product }}"></product-card>
    @endforeach
</section>
