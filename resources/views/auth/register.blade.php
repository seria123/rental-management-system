<x-guest-layout>
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 relative">
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1600&q=80" alt="Property" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-navy-900/70"></div>
            <div class="relative z-10 flex flex-col justify-center px-12 text-white">
                <h1 class="text-4xl font-bold mb-4">Join Our Property Platform</h1>
                <p class="text-lg text-gray-200">Create your account and start managing properties today.</p>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-6 py-12">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
                </div>
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative mt-1">
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pr-10">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 px-3 text-gray-500">👁</button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative mt-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pr-10">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 px-3 text-gray-500">👁</button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                    <div class="flex items-center">
                        <input id="terms" name="terms" type="checkbox" required class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                        <label for="terms" class="ms-2 text-sm text-gray-600">I agree to the Terms and Conditions</label>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition">
                        Create Account
                    </button>
                    <p class="text-center text-sm text-gray-600">
                        Already registered? <a href="{{ route('login') }}" class="text-emerald-600 hover:underline">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <script>
        function togglePassword(id) {
            const el = document.getElementById(id);
            el.type = el.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-guest-layout>