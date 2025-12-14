// Alpine.js 재사용 가능한 컴포넌트 정의
// Livewire와 함께 사용하기 위해 window.Alpine에 직접 등록

function registerAlpineComponents() {
    if (typeof window.Alpine !== 'undefined') {
        // 퇴실일 미정 컴포넌트
        window.Alpine.data('indefiniteMoveOut', (wireProperty = 'indefiniteMoveOut', moveOutDateProperty = 'moveOutDate') => ({
            indefinite: false,

            init() {
                // Livewire 속성과 연동
                this.$watch('indefinite', value => {
                    if (value) {
                        this.updateMoveOutDate();
                    }
                });
            },

            updateMoveOutDate() {
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const dateString = `${year}-${month}-${day}`;

                if (this.$wire && moveOutDateProperty) {
                    this.$wire.set(moveOutDateProperty, dateString);
                }
            },

            getDateInputStyle() {
                if (this.indefinite) {
                    return 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f3f4f6; cursor: not-allowed;';
                }
                return 'width: 100%; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;';
            }
        }));

        // 금액 포맷팅 컴포넌트
        window.Alpine.data('currencyInput', (wireProperty) => ({
            displayValue: '',

            init() {
                // 초기 값 로드
                this.$nextTick(() => {
                    if (this.$wire && wireProperty) {
                        const initialValue = this.$wire.get(wireProperty);
                        if (initialValue) {
                            this.displayValue = this.formatNumber(initialValue);
                        }
                    }
                });
            },

            formatNumber(value) {
                if (!value && value !== 0) return '';
                const num = String(value).replace(/[^\d]/g, '');
                return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            },

            handleInput(event) {
                const input = event.target;
                const rawValue = input.value.replace(/[^\d]/g, '');

                // 포맷된 값 표시
                this.displayValue = this.formatNumber(rawValue);
                input.value = this.displayValue;

                // Livewire에 숫자만 전달
                if (this.$wire && wireProperty) {
                    this.$wire.set(wireProperty, rawValue ? parseInt(rawValue) : null);
                }
            }
        }));
    } else {
        // Alpine이 아직 로드되지 않았다면 100ms 후 재시도
        setTimeout(registerAlpineComponents, 100);
    }
}

// Alpine이 로드될 때까지 기다렸다가 등록
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', registerAlpineComponents);
} else {
    registerAlpineComponents();
}
