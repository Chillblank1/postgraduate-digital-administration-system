import './bootstrap';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

const jsxPages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
const tsxPages = import.meta.glob('./pages/**/*.tsx');

createInertiaApp({
    resolve: async (name) => {
        const tsxPath = `./pages/${name}.tsx`;
        if (tsxPages[tsxPath]) {
            const module = await tsxPages[tsxPath]();

            return module.default;
        }

        const jsxPath = `./Pages/${name}.jsx`;
        const page = jsxPages[jsxPath];

        if (!page) {
            throw new Error(`Missing Inertia page: ${tsxPath} or ${jsxPath}`);
        }

        return page.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
