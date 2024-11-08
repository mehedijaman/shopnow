<footer class="bg-[#0b0e37] px-6 py-12 font-sans tracking-wide">
    <div class="flex flex-col items-center gap-6">
        <ul class="flex flex-wrap justify-center gap-4 gap-x-4">
            <li>
                <a href="javascript:void(0)">
                    <i
                        class="ri-facebook-box-fill text-2xl text-blue-700 hover:text-gray-400"
                    ></i>
                </a>
            </li>

            <li>
                <a href="javascript:void(0)">
                    <i
                        class="ri-twitter-x-line bg-black text-2xl text-white hover:text-gray-400"
                    ></i>
                </a>
            </li>

            <li>
                <a href="javascript:void(0)">
                    <i
                        class="ri-instagram-fill text-2xl text-red-200 hover:text-gray-400"
                    ></i>
                </a>
            </li>
        </ul>

        <hr class="w-full border-gray-500" />

        <ul class="flex flex-wrap gap-4 gap-x-7">
            <li>
                <a
                    href="/blog"
                    class="text-base text-gray-200 transition-all hover:underline"
                >
                    Blog
                </a>
            </li>
            <li>
                <a
                    href="{{ route('site.termsOfService') }}"
                    class="text-base text-gray-200 transition-all hover:underline"
                >
                    Terms of Service
                </a>
            </li>
            <li>
                <a
                    href="{{ route('site.privacyPolicy') }}"
                    class="text-base text-gray-200 transition-all hover:underline"
                >
                    Privacy Policy
                </a>
            </li>
            <li>
                <a
                    href="{{ route('site.contact') }}"
                    class="text-base text-gray-200 transition-all hover:underline"
                >
                    Contact
                </a>
            </li>
        </ul>
    </div>
</footer>
