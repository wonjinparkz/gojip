<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class StepIntro extends Component
{
    public $selectedType = null;

    public function mount($selectedType = null)
    {
        $this->selectedType = $selectedType;
    }

    public function selectType($type)
    {
        $this->selectedType = $type;
    }

    public function nextStep()
    {
        if ($this->selectedType) {
            $this->dispatch('saveSelectedType', type: $this->selectedType);
        }
    }

    public function render()
    {
        return view('livewire.onboarding.step-intro');
    }
}
