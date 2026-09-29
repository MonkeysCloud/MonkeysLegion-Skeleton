import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

interface FlashData {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
    message?: string;
    errors?: Record<string, string>;
}

interface ToastState extends FlashData {
    visible: boolean;
}

/**
 * Flash message composable — reads the `flash` prop from Inertia page data
 * and displays toast notifications.
 *
 * Usage:
 *   const { toast, dismiss } = useFlash();
 *   // toast.success, toast.error, etc. are available
 *   // toast.visible controls visibility
 */
export function useFlash() {
    const { flash } = usePage().props as { flash?: FlashData };
    const [toast, setToast] = useState<ToastState>({ visible: false });

    useEffect(() => {
        if (flash && (flash.success || flash.error || flash.warning || flash.info || flash.message)) {
            setToast({ ...flash, visible: true });

            // Auto-dismiss after 5 seconds
            const timer = setTimeout(() => {
                setToast((prev: ToastState) => ({ ...prev, visible: false }));
            }, 5000);

            return () => clearTimeout(timer);
        }
    }, [flash]);

    const dismiss = () => setToast((prev: ToastState) => ({ ...prev, visible: false }));

    return { toast, dismiss };
}
