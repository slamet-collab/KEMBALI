import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();
createIcons({
    attrs: {
        class: 'h-5 w-5',
    },
});
