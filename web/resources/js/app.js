import '../css/app.css';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
    pages: './Pages',
    title: (title) => title || 'Cây Cảnh',
    progress: { color: '#6fb83f' },
});
