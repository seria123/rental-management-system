```blade
<x-guest-layout>
    <div class="min-h-screen flex bg-white">

        {{-- LEFT: BRANDING SECTION --}}
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">

            <img
                src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1600&q=85"
                alt="Luxury Property"
                class="absolute inset-0 w-full h-full object-cover"
            >

            <div class="absolute inset-0 bg-slate-950/75"></div>

            <div class="relative z-10 flex flex-col justify-between w-full p-16 text-white">

                {{-- BRAND --}}
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold text-xl">
                            N
                        </div>
                        <div>
                            <h2 class="text-xl font-bold tracking-wide">NDIRO ESTATES</h2>
                            <p class="text-xs text-gray-300 tracking-widest uppercase">
                                Property Management
                            </p>
                        </div>
                    </div>
                </div>

                {{-- MAIN MESSAGE --}}
                <div class="max-w-xl space-y-7">

                    <span class="inline-block text-amber-400 text-sm font-semibold uppercase tracking-[0.25em]">
                        Property Management Simplified
                    </span>

                    <h1 class="text-5xl xl:text-6xl font-bold leading-tight">
                        Better Spaces.
                        <span class="text-amber-400">Better Living.</span>
                    </h1>

                    <p class="text-lg leading-8 text-gray-200 max-w-lg">
                        Manage properties, tenants, leases, maintenance
                        and payments through one intelligent property
                        management platform.
                    </p>

                    <div class="flex items-center gap-8 pt-5">

                        <div>
                            <p class="text-2xl font-bold text-amber-400">01</p>
                            <p class="text-sm text-gray-300 mt-1">Smart Management</p>
                        </div>

                        <div class="h-10 w-px bg-white/30"></div>

                        <div>
                            <p class="text-2xl font-bold text-amber-400">02</p>
                            <p class="text-sm text-gray-300 mt-1">Secure Payments</p>
                        </div>

                        <div class="h-10 w-px bg-white/30"></div>

                        <div>
                            <p class="text-2xl font-bold text-amber-400">03</p>
                            <p class="text-sm text-gray-300 mt-1">Easy Communication</p>
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Ndiro Estates. All rights reserved.
                </p>

            </div>
        </div>


        {{-- RIGHT: LOGIN SECTION --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-slate-50 px-6 sm:px-12 lg:px-16 py-16">

            <div class="w-full max-w-lg">

                {{-- MOBILE BRAND --}}
                <div class="lg:hidden flex items-center justify-center gap-3 mb-14">
                    <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-bold text-xl">
                        N
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">NDIRO ESTATES</h2>
                        <p class="text-xs text-slate-500 uppercase tracking-widest">
                            Property Management
                        </p>
                    </div>
                </div>


                {{-- HEADING --}}
                <div class="mb-12">

                    <p class="text-sm font-semibold text-amber-600 uppercase tracking-widest mb-4">
                        Welcome Back
                    </p>

                    <h1 class="text-4xl font-bold text-slate-900 tracking-tight">
                        Sign in to your account
                    </h1>

                    <p class="mt-4 text-base text-slate-500 leading-relaxed">
                        Enter your credentials below to access your
                        property management dashboard.
                    </p>

                </div>


                <x-auth-session-status
                    class="mb-6"
                    :status="session('status')"
                />


                {{-- LOGIN FORM --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-8">

                    @csrf

                    {{-- EMAIL --}}
                    <div class="space-y-3">

                        <label for="email" class="block text-sm font-semibold text-slate-700">
                            Email Address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter your email address"
                            class="block w-full h-14 px-5 rounded-xl border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 shadow-sm transition duration-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                        >

                        <x-input-error :messages="$errors->get('email')" class="mt-2"/>

                    </div>


                    {{-- PASSWORD --}}
                    <div class="space-y-3">

                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password
                        </label>

                        <div class="relative">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="block w-full h-14 px-5 pr-14 rounded-xl border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 shadow-sm transition duration-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                            >

                            <button
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute inset-y-0 right-0 flex items-center px-5 text-slate-400 hover:text-amber-600 transition"
                                aria-label="Toggle password visibility"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z"/>

                                    <circle cx="12" cy="12" r="3"/>

                                </svg>
                            </button>

                        </div>

                        <x-input-error :messages="$errors->get('password')" class="mt-2"/>

                    </div>


                    {{-- REMEMBER / FORGOT PASSWORD --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-1">

                        <label class="inline-flex items-center gap-3 cursor-pointer">

                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                            >

                            <span class="text-sm text-slate-600">
                                Remember me
                            </span>

                        </label>

                        @if (Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-sm font-semibold text-amber-600 hover:text-amber-700 transition"
                            >
                                Forgot password?
                            </a>
                        @endif

                    </div>


                    {{-- SIGN IN BUTTON --}}
                    <button
                        type="submit"
                        class="w-full h-14 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl shadow-lg shadow-slate-900/10 transition duration-200 flex items-center justify-center gap-3"
                    >
                        Sign In

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"/>

                        </svg>

                    </button>


                    {{-- REGISTER --}}
                    <div class="pt-5 border-t border-slate-200">

                        <p class="text-center text-sm text-slate-500">

                            Don't have an account?

                            <a
                                href="{{ route('register') }}"
                                class="font-semibold text-amber-600 hover:text-amber-700 ml-1"
                            >
                                Create an account
                            </a>

                        </p>

                    </div>

                </form>


                {{-- SECURITY FOOTER --}}
                <div class="mt-12 flex items-center justify-center gap-2 text-xs text-slate-400">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.8">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-11V7a4 4 0 00-8 0v3h8z"/>

                    </svg>

                    Secure access to your property management account.

                </div>

            </div>

        </div>

    </div>


    {{-- PASSWORD VISIBILITY --}}
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById('eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>

</x-guest-layout>
```