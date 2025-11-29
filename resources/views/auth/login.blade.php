<x-guest-layout>
   <!-- Session Status -->
   <x-auth-session-status class="mb-4" :status="session('status')" />

   <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email Address -->
      <div>
         <x-input-label for="email" :value="__('Email')" class="text-white" />
         <x-text-input
            id="email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username"
         />
         <x-input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <!-- Password -->
      <div class="mt-4">
         <x-input-label for="password" :value="__('Password')" class="text-white" />

         <x-text-input
            id="password"
            class="block mt-1 w-full"
            type="password"
            name="password"
            required
            autocomplete="current-password"
         />

         <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>

      <!-- Remember Me -->
      <div class="block mt-4">
         <label for="remember_me" class="inline-flex items-center">
            <input
               id="remember_me"
               type="checkbox"
               class="rounded shadow-sm focus:ring-indigo-500 border-gray-300 bg-white text-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-indigo-400"
               name="remember"
            />
            <span class="ms-2 text-sm text-gray-700 dark:text-gray-300">
               {{ __('Remember me') }}
            </span>
         </label>
      </div>

      <div class="flex items-center justify-end mt-4 gap-2">
         @if (Route::has('password.request'))
            <a
               class="underline text-sm p -1 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 text-gray-600 hover:text-gray-800 focus:ring-offset-white dark:text-gray-400 dark:hover:text-gray-200 dark:focus:ring-offset-gray-950"
               href="{{ route('password.request') }}"
            >
               {{ __('Forgot your password?') }}
            </a>
         @endif

         <x-primary-button class="ms-3 bg-green-600 hover:bg-green-700">
            {{ __('Log in') }}
         </x-primary-button>
      </div>
   </form>
   @if (config('demo.mode'))
      <form method="POST" action="{{ route('demo.login') }}" class="flex justify-center mt-4">
         @csrf
         <x-demo-button class="text-xs bg-sky-900" type="submit">USE DEMO →</x-demo-button>
      </form>
   @endif
</x-guest-layout>
