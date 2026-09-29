import { useState, useCallback } from 'react';
import { router } from '@inertiajs/react';

interface FormOptions {
    resetOnSuccess?: boolean;
}

interface UseFormReturn<T extends Record<string, any>> {
    data: T;
    errors: Partial<Record<keyof T, string>>;
    processing: boolean;
    wasSuccessful: boolean;
    setData: ((key: keyof T, value: any) => void) & ((data: Partial<T>) => void);
    reset: (...fields: (keyof T)[]) => void;
    clearErrors: (...fields: (keyof T)[]) => void;
    submit: (method: 'post' | 'put' | 'patch' | 'delete', url: string, options?: FormOptions) => void;
}

/**
 * Inertia form helper — manages form state, validation errors, and submission.
 *
 * Usage:
 *   const { data, setData, errors, processing, submit } = useForm({
 *       email: '',
 *       password: '',
 *   });
 */
export function useForm<T extends Record<string, any>>(initial: T): UseFormReturn<T> {
    const [data, setDataState] = useState<T>(initial);
    const [errors, setErrors] = useState<Partial<Record<keyof T, string>>>({});
    const [processing, setProcessing] = useState(false);
    const [wasSuccessful, setWasSuccessful] = useState(false);

    const setData = useCallback((keyOrData: keyof T | Partial<T>, value?: any) => {
        if (typeof keyOrData === 'object') {
            setDataState((prev: T) => ({ ...prev, ...keyOrData }));
        } else {
            setDataState((prev: T) => ({ ...prev, [keyOrData]: value }));
        }
    }, []);

    const reset = useCallback((...fields: (keyof T)[]) => {
        if (fields.length === 0) {
            setDataState(initial);
        } else {
            setDataState((prev: T) => {
                const next = { ...prev };
                fields.forEach(f => { (next as any)[f] = initial[f]; });
                return next;
            });
        }
        setErrors({});
    }, [initial]);

    const clearErrors = useCallback((...fields: (keyof T)[]) => {
        if (fields.length === 0) {
            setErrors({});
        } else {
            setErrors((prev: Partial<Record<keyof T, string>>) => {
                const next = { ...prev };
                fields.forEach(f => { delete next[f]; });
                return next;
            });
        }
    }, []);

    const submit = useCallback((method: 'post' | 'put' | 'patch' | 'delete', url: string, options?: FormOptions) => {
        setProcessing(true);
        setWasSuccessful(false);

        router[method](url, data, {
            onSuccess: () => {
                setProcessing(false);
                setWasSuccessful(true);
                setErrors({});
                if (options?.resetOnSuccess) {
                    setDataState(initial);
                }
            },
            onError: (errs: any) => {
                setProcessing(false);
                setErrors(errs || {});
            },
        });
    }, [data, initial]);

    return { data, errors, processing, wasSuccessful, setData, reset, clearErrors, submit };
}
