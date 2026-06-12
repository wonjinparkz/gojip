<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class OnboardingWizard extends Component
{
    public $currentStep = 1;
    public $selectedType = null;
    public $branches = [];
    public $operationalSettings = [
        'room_types' => [],
        'window_structure' => null,
        'gender_division' => null,
    ];

    protected $listeners = ['nextStep', 'previousStep', 'saveSelectedType', 'saveBranches', 'saveOperationalSettings'];

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

    public function saveSelectedType($type)
    {
        $this->selectedType = $type;
        $this->nextStep();
    }

    public function saveOperationalSettings($settings)
    {
        $this->operationalSettings = $settings;
        $this->nextStep();
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
