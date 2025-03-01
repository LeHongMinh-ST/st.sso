import { LoginForm } from '@/components/login-form';
import { Head } from '@inertiajs/react';

export default function LoginPage() {
    return (
        <>
            <Head title="Đăng nhập" />
            <div className="bg-muted flex min-h-svh flex-col items-center justify-center p-6 md:p-10">
                <div className="w-full max-w-sm md:max-w-3xl">
                    <LoginForm />
                </div>
            </div>
        </>
    );
}
