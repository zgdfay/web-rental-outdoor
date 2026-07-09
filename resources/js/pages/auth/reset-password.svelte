<script>
    import { useForm } from "@inertiajs/svelte";
    import GuestLayout from "@/layouts/guest-layout.svelte";
    import InputError from "@/components/input-error.svelte";
    import { Label } from "@/components/ui/label/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Input } from "@/components/ui/input/index.js";

    let { email, token } = $props();

    let form = useForm({
        email,
        token,
        password: "",
        password_confirmation: "",
    });

    const submit = (e) => {
        e.preventDefault();

        $form.post(route("password.store"), {
            onFinish: () => {
                $form.reset();
            },
        });
    };
</script>

<GuestLayout>
    <Head title="Reset Password" />

    <form onsubmit={submit}>
        <div>
            <Label for="email">Email</Label>

            <Input
                id="email"
                type="email"
                bind:value={form.email}
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="username"
            />

            <InputError class="mt-2" message={form.errors.email} />
        </div>

        <div class="mt-4">
            <Label for="password">Password</Label>

            <Input
                id="password"
                type="password"
                bind:value={form.password}
                class="mt-1 block w-full"
                required
                autocomplete="new-password"
            />

            <InputError class="mt-2" message={form.errors.password} />
        </div>

        <div class="mt-4">
            <Label for="password_confirmation">Confirm Password</Label>

            <Input
                id="password_confirmation"
                type="password"
                bind:value={form.password_confirmation}
                class="mt-1 block w-full"
                required
                autocomplete="new-password"
            />

            <InputError
                class="mt-2"
                message={form.errors.password_confirmation}
            />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Button
                type="submit"
                class={$form.processing ? "opacity-25" : ""}
                disabled={form.processing}
            >
                Reset Password
            </Button>
        </div>
    </form>
</GuestLayout>
