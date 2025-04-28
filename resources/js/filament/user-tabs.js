// Lưu trạng thái tab trong localStorage
document.addEventListener('DOMContentLoaded', function() {
    // Tìm tất cả các tab trong form chỉnh sửa người dùng
    const tabsContainer = document.querySelector('.filament-profile-tabs .fi-tabs');
    
    if (!tabsContainer) return;
    
    const tabs = tabsContainer.querySelectorAll('.fi-tabs-item');
    
    // Lấy tab đang active từ URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTabParam = urlParams.get('activeTab');
    
    // Nếu có tham số activeTab trong URL, không cần làm gì vì Livewire sẽ xử lý
    if (activeTabParam) return;
    
    // Lấy tab đang active từ localStorage
    const savedTab = localStorage.getItem('userEditActiveTab');
    
    if (savedTab) {
        // Tìm tab có text content khớp với savedTab
        tabs.forEach(tab => {
            if (tab.textContent.trim() === savedTab.trim()) {
                // Click vào tab để kích hoạt nó
                tab.click();
            }
        });
    }
    
    // Lưu tab đang active vào localStorage khi click
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            localStorage.setItem('userEditActiveTab', tab.textContent.trim());
        });
    });
});
