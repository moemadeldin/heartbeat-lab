<div class="w-full max-w-md space-y-8">
    <!-- Logo/Brand -->
    <div class="text-center">
        <div
            class="inline-flex items-center justify-center w-16 h-16 bg-linear-to-br from-blue-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
            <span class="text-3xl">💓</span>
        </div>
        <h2 class="text-4xl font-extrabold tracking-tight text-white">Welcome Back</h2>
        <p class="mt-2 text-slate-400">Sign in to access your dashboard</p>
    </div>

    <!-- Login Form -->
    <div class="bg-slate-800/50 backdrop-blur-sm p-8 rounded-2xl border border-slate-700 shadow-2xl">
        <form wire:submit="login" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-2">Email Address</label>
                    <input wire:model="email" type="email"
                        class="block w-full rounded-lg bg-slate-900/50 border border-slate-600 text-white px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-500"
                        placeholder="you@example.com">
                    @error('email') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-2">Password</label>
                    <input wire:model="password" type="password"
                        class="block w-full rounded-lg bg-slate-900/50 border border-slate-600 text-white px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-500"
                        placeholder="••••••••">
                    @error('password') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center items-center py-3 px-4 rounded-lg text-sm font-semibold text-white bg-linear-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-slate-900 shadow-lg hover:shadow-xl transition-all">
                Sign In
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-slate-400 text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" wire:navigate
                    class="text-blue-400 hover:text-blue-300 font-semibold">Sign up</a>
            </p>
        </div>
    </div>
</div>