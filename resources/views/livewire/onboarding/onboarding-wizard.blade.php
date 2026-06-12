<div class="h-screen md:min-h-screen bg-gray-50 flex flex-col md:items-center md:justify-center md:py-8">
    <div class="w-full md:max-w-4xl flex flex-col flex-grow md:flex-grow-0 md:max-h-[90vh]">
        <!-- Desktop-only Header (Logo & Progress) -->
        <div class="hidden md:block w-full mb-8">
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
            <div class="flex items-center justify-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900">고집</h1>
                <span class="ml-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">BETA</span>
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    @for($i = 1; $i <= 4; $i++)
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $currentStep >= $i ? 'bg-teal-400 text-white' : 'bg-gray-200 text-gray-500' }}">
                                @if($currentStep > $i)
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $i }}
                                @endif
                            </div>
                        </div>
                        @if($i < 4)
                            <div class="w-16 h-1 {{ $currentStep >= ($i + 1) ? 'bg-teal-400' : 'bg-gray-200' }}"></div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>

        <!-- Step Content Wrapper -->
        <div class="flex-grow w-full flex flex-col items-center justify-center md:overflow-y-auto md:min-h-0">
            @if($currentStep === 1)
                <livewire:onboarding.step-intro :selectedType="$selectedType" />
            @elseif($currentStep === 2)
                <livewire:onboarding.step-operational-settings :operationalSettings="$operationalSettings" />
            @elseif($currentStep === 3)
                <livewire:onboarding.step-one :branches="$branches" />
            @elseif($currentStep === 4)
                <livewire:onboarding.step-two :branches="$branches" :operationalSettings="$operationalSettings" />
            @endif
        </div>
    </div>
</div>
