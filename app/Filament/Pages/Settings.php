<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = '설정';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.settings';

    // 계정 정보
    public string $userName = '';
    public string $userEmail = '';

    // 보안
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $confirmPassword = '';

    // 알림 설정 (DB 미연동 - 향후 구현 예정)
    public bool $expirationAlert = true;
    public string $expirationAlertDays = '7';
    public bool $paymentAlert = true;
    public string $paymentAlertDays = '3';
    public bool $systemUpdateAlert = false;

    // 시스템 (DB 미연동 - 향후 구현 예정)
    public bool $autoBackup = true;
    public string $backupFrequency = 'daily';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->userName = $user->name ?? '';
        $this->userEmail = $user->email ?? '';
    }

    public function saveAccount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $this->userName,
            'email' => $this->userEmail,
        ]);

        Notification::make()->title('계정 정보가 저장되었습니다.')->success()->send();
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8',
            'confirmPassword' => 'required|same:newPassword',
        ], [
            'newPassword.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'confirmPassword.same' => '새 비밀번호와 확인 비밀번호가 일치하지 않습니다.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            Notification::make()->title('현재 비밀번호가 올바르지 않습니다.')->danger()->send();
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);
        Notification::make()->title('비밀번호가 변경되었습니다.')->success()->send();
    }

    public function saveNotifications(): void
    {
        // TODO: 알림 설정 DB 테이블 연동 후 실제 저장 로직 구현
        Notification::make()->title('알림 설정이 저장되었습니다.')->success()->send();
    }

    public function saveSystem(): void
    {
        // TODO: 시스템 설정 DB 테이블 연동 후 실제 저장 로직 구현
        Notification::make()->title('시스템 설정이 저장되었습니다.')->success()->send();
    }

    public function getTitle(): string
    {
        return '설정';
    }
}
