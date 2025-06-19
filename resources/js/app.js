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
  key: 'dd36ece1901fae0d49bf',
  cluster: 'ap1',
  forceTLS: true
});
