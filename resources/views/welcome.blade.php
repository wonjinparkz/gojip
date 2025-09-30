<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-white to-gray-50">
        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="text-center">
                <h1 class="text-5xl sm:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    고시원 운영은<br>고집하세요.
                </h1>
                <div class="max-w-3xl mx-auto space-y-4 text-lg sm:text-xl text-gray-600 mb-12">
                    <p>매번 번거로운 입실자 관리, 방 배정, 호실 현황 체크...</p>
                    <p>이제 고시원을 위한 스마트 집사 <span class="font-bold text-gray-900">'고집'</span>으로 손쉽게 끝내보세요.</p>
                </div>

                <div class="max-w-3xl mx-auto mb-12">
                    <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg">
                        <p class="text-gray-700 leading-relaxed">
                            고집은 나만의 믿음직한 집사처럼, 고시원 운영의 복잡한 업무를<br class="hidden sm:block">
                            내 상황에 맞게 섬세하게 챙겨주는 든든한 운영 파트너입니다.
                        </p>
                    </div>
                </div>

                <p class="text-xl text-gray-700 mb-10">
                    고집과 함께 고시원 운영의 부담은 줄이고, 소중한 시간을 아끼세요.
                </p>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 text-white font-bold text-lg px-10 py-4 rounded-lg hover:bg-blue-700 transition duration-150 shadow-lg hover:shadow-xl">
                        대시보드로 이동
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-blue-600 text-white font-bold text-lg px-10 py-4 rounded-lg hover:bg-blue-700 transition duration-150 shadow-lg hover:shadow-xl">
                        한 달 무료로 체험하기
                    </a>
                @endauth
            </div>
        </div>

        <!-- Features Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">입실자 관리</h3>
                    <p class="text-gray-600">입실부터 퇴실까지, 입실자 정보를 체계적으로 관리하고 필요한 정보를 빠르게 확인하세요.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">호실 현황 관리</h3>
                    <p class="text-gray-600">실시간으로 빈방 현황을 파악하고, 효율적인 방 배정으로 공실을 최소화하세요.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition">
                    <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">스마트 운영</h3>
                    <p class="text-gray-600">자동화된 알림과 리포트로 운영 현황을 한눈에 파악하고 더 효율적으로 운영하세요.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>