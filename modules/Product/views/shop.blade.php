@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <div class="sticky top-0">
                    <div
                        class="box mt-4 w-full rounded-md border border-gray-300 bg-white p-6 md:max-w-sm"
                    >
                        <div
                            class="mb-7 flex w-full items-center justify-between border-b border-gray-200 pb-3"
                        >
                            <p
                                class="text-base font-medium leading-7 text-black"
                            >
                                Filter Products
                            </p>
                            <a
                                href="{{ route('shop.index') }}"
                                class="cursor-pointer text-xs font-medium text-gray-500 transition-all duration-500 hover:text-indigo-600"
                            >
                                RESET
                            </a>
                        </div>
                        <p
                            class="mb-3 text-sm font-medium leading-6 text-black"
                        >
                            Categories
                        </p>
                        <div class="box flex flex-col gap-2">
                            @foreach ($categories as $category)
                                <a
                                    href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                                    class="cursor-pointer text-sm font-normal text-gray-600"
                                >
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-3">
                @if ($products->count())
                    <section
                        class="grid grid-cols-1 justify-center justify-items-center gap-x-4 gap-y-12 py-4 md:grid-cols-3"
                    >
                        @foreach ($products as $product)
                            <product-card
                                :product="{{ $product }}"
                            ></product-card>
                        @endforeach
                    </section>
                @endif

                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
