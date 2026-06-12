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
    public $operationalSettings = [];
    public $floorRooms = [];
    public $extraRooms = [];
    public $selectedFloors = [];
    public $excludedRooms = [];
    public $hasExtraRooms = [];
    public $showExcludedRooms = [];

    // Room number editing state
    public $editingRoomKey = null; // Format: "branchIndex-floor-roomIndex"
    public $roomNumbersList = []; // Stores actual room numbers for each room type
    public $editingRoomNumberIndex = null; // Index of room number being edited
    public $editingRoomNumberValue = ''; // Value being edited

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

    public function mount($branches, $operationalSettings = [])
    {
        $this->branches = $branches;
        $this->operationalSettings = $operationalSettings;

        $defaultRoomTypes = $this->operationalSettings['room_types'] ?? [];
        
        // If no types selected, use empty string as default
        if (empty($defaultRoomTypes)) {
            $defaultRoomTypes = [''];
        }

        // Initialize floor rooms for each branch
        foreach ($this->branches as $branchIndex => $branch) {
            $floors = $branch['floors'] ?? range($branch['start_floor'], $branch['end_floor']);
            $this->selectedFloors[$branchIndex] = $floors[0] ?? 1;
            $this->hasExtraRooms[$branchIndex] = false;
            $this->excludedRooms[$branchIndex] = [];
            $this->extraRooms[$branchIndex] = [];
            $this->showExcludedRooms[$branchIndex] = [];

            foreach ($floors as $floor) {
                $this->floorRooms[$branchIndex][$floor] = [
                    [
                        'room_category' => '',
                        'window_structure' => '',
                        'gender' => '',
                        'room_type' => '',
                        'monthly_rent' => 0,
                        'room_count' => 1
                    ]
                ];

                $this->excludedRooms[$branchIndex][$floor] = [];
                $this->showExcludedRooms[$branchIndex][$floor] = false;
            }
        }
    }

    public function selectFloor($branchIndex, $floor)
    {
        $this->selectedFloors[$branchIndex] = $floor;
    }

    public function addFloorRoom($branchIndex, $floor)
    {
        $this->floorRooms[$branchIndex][$floor][] = [
            'room_category' => '',
            'window_structure' => '',
            'gender' => '',
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

    /**
     * When room_count is changed, reset the custom room numbers list for that room.
     * This prevents the mismatch where old roomNumbersList count differs from new room_count.
     */
    public function updatedFloorRooms($value, $key)
    {
        // Key format: "branchIndex.floor.roomIndex.field"
        // We need to reset roomNumbersList when room_count is changed
        $parts = explode('.', $key);
        if (count($parts) === 4 && $parts[3] === 'room_count') {
            $branchIndex = $parts[0];
            $floor = $parts[1];
            $roomIndex = $parts[2];
            $roomKey = "{$branchIndex}-{$floor}-{$roomIndex}";
            
            // Clear the custom room numbers when room_count changes
            if (isset($this->roomNumbersList[$roomKey])) {
                unset($this->roomNumbersList[$roomKey]);
            }
            
            // Also close edit mode if it was open for this room
            if ($this->editingRoomKey === $roomKey) {
                $this->editingRoomKey = null;
                $this->editingRoomNumberIndex = null;
                $this->editingRoomNumberValue = '';
            }
        }
    }

    public function updatedHasExtraRooms($value, $key)
    {
        // Parse the key to get branch index (key format: "0", "1", etc.)
        $branchIndex = $key;

        // If checkbox is checked and extraRooms is empty, initialize with one room
        if ($value && empty($this->extraRooms[$branchIndex])) {
            $this->extraRooms[$branchIndex][] = [
                'room_category' => '',
                'window_structure' => '',
                'gender' => '',
                'room_type' => '',
                'monthly_rent' => 0,
                'room_count' => 1
            ];
        }
    }

    public function addExtraRoom($branchIndex)
    {
        $this->extraRooms[$branchIndex][] = [
            'room_category' => '',
            'window_structure' => '',
            'gender' => '',
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

    // Room number editing methods
    public function toggleEditRoomNumbers($branchIndex, $floor, $roomIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";

        if ($this->editingRoomKey === $key) {
            // Close edit mode
            $this->editingRoomKey = null;
            $this->editingRoomNumberIndex = null;
            $this->editingRoomNumberValue = '';
        } else {
            // Open edit mode and generate room numbers if not exists
            $this->editingRoomKey = $key;
            $this->editingRoomNumberIndex = null;
            $this->editingRoomNumberValue = '';

            if (!isset($this->roomNumbersList[$key])) {
                $this->generateRoomNumbers($branchIndex, $floor, $roomIndex);
            }
        }
    }

    public function generateRoomNumbers($branchIndex, $floor, $roomIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";
        $room = $this->floorRooms[$branchIndex][$floor][$roomIndex] ?? null;

        if (!$room) return;

        // Calculate starting offset based on previous rooms in the same floor
        $offset = 1;
        foreach ($this->floorRooms[$branchIndex][$floor] as $idx => $r) {
            if ($idx < $roomIndex) {
                // Check if this room has custom numbers
                $rKey = "{$branchIndex}-{$floor}-{$idx}";
                if (isset($this->roomNumbersList[$rKey])) {
                    $offset += count($this->roomNumbersList[$rKey]);
                } else {
                    $offset += (int) ($r['room_count'] ?? 1);
                }
            }
        }

        $roomNumbers = [];
        $roomCount = (int) ($room['room_count'] ?? 1);
        for ($i = 0; $i < $roomCount; $i++) {
            $roomNumbers[] = $floor . str_pad($offset + $i, 2, '0', STR_PAD_LEFT);
        }

        $this->roomNumbersList[$key] = $roomNumbers;
    }

    public function deleteRoomNumber($branchIndex, $floor, $roomIndex, $numberIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";

        if (isset($this->roomNumbersList[$key][$numberIndex])) {
            unset($this->roomNumbersList[$key][$numberIndex]);
            $this->roomNumbersList[$key] = array_values($this->roomNumbersList[$key]);

            // Update room count
            $this->floorRooms[$branchIndex][$floor][$roomIndex]['room_count'] = count($this->roomNumbersList[$key]);
        }
    }

    public function startEditRoomNumber($numberIndex, $currentValue)
    {
        $this->editingRoomNumberIndex = $numberIndex;
        $this->editingRoomNumberValue = $currentValue;
    }

    public function saveRoomNumber($branchIndex, $floor, $roomIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";

        if ($this->editingRoomNumberIndex !== null && isset($this->roomNumbersList[$key])) {
            $this->roomNumbersList[$key][$this->editingRoomNumberIndex] = $this->editingRoomNumberValue;
        }

        $this->editingRoomNumberIndex = null;
        $this->editingRoomNumberValue = '';
    }

    public function cancelEditRoomNumber()
    {
        $this->editingRoomNumberIndex = null;
        $this->editingRoomNumberValue = '';
    }

    public function addRoomNumber($branchIndex, $floor, $roomIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";

        if (!isset($this->roomNumbersList[$key])) {
            $this->generateRoomNumbers($branchIndex, $floor, $roomIndex);
        }

        // Find next available room number
        $existingNumbers = $this->roomNumbersList[$key] ?? [];
        $maxNumber = 0;
        foreach ($existingNumbers as $num) {
            $numPart = (int) substr($num, strlen((string)$floor));
            if ($numPart > $maxNumber) {
                $maxNumber = $numPart;
            }
        }

        $newNumber = $floor . str_pad($maxNumber + 1, 2, '0', STR_PAD_LEFT);
        $this->roomNumbersList[$key][] = $newNumber;

        // Update room count
        $this->floorRooms[$branchIndex][$floor][$roomIndex]['room_count'] = count($this->roomNumbersList[$key]);
    }

    public function getRoomNumbersForDisplay($branchIndex, $floor, $roomIndex)
    {
        $key = "{$branchIndex}-{$floor}-{$roomIndex}";

        if (isset($this->roomNumbersList[$key])) {
            return $this->roomNumbersList[$key];
        }

        // Generate default room numbers
        $room = $this->floorRooms[$branchIndex][$floor][$roomIndex] ?? null;
        if (!$room) return [];

        $offset = 1;
        foreach ($this->floorRooms[$branchIndex][$floor] as $idx => $r) {
            if ($idx < $roomIndex) {
                $rKey = "{$branchIndex}-{$floor}-{$idx}";
                if (isset($this->roomNumbersList[$rKey])) {
                    $offset += count($this->roomNumbersList[$rKey]);
                } else {
                    $offset += (int) ($r['room_count'] ?? 1);
                }
            }
        }

        $roomNumbers = [];
        $roomCount = (int) ($room['room_count'] ?? 1);
        for ($i = 0; $i < $roomCount; $i++) {
            $roomNumbers[] = $floor . str_pad($offset + $i, 2, '0', STR_PAD_LEFT);
        }

        return $roomNumbers;
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
                    // Create or update branch by name
                    $branch = Branch::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'name' => $branchData['name'],
                        ],
                        [
                            'start_floor' => $branchData['start_floor'],
                            'end_floor' => $branchData['end_floor'],
                        ]
                    );

                    // Delete existing rooms and floor_rooms for this branch to recreate them
                    Room::where('branch_id', $branch->id)->delete();
                    FloorRoom::where('branch_id', $branch->id)->delete();
                    ExtraRoom::where('branch_id', $branch->id)->delete();

                    // Save floor rooms
                    if (isset($this->floorRooms[$branchIndex])) {
                        foreach ($this->floorRooms[$branchIndex] as $floor => $rooms) {
                            // Track room number offset for this floor to avoid duplicates
                            $roomNumberOffset = 1;

                            foreach ($rooms as $roomIndex => $room) {
                                // Check if custom room numbers exist for this room
                                $roomKey = "{$branchIndex}-{$floor}-{$roomIndex}";
                                $customRoomNumbers = $this->roomNumbersList[$roomKey] ?? null;

                                $floorRoom = FloorRoom::create([
                                    'branch_id' => $branch->id,
                                    'floor_number' => $floor,
                                    'room_type' => $room['room_type'] ?? '',
                                    'monthly_rent' => (int) ($room['monthly_rent'] ?? 0),
                                    'room_count' => (int) ($room['room_count'] ?? 1),
                                    'excluded_room_numbers' => [],
                                ]);

                                $monthlyRent = (int) ($room['monthly_rent'] ?? 0);
                                $roomCategory = $room['room_category'] ?? null;
                                $windowStructure = $room['window_structure'] ?? null;
                                $gender = $room['gender'] ?? null;

                                $roomType = $room['room_type'] ?? '';

                                if ($customRoomNumbers) {
                                    // Use custom room numbers
                                    foreach ($customRoomNumbers as $roomNumber) {
                                        Room::updateOrCreate(
                                            [
                                                'branch_id' => $branch->id,
                                                'room_number' => $roomNumber,
                                                'room_type' => $roomType,
                                            ],
                                            [
                                                'floor' => $floor,
                                                'room_category' => $roomCategory,
                                                'window_structure' => $windowStructure,
                                                'gender' => $gender,
                                                'monthly_rent' => $monthlyRent,
                                                'deposit' => 0,
                                                'status' => 'available',
                                            ]
                                        );
                                    }
                                } else {
                                    // Generate room numbers based on room_count
                                    $roomCount = (int) ($room['room_count'] ?? 1);

                                    for ($i = 0; $i < $roomCount; $i++) {
                                        $roomNumber = $floor . str_pad($roomNumberOffset, 2, '0', STR_PAD_LEFT);

                                        Room::updateOrCreate(
                                            [
                                                'branch_id' => $branch->id,
                                                'room_number' => $roomNumber,
                                                'room_type' => $roomType,
                                            ],
                                            [
                                                'floor' => $floor,
                                                'room_category' => $roomCategory,
                                                'window_structure' => $windowStructure,
                                                'gender' => $gender,
                                                'monthly_rent' => $monthlyRent,
                                                'deposit' => 0,
                                                'status' => 'available',
                                            ]
                                        );

                                        $roomNumberOffset++;
                                    }
                                }

                                // Update offset for next room type if custom numbers were used
                                if ($customRoomNumbers) {
                                    $roomNumberOffset += count($customRoomNumbers);
                                }
                            }
                        }
                    }

                    // Save extra rooms
                    if ($this->hasExtraRooms[$branchIndex] && isset($this->extraRooms[$branchIndex])) {
                        foreach ($this->extraRooms[$branchIndex] as $extraRoomIndex => $room) {
                            $extraRoom = ExtraRoom::create([
                                'branch_id' => $branch->id,
                                'room_type' => $room['room_type'] ?? '',
                                'room_category' => $room['room_category'] ?? null,
                                'window_structure' => $room['window_structure'] ?? null,
                                'gender' => $room['gender'] ?? null,
                                'monthly_rent' => (int) ($room['monthly_rent'] ?? 0),
                                'room_count' => (int) ($room['room_count'] ?? 1),
                            ]);

                            // Create individual Room records for extra rooms
                            $roomCount = (int) ($room['room_count'] ?? 1);
                            $monthlyRent = (int) ($room['monthly_rent'] ?? 0);
                            $roomCategory = $room['room_category'] ?? null;
                            $windowStructure = $room['window_structure'] ?? null;
                            $gender = $room['gender'] ?? null;
                            $roomType = $room['room_type'] ?? '';

                            for ($i = 1; $i <= $roomCount; $i++) {
                                // Generate room number for extra rooms (e.g., 기타1, 기타2...)
                                $roomNumber = '기타' . $i;

                                Room::updateOrCreate(
                                    [
                                        'branch_id' => $branch->id,
                                        'room_number' => $roomNumber,
                                        'room_type' => $roomType,
                                    ],
                                    [
                                        'floor' => 0, // Extra rooms have floor 0
                                        'room_category' => $roomCategory,
                                        'window_structure' => $windowStructure,
                                        'gender' => $gender,
                                        'monthly_rent' => $monthlyRent,
                                        'deposit' => 0,
                                        'status' => 'available',
                                    ]
                                );
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
