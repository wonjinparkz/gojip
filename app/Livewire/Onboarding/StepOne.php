<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class StepOne extends Component
{
    public $branches = [];

    protected $rules = [
        'branches.*.name' => 'required|string|max:255',
        'branches.*.start_floor' => 'required|integer',
        'branches.*.end_floor' => 'required|integer|gte:branches.*.start_floor',
    ];

    protected $messages = [
        'branches.*.name.required' => '지점명을 입력해주세요.',
        'branches.*.start_floor.required' => '시작층을 입력해주세요.',
        'branches.*.end_floor.required' => '끝층을 입력해주세요.',
        'branches.*.end_floor.gte' => '끝층은 시작층보다 크거나 같아야 합니다.',
    ];

    public function mount()
    {
        // Initialize with one branch
        $this->branches = [
            ['name' => '', 'start_floor' => 1, 'end_floor' => 3]
        ];
    }

    public function addBranch()
    {
        $this->branches[] = ['name' => '', 'start_floor' => 1, 'end_floor' => 3];
    }

    public function removeBranch($index)
    {
        unset($this->branches[$index]);
        $this->branches = array_values($this->branches);
    }

    public function nextStep()
    {
        $this->validate();
        $this->dispatch('saveBranches', branches: $this->branches);
    }

    public function render()
    {
        return view('livewire.onboarding.step-one');
    }
}
