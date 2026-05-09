import './bootstrap';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const path = `./Pages/${name}.jsx`;
        const page = pages[path];

        if (!page) {
            throw new Error(`Missing Inertia page: ${path}`);
        }

        return page.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});