<x-app-layout>
    <div class="min-h-screen bg-white">
        <!-- Hero Section -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-24">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 mb-8">
                    <span class="inline-block bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">BETA</span>
                    <span class="text-sm text-gray-600">고시원 운영의 새로운 기준</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl sm:text-7xl font-bold text-gray-900 mb-8 leading-tight tracking-tight">
                    고시원 운영은<br>
                    <span class="text-teal-400">고집</span>하세요
                </h1>

                <!-- Subtitle -->
                <p class="max-w-2xl mx-auto text-xl text-gray-600 mb-12 leading-relaxed">
                    번거로운 입실자 관리, 방 배정, 호실 현황 체크를<br class="hidden sm:block">
                    스마트하게 관리하는 고시원 전문 솔루션
                </p>

                <!-- CTA Button -->
                @auth
                    <a href="/admin" class="inline-block bg-teal-400 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-teal-500 transition duration-150">
                        대시보드로 이동
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-teal-400 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-teal-500 transition duration-150">
                        무료로 시작하기
                    </a>
                @endauth

                <p class="mt-4 text-sm text-gray-500">신용카드 등록 없이 바로 시작</p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="bg-gray-50 py-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        왜 고집인가요?
                    </h2>
                    <p class="text-lg text-gray-600">
                        고시원 운영에 필요한 모든 기능을 한 곳에서
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white p-10 rounded-2xl border border-gray-100 hover:border-teal-200 transition duration-300">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">입실자 관리</h3>
                        <p class="text-gray-600 leading-relaxed">
                            입실부터 퇴실까지, 입실자 정보를 체계적으로 관리하고 필요한 정보를 빠르게 확인하세요.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-10 rounded-2xl border border-gray-100 hover:border-teal-200 transition duration-300">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">호실 현황 관리</h3>
                        <p class="text-gray-600 leading-relaxed">
                            실시간으로 빈방 현황을 파악하고, 효율적인 방 배정으로 공실을 최소화하세요.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-10 rounded-2xl border border-gray-100 hover:border-teal-200 transition duration-300">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">스마트 대시보드</h3>
                        <p class="text-gray-600 leading-relaxed">
                            운영 현황을 한눈에 파악하고, 데이터 기반으로 더 나은 의사결정을 하세요.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    지금 바로 시작하세요
                </h2>
                <p class="text-xl text-gray-600 mb-10">
                    복잡한 고시원 운영, 고집과 함께 간단하게
                </p>
                @auth
                    <a href="/admin" class="inline-block bg-gray-900 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-gray-800 transition duration-150">
                        대시보드로 이동
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-gray-900 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-gray-800 transition duration-150">
                        무료 체험 시작하기
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>