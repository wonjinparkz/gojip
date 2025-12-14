<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full">
        <!-- Alert Message -->
        @if(session('onboarding_required') && session('message'))
        <div class="mb-6 bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg shadow-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-amber-800">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Logo and Beta Badge -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 inline-block">고집</h1>
            <span class="ml-2 inline-block bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">BETA</span>
        </div>

        <!-- Progress Steps -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center">
                <!-- Step 1 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 1 ? 'bg-teal-400 text-white' : 'bg-gray-200 text-gray-500' }}">
                        @if($currentStep > 1)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                </div>

                <!-- Line -->
                <div class="w-24 h-1 {{ $currentStep >= 2 ? 'bg-teal-400' : 'bg-gray-200' }}"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= 2 ? 'bg-teal-400 text-white' : 'bg-gray-200 text-gray-500' }}">
                        2
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Content -->
        @if($currentStep === 1)
            <livewire:onboarding.step-one />
        @elseif($currentStep === 2)
            <livewire:onboarding.step-two :branches="$branches" />
        @endif
    </div>
</div>
