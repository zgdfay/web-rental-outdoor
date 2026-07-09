<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import { Mail, Lock, ArrowRight, ChevronLeft, Eye, EyeOff } from 'lucide-svelte';
    import InputError from '@/components/input-error.svelte';
    import { Label } from '@/components/ui/label/index.js';
    import { Button } from '@/components/ui/button/index.js';
    import { Input } from '@/components/ui/input/index.js';
    import { Checkbox } from '@/components/ui/checkbox/index.js';

    let { canResetPassword, status } = $props();
    let showPassword = $state(false);

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        $form.post(route('login'));
    };
</script>

<svelte:head>
    <title>Login - Outdoor Species</title>
</svelte:head>

<div class="flex min-h-screen bg-zinc-950 text-white overflow-hidden">
    <!-- Left Side: Image & Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <img 
            src="/assets/images/login-bg.png" 
            alt="Outdoor Background" 
            class="absolute inset-0 w-full h-full object-cover grayscale-[0.2] brightness-[0.7]"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-zinc-950/20"></div>
        
        <div class="relative z-10 p-12 flex flex-col justify-center h-full">
            <div class="space-y-6">
                <h1 class="text-6xl font-black leading-[0.9]">
                    START YOUR <br />
                    <span class="text-primary text-7xl">ADVENTURE</span>
                </h1>
                <p class="text-zinc-400 text-lg max-w-md leading-relaxed">
                    Access your account to manage rentals, track your gear, and explore new territories with Outdoor Species.
                </p>
            </div>
        </div>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 md:p-12 relative bg-white text-zinc-950">
        <!-- Mobile Background (Visible only on mobile) -->
        <div class="lg:hidden absolute inset-0 z-0">
            <img src="/assets/images/login-bg.png" alt="bg" class="w-full h-full object-cover opacity-20 blur-sm" />
            <div class="absolute inset-0 bg-zinc-950/80"></div>
        </div>

        <div class="w-full max-w-md space-y-10 relative z-10">
            <div class="space-y-2 text-center lg:text-left">
                <h2 class="text-3xl font-black tracking-tight text-zinc-900">Login to Account</h2>
                <p class="text-zinc-500 font-medium">Enter your credentials to continue your journey.</p>
            </div>

            {#if status}
                <div class="p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-500 text-sm font-bold">
                    {status}
                </div>
            {/if}

            <form onsubmit={submit} class="space-y-6">
                    <div class="space-y-2 text-zinc-900">
                        <Label for="email" class="text-xs font-bold uppercase tracking-widest text-zinc-400">Email Address</Label>
                        <div class="relative">
                            <Mail class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" size={18} />
                            <Input
                                id="email"
                                type="email"
                                bind:value={$form.email}
                                placeholder="name@example.com"
                                class="h-12 pl-11 bg-zinc-50 border-zinc-200 focus:border-primary focus:ring-primary/20 rounded-xl transition-all text-zinc-900 placeholder:text-zinc-300"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                        <InputError message={$form.errors.email} />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-xs font-bold uppercase tracking-widest text-zinc-400">Password</Label>
                            {#if canResetPassword}
                                <Link
                                    href={route('password.request')}
                                    class="text-xs font-bold text-primary hover:underline underline-offset-4"
                                >
                                    Forgot password?
                                </Link>
                            {/if}
                        </div>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400" size={18} />
                            <Input
                                id="password"
                                type={showPassword ? "text" : "password"}
                                bind:value={$form.password}
                                placeholder="••••••••"
                                class="h-12 pl-11 pr-11 bg-zinc-50 border-zinc-200 focus:border-primary focus:ring-primary/20 rounded-xl transition-all text-zinc-900 placeholder:text-zinc-300"
                                required
                                autocomplete="current-password"
                            />
                            <button 
                                type="button"
                                onclick={() => showPassword = !showPassword}
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 transition-colors"
                            >
                                {#if showPassword}
                                    <EyeOff size={18} />
                                {:else}
                                    <Eye size={18} />
                                {/if}
                            </button>
                        </div>
                        <InputError message={$form.errors.password} />
                    </div>

                    <div class="flex items-center gap-2 py-2">
                        <Checkbox id="remember" bind:checked={$form.remember} class="border-zinc-200 data-[state=checked]:bg-primary data-[state=checked]:border-primary" />
                        <label for="remember" class="text-sm font-medium text-zinc-500 cursor-pointer select-none">
                            Keep me signed in
                        </label>
                    </div>

                <Button
                    type="submit"
                    disabled={$form.processing}
                    class="w-full h-12 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all group overflow-hidden relative"
                >
                    {#if $form.processing}
                        <div class="flex items-center gap-2">
                            <span class="size-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            Processing...
                        </div>
                    {:else}
                        <div class="flex items-center justify-center gap-2 group-hover:translate-x-1 transition-transform">
                            Access Gear Account
                            <ArrowRight size={18} />
                        </div>
                    {/if}
                </Button>
            </form>

            <div class="pt-8 border-t border-zinc-100 mt-8 flex flex-col items-center gap-6">
                <Button 
                    variant="ghost" 
                    href="/" 
                    class="w-full h-12 flex items-center justify-center gap-2 text-zinc-400 hover:text-zinc-900 hover:bg-zinc-50 font-bold rounded-xl transition-all group"
                >
                    <ChevronLeft size={18} class="transition-transform group-hover:-translate-x-1" />
                    <span class="uppercase tracking-widest text-xs">Back to Store</span>
                </Button>
            </div>
        </div>
    </div>
</div>
