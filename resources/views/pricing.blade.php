<x-app-layout>
    <div class="min-h-screen bg-white">
        <!-- Hero Section -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-16">
            <div class="text-center">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 mb-8">
                    <span class="inline-block bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full">BETA</span>
                    <span class="text-sm text-gray-600">특별 할인 진행 중</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl sm:text-7xl font-bold text-gray-900 mb-8 leading-tight tracking-tight">
                    간단하고 명확한<br>
                    <span class="text-teal-400">요금제</span>
                </h1>

                <!-- Subtitle -->
                <p class="max-w-2xl mx-auto text-xl text-gray-600 mb-12 leading-relaxed">
                    고시원 규모에 맞는 최적의 요금제를 선택하세요<br class="hidden sm:block">
                    모든 요금제에서 핵심 기능을 제공합니다
                </p>
            </div>
        </div>

        <!-- Pricing Cards Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="bg-white p-8 rounded-2xl border-2 border-gray-200 hover:border-teal-200 transition duration-300">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                        <p class="text-gray-600">소규모 고시원에 적합</p>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-bold text-gray-900">₩5,900</span>
                            <span class="text-gray-600 ml-2">/월</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">VAT 포함</p>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">최대 30개 호실 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">1개 지점 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">입실자 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">방 배정 및 현황 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">기본 대시보드</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">이메일 지원</span>
                        </li>
                    </ul>

                    @auth
                        <a href="/admin" class="block w-full text-center bg-gray-100 text-gray-900 font-semibold px-6 py-3 rounded-full hover:bg-gray-200 transition duration-150">
                            시작하기
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center bg-gray-100 text-gray-900 font-semibold px-6 py-3 rounded-full hover:bg-gray-200 transition duration-150">
                            시작하기
                        </a>
                    @endauth
                </div>

                <!-- Pro Plan (Popular) -->
                <div class="bg-white p-8 rounded-2xl border-2 border-teal-400 hover:border-teal-500 transition duration-300 relative transform md:scale-105 shadow-xl">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-teal-400 text-white text-xs font-semibold px-4 py-1 rounded-full">인기</span>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro</h3>
                        <p class="text-gray-600">중대규모 고시원에 최적</p>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-bold text-gray-900">₩12,900</span>
                            <span class="text-gray-600 ml-2">/월</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">VAT 포함</p>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">최대 100개 호실 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">무제한 지점 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">입실자 고급 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">방 배정 및 현황 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">고급 대시보드 및 통계</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">데이터 내보내기 (엑셀)</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">우선 지원</span>
                        </li>
                    </ul>

                    @auth
                        <a href="/admin" class="block w-full text-center bg-teal-400 text-white font-semibold px-6 py-3 rounded-full hover:bg-teal-500 transition duration-150">
                            시작하기
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center bg-teal-400 text-white font-semibold px-6 py-3 rounded-full hover:bg-teal-500 transition duration-150">
                            시작하기
                        </a>
                    @endauth
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white p-8 rounded-2xl border-2 border-gray-200 hover:border-teal-200 transition duration-300">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <p class="text-gray-600">대규모 고시원 체인</p>
                    </div>

                    <div class="mb-8">
                        <div class="flex items-baseline">
                            <span class="text-5xl font-bold text-gray-900">맞춤형</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">문의를 통해 견적 제공</p>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">무제한 호실 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">무제한 지점 관리</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">모든 Pro 기능 포함</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">맞춤형 기능 개발</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">전담 매니저 배정</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">24/7 전화 지원</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-teal-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">온사이트 교육</span>
                        </li>
                    </ul>

                    <a href="mailto:support@gojip.kr" class="block w-full text-center bg-gray-900 text-white font-semibold px-6 py-3 rounded-full hover:bg-gray-800 transition duration-150">
                        문의하기
                    </a>
                </div>
            </div>
        </div>

        <!-- Feature Comparison Matrix -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="text-center mb-12">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                    요금제별 기능 비교
                </h2>
                <p class="text-lg text-gray-600">
                    각 요금제에서 제공하는 기능을 자세히 비교해보세요
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">기능</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Starter</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-teal-600 bg-teal-50">Pro</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Enterprise</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Pricing -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">월 요금</td>
                                <td class="px-6 py-4 text-sm text-gray-700 text-center">₩5,900</td>
                                <td class="px-6 py-4 text-sm text-gray-700 text-center bg-teal-50">₩12,900</td>
                                <td class="px-6 py-4 text-sm text-gray-700 text-center">맞춤 견적</td>
                            </tr>

                            <!-- Category: 기본 관리 -->
                            <tr class="bg-gray-100">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900">기본 관리</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">호실 관리</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">최대 30개</div>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <div class="text-sm text-gray-700">최대 100개</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">무제한</div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">지점 관리</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">1개</div>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <div class="text-sm text-gray-700">무제한</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">무제한</div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">입실자 정보 관리</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">입/퇴실 일정 관리</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>

                            <!-- Category: 대시보드 & 통계 -->
                            <tr class="bg-gray-100">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900">대시보드 & 통계</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">기본 대시보드</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">고급 통계 및 분석</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">메모 위젯</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>

                            <!-- Category: 데이터 관리 -->
                            <tr class="bg-gray-100">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900">데이터 관리</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">데이터 내보내기 (엑셀)</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">데이터 가져오기 (엑셀)</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">자동 백업</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <div class="text-sm text-gray-700">주 1회</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">일 1회</div>
                                </td>
                            </tr>

                            <!-- Category: 지원 -->
                            <tr class="bg-gray-100">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900">고객 지원</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">이메일 지원</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">48시간 이내</div>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <div class="text-sm text-gray-700">24시간 이내</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">12시간 이내</div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">전화 지원</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <div class="text-sm text-gray-700">평일 9-6시</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm text-gray-700">24/7</div>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">전담 매니저</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">온사이트 교육</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>

                            <!-- Category: 맞춤 기능 -->
                            <tr class="bg-gray-100">
                                <td colspan="4" class="px-6 py-3 text-sm font-bold text-gray-900">맞춤 기능</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">맞춤형 기능 개발</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">API 접근</td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center bg-teal-50">
                                    <svg class="w-5 h-5 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <svg class="w-5 h-5 text-teal-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Features Section -->
        <div class="bg-gray-50 py-24">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        주요 기능 상세 설명
                    </h2>
                    <p class="text-lg text-gray-600">
                        고집이 제공하는 핵심 기능을 자세히 알아보세요
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Feature Detail 1 -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">호실 현황 관리</h3>
                        <p class="text-gray-600 mb-4">
                            층별, 호실별 입실자 현황을 한눈에 파악하고, 빈방을 효율적으로 관리하세요. 방 상태를 실시간으로 업데이트하고, 입실 가능한 호실을 빠르게 확인할 수 있습니다.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>실시간 호실 상태 모니터링</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>층별/타입별 필터링</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>공실률 자동 계산</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Feature Detail 2 -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">입실자 관리</h3>
                        <p class="text-gray-600 mb-4">
                            입실자의 개인정보, 계약 정보, 입퇴실 일정을 체계적으로 관리하세요. 결제 상태를 추적하고, 입실자 정보를 빠르게 검색할 수 있습니다.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>입실자 상세 정보 관리</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>입퇴실 일정 캘린더</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>결제 상태 추적</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Feature Detail 3 -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">스마트 대시보드</h3>
                        <p class="text-gray-600 mb-4">
                            고시원 운영 현황을 한눈에 파악할 수 있는 대시보드를 제공합니다. 입실/퇴실 현황, 공실률, 수익 통계 등 핵심 지표를 실시간으로 확인하세요.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>실시간 운영 현황</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>주요 지표 위젯</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>Pro: 고급 통계 및 그래프</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Feature Detail 4 -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-100">
                        <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">데이터 내보내기/가져오기</h3>
                        <p class="text-gray-600 mb-4">
                            Pro 요금제부터 엑셀 파일로 데이터를 내보내거나 가져올 수 있습니다. 기존 데이터를 쉽게 이전하고, 보고서를 작성할 수 있습니다.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>엑셀 파일 내보내기</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>엑셀 파일 가져오기</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-400 mr-2">•</span>
                                <span>템플릿 제공</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="py-24 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                        자주 묻는 질문
                    </h2>
                    <p class="text-lg text-gray-600">
                        요금제에 대해 궁금한 점이 있으신가요?
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">무료 체험 기간이 있나요?</h3>
                        <p class="text-gray-600">네, 모든 요금제에 대해 14일 무료 체험을 제공합니다. 신용카드 등록 없이 바로 시작하실 수 있습니다.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">요금제는 언제든지 변경할 수 있나요?</h3>
                        <p class="text-gray-600">네, 언제든지 요금제를 업그레이드하거나 다운그레이드할 수 있습니다. 변경 사항은 다음 결제 주기부터 적용됩니다.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">결제 방법은 어떻게 되나요?</h3>
                        <p class="text-gray-600">신용카드, 체크카드, 계좌이체를 통한 결제가 가능합니다. 연간 결제 시 10% 할인 혜택을 드립니다.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">호실 수가 증가하면 어떻게 되나요?</h3>
                        <p class="text-gray-600">호실 수가 요금제 한도를 초과하면, 자동으로 상위 요금제로 업그레이드를 안내해 드립니다. 초과 사용에 대한 추가 요금은 없습니다.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">해지는 어떻게 하나요?</h3>
                        <p class="text-gray-600">언제든지 해지하실 수 있으며, 해지 위약금이나 추가 비용은 없습니다. 계정 설정에서 간단하게 해지 신청이 가능합니다.</p>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">VAT는 별도인가요?</h3>
                        <p class="text-gray-600">아니요, 표시된 가격은 VAT가 포함된 최종 금액입니다. 추가 비용은 발생하지 않습니다.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 bg-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    아직 고민 중이신가요?
                </h2>
                <p class="text-xl text-gray-600 mb-10">
                    14일 무료 체험으로 직접 경험해보세요
                </p>
                @auth
                    <a href="/admin" class="inline-block bg-teal-400 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-teal-500 transition duration-150 mr-4">
                        대시보드로 이동
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-teal-400 text-white font-semibold text-lg px-12 py-4 rounded-full hover:bg-teal-500 transition duration-150 mr-4">
                        무료로 시작하기
                    </a>
                @endauth
                <a href="mailto:support@gojip.kr" class="inline-block bg-white text-gray-900 font-semibold text-lg px-12 py-4 rounded-full border-2 border-gray-200 hover:border-gray-300 transition duration-150">
                    문의하기
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
