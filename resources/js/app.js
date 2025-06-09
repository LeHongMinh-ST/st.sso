import "./bootstrap";
import Echo from "laravel-echo";
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
Livewire.start()

Noty.overrideDefaults({
    theme: 'limitless',
    layout: 'topRight',
    type: 'alert',
    timeout: 2500
});

window.addEventListener('alert', event => {
    new Noty({
        title: event.detail.title ?? '',
        text: event.detail.message,
        type: event.detail.type ?? 'alert',
    }).show();
});

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: '504ac0f5fa1e4c90ce76',
  cluster: 'ap1',
  forceTLS: true
});

window.Echo.connector.socket.on('connect', () => {
    console.log('Đã kết nối với Pusher WebSocket');
});

window.Echo.connector.socket.on('disconnect', () => {
    console.log('Mất kết nối Pusher WebSocket');
});

window.Echo.connector.socket.on('error', (error) => {
    console.error('Lỗi kết nối WebSocket:', error);
});