import { use, useCallback } from "react";

const SIDEBAR_KEY = "sidebar-open";

export function useSidebarState( defaultOpen: boolean ) {
    const getStored = (): boolean => {
        try{
            const stored = localStorage.getItem(SIDEBAR_KEY);
            return stored !== null ? stored === 'true' : defaultOpen;
        } catch {
            return defaultOpen;
        }
    };


    const persist = useCallback((open: boolean) => {
        try {
            localStorage.setItem(SIDEBAR_KEY, open.toString());
        } catch {}
            // Ignore storage errors
        }, []);

        return { getStored, persist };

}
