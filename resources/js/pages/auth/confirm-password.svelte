<script>
    import { useForm } from "@inertiajs/svelte";
    import GuestLayout from "@/layouts/guest-layout.svelte";
    import InputError from "@/components/input-error.svelte";
    import { Label } from "@/components/ui/label/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Input } from "@/components/ui/input/index.js";

    const form = useForm({
        password: "",
    });

    const submit = (e) => {
        e.preventDefault();
        $form.post(route("password.confirm"), {
            onFinish: () => form.reset(),
        });
    };
</script>

<GuestLayout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        This is a secure area of the application. Please confirm your password
        before continuing.
    </div>

    <form onsubmit={submit}>
        <div>
            <Label for="password">Password</Label>

            <Input
                id="password"
                type="password"
                bind:value={$form.password}
                class="mt-1 block w-full"
                required
                autocomplete="current-password"
                autofocus
            />

            <InputError class="mt-2" message={$form.errors.password} />
        </div>

        <div class="mt-4 flex justify-end">
            <Button
                type="submit"
                class={`ms-4 ${$form.processing ? "opacity-25" : ""}`}
                disabled={$form.processing}
            >
                Confirm
            </Button>
        </div>
    </form>
</GuestLayout>
