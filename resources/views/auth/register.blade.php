<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Logo and Beta Badge -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 inline-block">고집</h1>
                <span class="ml-2 inline-block bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">BETA</span>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" x-data="registerForm()">
                    @csrf

                    <!-- Name Input -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="이름을 입력하세요"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                    </div>

                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autocomplete="username"
                            placeholder="이메일을 입력하세요"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                    </div>

                    <!-- Password Input -->
                    <div class="mb-4 relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">비밀번호</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="비밀번호를 입력하세요"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                    </div>

                    <!-- Password Confirmation Input -->
                    <div class="mb-4 relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">비밀번호 확인</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="비밀번호를 다시 입력하세요"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                        />
                    </div>

                    <!-- Phone Number Input with Verify Button -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">휴대폰번호</label>
                        <div class="flex gap-2">
                            <input
                                id="phone"
                                type="tel"
                                name="phone"
                                x-model="phone"
                                :disabled="phoneVerified"
                                placeholder="010-0000-0000"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent disabled:bg-gray-100"
                            />
                            <button
                                type="button"
                                @click="requestVerificationCode"
                                x-show="!showVerificationInput"
                                :disabled="phoneVerified"
                                class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition duration-150 ease-in-out disabled:bg-gray-100 disabled:text-gray-400"
                            >
                                인증번호
                            </button>
                        </div>
                    </div>

                    <!-- Verification Code Input (shown after clicking 인증번호) -->
                    <div x-show="showVerificationInput" x-transition class="mb-4">
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">인증번호</label>
                        <div class="flex gap-2">
                            <input
                                id="verification_code"
                                type="text"
                                x-model="verificationCode"
                                :disabled="phoneVerified"
                                placeholder="인증번호를 입력하세요"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent disabled:bg-gray-100"
                            />
                            <button
                                type="button"
                                @click="verifyCode"
                                :disabled="phoneVerified"
                                class="px-6 py-3 bg-teal-400 hover:bg-teal-500 text-white font-semibold rounded-lg transition duration-150 ease-in-out disabled:bg-gray-300"
                            >
                                확인
                            </button>
                        </div>
                        <p x-show="phoneVerified" class="mt-2 text-sm text-green-600">인증이 완료되었습니다.</p>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="terms" id="terms" required class="rounded border-gray-300 text-teal-400 shadow-sm focus:ring-teal-400" />
                                <span class="ml-2 text-sm text-gray-600">
                                    <a target="_blank" href="{{ route('terms.show') }}" class="underline hover:text-gray-900">이용약관</a>
                                    및
                                    <a target="_blank" href="{{ route('policy.show') }}" class="underline hover:text-gray-900">개인정보처리방침</a>에 동의합니다
                                </span>
                            </label>
                        </div>
                    @endif

                    <!-- Register Button -->
                    <button
                        type="submit"
                        :disabled="!phoneVerified"
                        class="w-full bg-teal-400 hover:bg-teal-500 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out disabled:bg-gray-300 disabled:cursor-not-allowed"
                    >
                        회원가입
                    </button>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">또는</span>
                        </div>
                    </div>

                    <!-- Social Login Buttons -->
                    <div class="space-y-3">
                        <!-- Kakao Login Button -->
                        <a
                            href="{{ route('social.redirect', 'kakao') }}"
                            class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold py-3 rounded-lg transition duration-150 ease-in-out flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3C6.477 3 2 6.477 2 10.5c0 2.568 1.664 4.82 4.166 6.166l-1.093 4.015c-.063.234.188.428.398.307l4.629-2.667C10.684 18.47 11.327 18.5 12 18.5c5.523 0 10-3.477 10-7.5S17.523 3 12 3z"/>
                            </svg>
                            카카오톡으로 가입
                        </a>

                        <!-- Naver Login Button -->
                        <a
                            href="{{ route('social.redirect', 'naver') }}"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition duration-150 ease-in-out flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16.273 12.845L7.376 0H0v24h7.727V11.155L16.624 24H24V0h-7.727v12.845z"/>
                            </svg>
                            네이버로 가입
                        </a>
                    </div>

                    <!-- Back to Login Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            로그인 페이지로 돌아가기
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function registerForm() {
            return {
                phone: '',
                verificationCode: '',
                showVerificationInput: false,
                phoneVerified: false,

                requestVerificationCode() {
                    if (this.phone.trim() === '') {
                        alert('휴대폰번호를 입력해주세요.');
                        return;
                    }

                    // TODO: API 호출하여 인증번호 발송
                    // 실제로는 서버에 요청을 보내야 합니다
                    console.log('인증번호 발송 요청:', this.phone);
                    alert('인증번호가 발송되었습니다.');
                    this.showVerificationInput = true;
                },

                verifyCode() {
                    if (this.verificationCode.trim() === '') {
                        alert('인증번호를 입력해주세요.');
                        return;
                    }

                    // TODO: API 호출하여 인증번호 확인
                    // 실제로는 서버에 요청을 보내야 합니다
                    console.log('인증번호 확인:', this.verificationCode);

                    // 임시로 인증 성공 처리 (실제로는 서버 응답에 따라 처리)
                    this.phoneVerified = true;
                    alert('인증이 완료되었습니다.');
                }
            }
        }
    </script>
</x-guest-layout>
