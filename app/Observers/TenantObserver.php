<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Models\Room;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)
                ->update([
                    'tenant_name' => $tenant->name,
                    'move_in_date' => $tenant->move_in_date,
                    'move_out_date' => $tenant->move_out_date,
                ]);
        }
    }

    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        // 호실이 변경되었는지 확인
        if ($tenant->isDirty('room_id')) {
            // 이전 호실의 tenant_name 초기화
            $originalRoomId = $tenant->getOriginal('room_id');
            if ($originalRoomId) {
                Room::where('id', $originalRoomId)
                    ->update([
                        'tenant_name' => null,
                        'move_in_date' => null,
                        'move_out_date' => null,
                    ]);
            }

            // 새 호실에 tenant_name 설정
            if ($tenant->room_id) {
                Room::where('id', $tenant->room_id)
                    ->update([
                        'tenant_name' => $tenant->name,
                        'move_in_date' => $tenant->move_in_date,
                        'move_out_date' => $tenant->move_out_date,
                    ]);
            }
        } else {
            // 이름, 입주일, 퇴실일 중 하나라도 변경된 경우
            if (($tenant->isDirty('name') || $tenant->isDirty('move_in_date') || $tenant->isDirty('move_out_date')) && $tenant->room_id) {
                Room::where('id', $tenant->room_id)
                    ->update([
                        'tenant_name' => $tenant->name,
                        'move_in_date' => $tenant->move_in_date,
                        'move_out_date' => $tenant->move_out_date,
                    ]);
            }
        }
    }

    /**
     * Handle the Tenant "deleted" event.
     */
    public function deleted(Tenant $tenant): void
    {
        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)
                ->update([
                    'tenant_name' => null,
                    'move_in_date' => null,
                    'move_out_date' => null,
                ]);
        }
    }

    /**
     * Handle the Tenant "restored" event.
     */
    public function restored(Tenant $tenant): void
    {
        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)
                ->update([
                    'tenant_name' => $tenant->name,
                    'move_in_date' => $tenant->move_in_date,
                    'move_out_date' => $tenant->move_out_date,
                ]);
        }
    }

    /**
     * Handle the Tenant "force deleted" event.
     */
    public function forceDeleted(Tenant $tenant): void
    {
        if ($tenant->room_id) {
            Room::where('id', $tenant->room_id)
                ->update([
                    'tenant_name' => null,
                    'move_in_date' => null,
                    'move_out_date' => null,
                ]);
        }
    }
}
