<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - {{ config('app.name', 'PartnerInsight') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <section class="bg-gray-50 dark:bg-gray-900" style="background-image: url('/images/login-bg.png'); background-size: cover; background-position: center bottom; background-repeat: no-repeat;">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <div class="flex justify-center mb-6">
                        <a href="/" class="flex items-center text-2xl font-semibold text-gray-900 dark:text-white">
                            <img class="w-10 h-10 mr-2" src="/images/logo.png" alt="logo">
                            PartnerInsight    
                        </a>
                    </div>
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-center text-gray-900 md:text-2xl dark:text-white">
                        Forgot Password?
                    </h1>
                    <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>
                    
                    @if (session('status'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="space-y-4 md:space-y-6" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-500 sm:text-sm rounded-lg focus:ring-brand-600 focus:border-brand-600 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="admin@example.com" required="">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800 shadow-lg shadow-brand-500/50">Send Reset Link</button>
                        <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                            Remember your password? <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:underline dark:text-brand-500">Sign in</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
