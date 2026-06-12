<?php

namespace App\Livewire\Onboarding;

use Livewire\Component;

class StepOne extends Component
{
    public $branches = [];

    protected $rules = [
        'branches.*.name' => 'required|string|max:255',
        'branches.*.selected_floors' => 'required_without:branches.*.custom_floors|array',
        'branches.*.custom_floors' => 'nullable|string',
    ];

    protected $messages = [
        'branches.*.name.required' => '지점명을 입력해주세요.',
        'branches.*.selected_floors.required_without' => '고시원이 위치한 층을 선택하거나 입력해주세요.',
    ];

    public function mount($branches = [])
    {
        // Restore branches from parent if available, otherwise initialize with one branch
        if (!empty($branches)) {
            // Need to restore the UI state for branches that were already processed
            $this->branches = array_map(function($branch) {
                return [
                    'name' => $branch['name'] ?? '',
                    'selected_floors' => $branch['floors'] ?? [],
                    'custom_floors' => '',
                    'show_custom_input' => false
                ];
            }, $branches);
        } else {
            $this->addBranch();
        }
    }

    public function addBranch()
    {
        $this->branches[] = [
            'name' => '', 
            'selected_floors' => [], 
            'custom_floors' => '',
            'show_custom_input' => false
        ];
    }

    public function removeBranch($index)
    {
        unset($this->branches[$index]);
        $this->branches = array_values($this->branches);
    }

    public function toggleFloor($branchIndex, $floor)
    {
        $selectedFloors = $this->branches[$branchIndex]['selected_floors'];
        if (in_array($floor, $selectedFloors)) {
            $this->branches[$branchIndex]['selected_floors'] = array_diff($selectedFloors, [$floor]);
        } else {
            $this->branches[$branchIndex]['selected_floors'][] = $floor;
        }
    }

    public function previousStep()
    {
        $this->dispatch('previousStep');
    }

    public function nextStep()
    {
        $this->validate();

        $processedBranches = array_map(function($branch) {
            // Parse custom floors
            $customFloors = [];
            if ($branch['show_custom_input'] && !empty($branch['custom_floors'])) {
                // Remove whitespace and split by comma
                $cleanInput = preg_replace('/\s+/', '', $branch['custom_floors']);
                $customFloors = array_filter(explode(',', $cleanInput), function($val) {
                    return is_numeric($val);
                });
                $customFloors = array_map('intval', $customFloors);
            }

            // Merge and unique
            $allFloors = array_unique(array_merge($branch['selected_floors'], $customFloors));
            sort($allFloors);

            if (empty($allFloors)) {
                $allFloors = [1]; // Fallback
            }

            return [
                'name' => $branch['name'],
                'floors' => $allFloors,
                'start_floor' => min($allFloors),
                'end_floor' => max($allFloors),
            ];
        }, $this->branches);

        $this->dispatch('saveBranches', branches: $processedBranches);
    }

    public function render()
    {
        return view('livewire.onboarding.step-one');
    }
}
