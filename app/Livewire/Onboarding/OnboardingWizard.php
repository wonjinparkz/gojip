<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class OnboardingWizard extends Component
{
    public $currentStep = 1;
    public $branches = [];

    protected $listeners = ['nextStep', 'previousStep', 'saveBranches'];

    public function mount()
    {
        // Initialize with one empty branch
        $this->branches = [];
    }

    public function nextStep()
    {
        $this->currentStep++;
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function saveBranches($branches)
    {
        $this->branches = $branches;
        $this->nextStep();
    }

    public function render()
    {
        return view('livewire.onboarding.onboarding-wizard')
            ->layout('layouts.guest');
    }
}
