<div style="padding: 1.5rem;" x-data="{
    editMode: @entangle('editMode').live,
    monthlyRentDisplay: '',
    depositDisplay: '',
    init() {
        this.$watch('editMode', value => {
            if (value) {
                this.monthlyRentDisplay = this.$wire.editMonthlyRent ? parseInt(this.$wire.editMonthlyRent).toLocaleString() : '';
                this.depositDisplay = this.$wire.editDeposit ? parseInt(this.$wire.editDeposit).toLocaleString() : '';
            }
        });
    },
    formatNumber(value) {
        return value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
}">
    <div class="view-room-modal-content">
        <!-- 층수 -->
        <div class="modal-field">
            <div class="modal-label">층수</div>
            <div class="modal-value" x-show="!editMode">{{ $record->floor }}층</div>
            <input x-show="editMode"
                   type="number"
                   wire:model="editFloor"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
        </div>

        <!-- 호실 번호 -->
        <div class="modal-field">
            <div class="modal-label">호실 번호</div>
            <div class="modal-value" x-show="!editMode">{{ $record->room_number }}호</div>
            <input x-show="editMode"
                   type="text"
                   wire:model="editRoomNumber"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
        </div>

        <!-- 월세 -->
        <div class="modal-field">
            <div class="modal-label">월세</div>
            <div class="modal-value" x-show="!editMode">₩{{ number_format($record->monthly_rent) }}</div>
            <input x-show="editMode"
                   type="text"
                   x-model="monthlyRentDisplay"
                   x-on:input="
                       monthlyRentDisplay = formatNumber(monthlyRentDisplay);
                       $wire.editMonthlyRent = monthlyRentDisplay.replace(/,/g, '');
                   "
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
        </div>

        <!-- 보증금 -->
        <div class="modal-field">
            <div class="modal-label">보증금</div>
            <div class="modal-value" x-show="!editMode">₩{{ number_format($record->deposit) }}</div>
            <input x-show="editMode"
                   type="text"
                   x-model="depositDisplay"
                   x-on:input="
                       depositDisplay = formatNumber(depositDisplay);
                       $wire.editDeposit = depositDisplay.replace(/,/g, '');
                   "
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
        </div>

        <!-- 방 타입 -->
        <div class="modal-field">
            <div class="modal-label">방 타입</div>
            <div class="modal-value" x-show="!editMode">{{ $record->room_type }}</div>
            <input x-show="editMode"
                   type="text"
                   wire:model="editRoomType"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
        </div>

        <!-- 상태 -->
        <div class="modal-field">
            <div class="modal-label">상태</div>
            <div class="modal-value" x-show="!editMode">
                @if($record->status === 'occupied')
                    입주중
                @elseif($record->status === 'available')
                    입주가능
                @else
                    수리중
                @endif
            </div>
            <select x-show="editMode"
                    wire:model="editStatus"
                    style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 500; color: #111827;">
                <option value="occupied">입주중</option>
                <option value="available">입주가능</option>
                <option value="maintenance">수리중</option>
            </select>
        </div>
    </div>
</div>

<style>
    .view-room-modal-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .modal-field {
        padding: 0.75rem 0;
    }

    .modal-label {
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .modal-value {
        font-weight: 500;
        color: #111827;
    }

    @media (min-width: 768px) {
        .view-room-modal-content {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
