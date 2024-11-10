@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto min-h-screen max-w-7xl px-6 py-12 lg:px-6">
        @if (isset($searchText))
            <div class="mb-4">
                <h2 class="text-2xl font-bold">
                    Search Results for "{{ $searchText }}"
                </h2>
            </div>
        @endif

        @if (isset($category))
            <div class="mb-4">
                <h2 class="text-2xl font-bold">
                    Category: {{ $category->name }}
                </h2>
            </div>
        @endif

        <div class="grid grid-cols-4 gap-4">
            <div>
                <div class="sticky top-0">
                    <div
                        class="box mt-4 w-full rounded-md border border-gray-300 bg-white p-4 md:max-w-sm"
                    >
                        <div
                            class="mb-4 flex w-full items-center justify-between border-b border-gray-200 pb-3"
                        >
                            <p
                                class="text-base font-medium leading-7 text-black"
                            >
                                Product Categories
                            </p>
                            <a
                                href="{{ route('shop.index') }}"
                                class="cursor-pointer text-xs font-medium text-gray-500 transition-all duration-500 hover:text-indigo-600"
                            >
                                RESET
                            </a>
                        </div>
                        {{--
                            <p
                            class="mb-3 text-sm font-medium leading-6 text-black"
                            >
                            Categories
                            </p>
                        --}}
                        <div class="box flex flex-col gap-2">
                            @foreach ($categories as $category)
                                <a
                                    href="{{ route('shop.category', [$category->id, $category->slug]) }}"
                                    class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                                >
                                    {{--
                                        <svg
                                        class="me-2 h-4 w-4 shrink-0 text-gray-900 dark:text-white"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        >
                                        <path
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v5m-3 0h6M4 11h16M5 15h14a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1Z"
                                        ></path>
                                        </svg>
                                    --}}
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ $category->name }}
                                    </span>
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
                            <x-product-card
                                :product="$product"
                            ></x-product-card>
                        @endforeach
                    </section>
                @endif

                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
