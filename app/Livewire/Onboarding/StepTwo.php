<?php

namespace App\Livewire\Onboarding;

use App\Models\Branch;
use App\Models\FloorRoom;
use App\Models\ExtraRoom;
use App\Models\Room;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StepTwo extends Component
{
    public $branches = [];
    public $floorRooms = [];
    public $extraRooms = [];
    public $expandedBranches = [];
    public $excludedRooms = [];
    public $hasExtraRooms = [];
    public $showExcludedRooms = [];

    protected $rules = [
        'floorRooms.*.*.*.room_type' => 'required|string',
        'floorRooms.*.*.*.monthly_rent' => 'required|integer|min:0',
        'floorRooms.*.*.*.room_count' => 'required|integer|min:1',
        'extraRooms.*.*.room_type' => 'nullable|string',
        'extraRooms.*.*.monthly_rent' => 'nullable|integer|min:0',
        'extraRooms.*.*.room_count' => 'nullable|integer|min:1',
    ];

    protected $messages = [
        'floorRooms.*.*.*.room_type.required' => '호실 타입을 입력해주세요.',
        'floorRooms.*.*.*.monthly_rent.required' => '월세를 입력해주세요.',
        'floorRooms.*.*.*.monthly_rent.min' => '월세는 0원 이상이어야 합니다.',
        'floorRooms.*.*.*.room_count.required' => '호실 수를 입력해주세요.',
        'floorRooms.*.*.*.room_count.min' => '호실 수는 1개 이상이어야 합니다.',
    ];

    public function mount($branches)
    {
        $this->branches = $branches;

        // Initialize floor rooms for each branch
        foreach ($this->branches as $branchIndex => $branch) {
            $this->expandedBranches[$branchIndex] = false;
            $this->hasExtraRooms[$branchIndex] = false;
            $this->excludedRooms[$branchIndex] = [];
            $this->extraRooms[$branchIndex] = [];
            $this->showExcludedRooms[$branchIndex] = [];

            for ($floor = $branch['start_floor']; $floor <= $branch['end_floor']; $floor++) {
                $this->floorRooms[$branchIndex][$floor] = [
                    ['room_type' => '', 'monthly_rent' => 0, 'room_count' => 1]
                ];
                $this->excludedRooms[$branchIndex][$floor] = [];
                $this->showExcludedRooms[$branchIndex][$floor] = false;
            }
        }
    }

    public function toggleBranch($branchIndex)
    {
        $this->expandedBranches[$branchIndex] = !$this->expandedBranches[$branchIndex];
    }

    public function addFloorRoom($branchIndex, $floor)
    {
        $this->floorRooms[$branchIndex][$floor][] = [
            'room_type' => '',
            'monthly_rent' => 0,
            'room_count' => 1
        ];
    }

    public function removeFloorRoom($branchIndex, $floor, $roomIndex)
    {
        unset($this->floorRooms[$branchIndex][$floor][$roomIndex]);
        $this->floorRooms[$branchIndex][$floor] = array_values($this->floorRooms[$branchIndex][$floor]);
    }

    public function updatedHasExtraRooms($value, $key)
    {
        // Parse the key to get branch index (key format: "0", "1", etc.)
        $branchIndex = $key;

        // If checkbox is checked and extraRooms is empty, initialize with one room
        if ($value && empty($this->extraRooms[$branchIndex])) {
            $this->extraRooms[$branchIndex][] = [
                'room_type' => '',
                'monthly_rent' => 0,
                'room_count' => 1
            ];
        }
    }

    public function addExtraRoom($branchIndex)
    {
        $this->extraRooms[$branchIndex][] = [
            'room_type' => '',
            'monthly_rent' => 0,
            'room_count' => 1
        ];
    }

    public function removeExtraRoom($branchIndex, $roomIndex)
    {
        unset($this->extraRooms[$branchIndex][$roomIndex]);
        $this->extraRooms[$branchIndex] = array_values($this->extraRooms[$branchIndex]);
    }

    public function previousStep()
    {
        $this->dispatch('previousStep');
    }

    public function completeOnboarding()
    {
        // Simple validation - at least check if branches exist
        if (empty($this->branches)) {
            session()->flash('error', '지점 정보가 없습니다.');
            return;
        }

        try {
            $user = Auth::user();

            DB::transaction(function () use ($user) {

            foreach ($this->branches as $branchIndex => $branchData) {
                $branch = Branch::create([
                    'user_id' => $user->id,
                    'name' => $branchData['name'],
                    'start_floor' => $branchData['start_floor'],
                    'end_floor' => $branchData['end_floor'],
                ]);

                // Save floor rooms
                if (isset($this->floorRooms[$branchIndex])) {
                    foreach ($this->floorRooms[$branchIndex] as $floor => $rooms) {
                        foreach ($rooms as $room) {
                            // Convert excluded rooms string to array
                            $excludedRoomsArray = [];
                            if (!empty($this->excludedRooms[$branchIndex][$floor])) {
                                $excludedRoomsString = $this->excludedRooms[$branchIndex][$floor];
                                if (is_string($excludedRoomsString)) {
                                    $excludedRoomsArray = array_map('trim', explode(',', $excludedRoomsString));
                                } else {
                                    $excludedRoomsArray = $excludedRoomsString;
                                }
                            }

                            $floorRoom = FloorRoom::create([
                                'branch_id' => $branch->id,
                                'floor_number' => $floor,
                                'room_type' => $room['room_type'],
                                'monthly_rent' => $room['monthly_rent'],
                                'room_count' => $room['room_count'],
                                'excluded_room_numbers' => $excludedRoomsArray,
                            ]);

                            // Create individual Room records based on room_count
                            $roomCount = (int) $room['room_count'];
                            $monthlyRent = (int) $room['monthly_rent'];

                            for ($i = 1; $i <= $roomCount; $i++) {
                                // Generate room number (e.g., 201, 202, 203...)
                                $roomNumber = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);

                                // Skip if room number is in excluded list
                                if (in_array($roomNumber, $excludedRoomsArray)) {
                                    continue;
                                }

                                Room::create([
                                    'branch_id' => $branch->id,
                                    'room_number' => $roomNumber,
                                    'floor' => $floor,
                                    'room_type' => $room['room_type'],
                                    'monthly_rent' => $monthlyRent,
                                    'deposit' => 0,
                                    'status' => 'available',
                                ]);
                            }
                        }
                    }
                }

                // Save extra rooms
                if ($this->hasExtraRooms[$branchIndex] && isset($this->extraRooms[$branchIndex])) {
                    foreach ($this->extraRooms[$branchIndex] as $extraRoomIndex => $room) {
                        $extraRoom = ExtraRoom::create([
                            'branch_id' => $branch->id,
                            'room_type' => $room['room_type'],
                            'monthly_rent' => $room['monthly_rent'],
                            'room_count' => $room['room_count'],
                        ]);

                        // Create individual Room records for extra rooms
                        $roomCount = (int) $room['room_count'];
                        $monthlyRent = (int) $room['monthly_rent'];

                        for ($i = 1; $i <= $roomCount; $i++) {
                            // Generate room number for extra rooms (e.g., 기타1, 기타2...)
                            $roomNumber = '기타' . $i;

                            Room::create([
                                'branch_id' => $branch->id,
                                'room_number' => $roomNumber,
                                'floor' => 0, // Extra rooms have floor 0
                                'room_type' => $room['room_type'],
                                'monthly_rent' => $monthlyRent,
                                'deposit' => 0,
                                'status' => 'available',
                            ]);
                        }
                    }
                }
            }

            // Mark onboarding as completed
            $user->update(['onboarding_completed' => true]);
            });

            return redirect('/admin');
        } catch (\Exception $e) {
            session()->flash('error', '저장 중 오류가 발생했습니다: ' . $e->getMessage());
            logger()->error('Onboarding error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.onboarding.step-two');
    }
}
