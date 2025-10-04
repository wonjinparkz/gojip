<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo and Beta Badge -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 inline-block">고집</h1>
                <span class="ml-2 inline-block bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">BETA</span>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-4">
                        <input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="이메일"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6 relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="비밀 번호"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        로그인
                    </button>

                    <!-- Links -->
                    <div class="mt-4 text-center text-sm text-gray-600">
                        <a href="{{ route('password.request') }}" class="hover:text-gray-900">아이디 · 비밀 번호 찾기</a>
                        <span class="mx-2">|</span>
                        <a href="{{ route('register') }}" class="hover:text-gray-900">회원 가입</a>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">또는</span>
                        </div>
                    </div>

                    <!-- Kakao Login Button -->
                    <a
                        href="{{ route('social.redirect', 'kakao') }}"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-lg transition duration-150 ease-in-out flex items-center justify-center"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3C6.477 3 2 6.477 2 10.5c0 2.568 1.664 4.82 4.166 6.166l-1.093 4.015c-.063.234.188.428.398.307l4.629-2.667C10.684 18.47 11.327 18.5 12 18.5c5.523 0 10-3.477 10-7.5S17.523 3 12 3z"/>
                        </svg>
                        카카오톡으로 로그인
                    </a>

                    <!-- Naver Login Button -->
                    <a
                        href="{{ route('social.redirect', 'naver') }}"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out flex items-center justify-center mt-3"
                    >
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16.273 12.845L7.376 0H0v24h7.727V11.155L16.624 24H24V0h-7.727v12.845z"/>
                        </svg>
                        네이버로 로그인
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-guest-layout>
