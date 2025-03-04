import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export function LoginForm({ className, ...props }: React.ComponentProps<'div'>) {
    const { data, setData, post, processing, get } = useForm({
        email: '',
        password: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('login'));
    };

    const [imageSrc, setImageSrc] = useState('/images/auth/login.svg');

    useEffect(() => {
        const isDark = document.documentElement.classList.contains('dark');
        setImageSrc(isDark ? '/images/auth/login-dark.svg' : '/images/auth/login.svg');
    }, []);

    const loginWithMicrosoft = () => {
        try {
            post(route('login.microsoft'));
        } catch (error) {
            console.error(error);
        }
    };

    return (
        <div className={cn('flex flex-col gap-6', className)} {...props}>
            <Card className="overflow-hidden">
                <CardContent className="grid p-0 md:grid-cols-2">
                    <form className="p-6 md:p-8" onSubmit={handleSubmit}>
                        <div className="flex flex-col gap-6">
                            <div className="flex flex-col items-center text-center">
                                <h1 className="text-2xl font-bold">Hệ thống ST SSO</h1>
                                <p className="text-muted-foreground text-balance">Đăng nhập vào tài khoản của bạn</p>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="email">Tài khoản/Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    placeholder="email@example.com"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                />
                            </div>
                            <div className="grid gap-2">
                                <div className="flex items-center">
                                    <Label htmlFor="password">Mật khẩu</Label>
                                    <a href={route('password.request')} className="ml-auto text-sm underline-offset-2 hover:underline">
                                        Quên mật khẩu?
                                    </a>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    required
                                />
                            </div>
                            <Button type="submit" className="w-full" disabled={processing}>
                                Đăng nhập
                            </Button>
                            <div className="after:border-border relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t">
                                <span className="bg-background text-muted-foreground relative z-10 px-2">Hoặc tiếp tục với</span>
                            </div>
                            <Button onClick={loginWithMicrosoft} variant="outline" className="w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23" className="mr-2 h-5 w-5">
                                    <path
                                        fill="currentColor"
                                        d="M11.04 0v11.04H0V0h11.04zM23 0v11.04H11.96V0H23zM11.04 11.96V23H0V11.96h11.04zM23 11.96V23H11.96V11.96H23z"
                                    />
                                </svg>
                                Đăng nhập với tài khoản Microsoft
                            </Button>
                            <div className="text-center text-sm"></div>
                        </div>
                    </form>
                    <div className="bg-muted relative hidden md:block">
                        <img src={imageSrc} alt="Hình ảnh" className="absolute inset-0 h-full w-full object-cover" />
                    </div>
                </CardContent>
            </Card>
        </div>
    );
}
