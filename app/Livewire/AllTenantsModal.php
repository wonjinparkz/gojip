<?php

namespace App\Livewire;

use App\Models\Room;
use Livewire\Component;
use Livewire\Attributes\On;

class AllTenantsModal extends Component
{
    public bool $show = false;
    public $rooms = [];

    #[On('open-all-tenants-modal')]
    public function open()
    {
        $this->loadRooms();
        $this->show = true;
    }

    public function loadRooms()
    {
        $branchId = session('current_branch_id');

        $this->rooms = Room::where('branch_id', $branchId)
            ->with('tenant')
            ->orderBy('room_number', 'asc')
            ->get()
            ->toArray();
    }

    public function close()
    {
        $this->show = false;
    }

    public function goToTenantsPage()
    {
        return redirect()->route('filament.admin.resources.tenants.index');
    }

    public function render()
    {
        return view('livewire.all-tenants-modal');
    }
}
