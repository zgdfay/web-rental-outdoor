<script>
    import { useForm } from "@inertiajs/svelte";
    import GuestLayout from "@/layouts/guest-layout.svelte";
    import InputError from "@/components/input-error.svelte";
    import { Label } from "@/components/ui/label/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Input } from "@/components/ui/input/index.js";

    let { status } = $props();

    const form = useForm({
        email: "",
    });

    const submit = (e) => {
        e.preventDefault();
        $form.post(route("password.email"));
    };
</script>

<GuestLayout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Forgot your password? No problem. Just let us know your email address
        and we will email you a password reset link that will allow you to
        choose a new one.
    </div>

    {#if status}
        <div
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {status}
        </div>
    {/if}

    <form onsubmit={submit}>
        <div>
            <Label for="email">Email</Label>

            <Input
                id="email"
                type="email"
                bind:value={$form.email}
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="username"
            />

            <InputError class="mt-2" message={$form.errors.email} />
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Button
                type="submit"
                class={`ms-4 ${$form.processing ? "opacity-25" : ""}`}
                disabled={$form.processing}
            >
                Email Password Reset Link
            </Button>
        </div>
    </form>
</GuestLayout>
