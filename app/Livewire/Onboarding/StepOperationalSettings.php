<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class StepOperationalSettings extends Component
{
    public $roomTypes = [];
    public $windowStructure = null;
    public $genderDivision = null;

    protected $rules = [
        'roomTypes' => 'required|array|min:1',
        'windowStructure' => 'required|string',
        'genderDivision' => 'required|string',
    ];

    protected $messages = [
        'roomTypes.required' => '최소 하나 이상의 호실 유형을 선택해주세요.',
        'roomTypes.min' => '최소 하나 이상의 호실 유형을 선택해주세요.',
        'windowStructure.required' => '창 구조를 선택해주세요.',
        'genderDivision.required' => '남녀 구분 여부를 선택해주세요.',
    ];

    public function mount($operationalSettings = [])
    {
        // Restore saved settings if available
        if (!empty($operationalSettings)) {
            $this->roomTypes = $operationalSettings['room_types'] ?? [];
            $this->windowStructure = $operationalSettings['window_structure'] ?? null;
            $this->genderDivision = $operationalSettings['gender_division'] ?? null;
        }
    }

    public function toggleRoomType($type)
    {
        if (in_array($type, $this->roomTypes)) {
            $this->roomTypes = array_diff($this->roomTypes, [$type]);
        } else {
            $this->roomTypes[] = $type;
        }
    }

    public function selectWindowStructure($structure)
    {
        $this->windowStructure = $structure;
    }

    public function selectGenderDivision($division)
    {
        $this->genderDivision = $division;
    }

    public function nextStep()
    {
        $this->validate();

        $this->dispatch('saveOperationalSettings', settings: [
            'room_types' => $this->roomTypes,
            'window_structure' => $this->windowStructure,
            'gender_division' => $this->genderDivision,
        ]);
    }

    public function previousStep()
    {
        $this->dispatch('previousStep');
    }

    public function render()
    {
        return view('livewire.onboarding.step-operational-settings');
    }
}
