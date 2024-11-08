@extends('site-layout')

@section('bodyEndScripts')
    @vite('resources-site/js/index-app.js')
@endsection

@section('content')
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-6">
        <section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
            <div class="mx-auto max-w-2xl px-4 2xl:px-0">
                <h2
                    class="mb-2 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl"
                >
                    Thanks for your order!
                </h2>
                <p class="mb-6 text-gray-500 dark:text-gray-400 md:mb-8">
                    Your order
                    <a
                        href="#"
                        class="font-medium text-gray-900 hover:underline dark:text-white"
                    >
                        #7564804
                    </a>
                    will be processed within 24 hours during working days. We
                    will notify you by email once your order has been shipped.
                </p>
                <div
                    class="mb-6 space-y-4 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 sm:space-y-2 md:mb-8"
                >
                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt
                            class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0"
                        >
                            Date
                        </dt>
                        <dd
                            class="font-medium text-gray-900 dark:text-white sm:text-end"
                        >
                            14 May 2024
                        </dd>
                    </dl>
                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt
                            class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0"
                        >
                            Payment Method
                        </dt>
                        <dd
                            class="font-medium text-gray-900 dark:text-white sm:text-end"
                        >
                            JPMorgan monthly installments
                        </dd>
                    </dl>
                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt
                            class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0"
                        >
                            Name
                        </dt>
                        <dd
                            class="font-medium text-gray-900 dark:text-white sm:text-end"
                        >
                            Flowbite Studios LLC
                        </dd>
                    </dl>
                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt
                            class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0"
                        >
                            Address
                        </dt>
                        <dd
                            class="font-medium text-gray-900 dark:text-white sm:text-end"
                        >
                            34 Scott Street, San Francisco, California, USA
                        </dd>
                    </dl>
                    <dl class="items-center justify-between gap-4 sm:flex">
                        <dt
                            class="mb-1 font-normal text-gray-500 dark:text-gray-400 sm:mb-0"
                        >
                            Phone
                        </dt>
                        <dd
                            class="font-medium text-gray-900 dark:text-white sm:text-end"
                        >
                            +(123) 456 7890
                        </dd>
                    </dl>
                </div>
                <div class="flex items-center space-x-4">
                    <a
                        href="#"
                        class="bg-primary-700 hover:bg-primary-800 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 rounded-lg px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4"
                    >
                        Track your order
                    </a>
                    <a
                        href="#"
                        class="hover:text-primary-700 rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                    >
                        Return to shopping
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
