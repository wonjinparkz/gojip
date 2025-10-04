<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use App\Models\Branch;
use App\Models\Room;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ListRooms extends Page
{
    protected static string $resource = RoomResource::class;

    protected string $view = 'filament.resources.room-resource.pages.list-rooms';

    public ?int $selectedBranchId = null;

    public function mount(): void
    {
        // 첫 번째 지점을 기본 선택
        $this->selectedBranchId = Branch::where('user_id', auth()->id())->first()?->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getBranches()
    {
        return Branch::where('user_id', auth()->id())->get();
    }

    public function selectBranch(?int $branchId): void
    {
        $this->selectedBranchId = $branchId;
    }

    public function getRooms()
    {
        $query = Room::with('branch');

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        return $query->paginate(12);
    }
}
