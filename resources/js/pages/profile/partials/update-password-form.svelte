<script>
    import InputError from "@/components/input-error.svelte";
    import { Label } from "@/components/ui/label/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Input } from "@/components/ui/input/index.js";
    import { useForm } from "@inertiajs/svelte";
    import { toast } from "svelte-sonner";

    // Form data store
    const form = useForm({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    let currentPasswordInput;
    let passwordInput;

    // Function to update the password
    const updatePassword = async (event) => {
        event.preventDefault();
        $form.put(route("password.update"), {
            onSuccess: () => {
                $form.reset();
                toast.success("Password changed successfully");
            },
        });
    };
</script>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Update Password
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form on:submit={updatePassword} class="mt-6 space-y-6">
        <!-- Current Password -->
        <div>
            <Label for="current_password">Current Password</Label>
            <Input
                id="current_password"
                bind:this={currentPasswordInput}
                bind:value={$form.current_password}
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />
            {#if $form.errors.current_password}
                <InputError
                    message={$form.errors.current_password}
                    class="mt-2"
                />
            {/if}
        </div>

        <!-- New Password -->
        <div>
            <Label for="password">New Password</Label>
            <Input
                id="password"
                bind:this={passwordInput}
                bind:value={$form.password}
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            {#if $form.errors.password}
                <InputError message={$form.errors.password} class="mt-2" />
            {/if}
        </div>

        <!-- Confirm New Password -->
        <div>
            <Label for="password_confirmation">Confirm Password</Label>
            <Input
                id="password_confirmation"
                bind:value={$form.password_confirmation}
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            {#if $form.errors.password_confirmation}
                <InputError
                    message={$form.errors.password_confirmation}
                    class="mt-2"
                />
            {/if}
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-4">
            <Button disabled={$form.processing} type="submit">Save</Button>

            {#if $form.recentlySuccessful}
                <p class="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
            {/if}
        </div>
    </form>
</section>

<style>
    /* Optional: Add styles for form elements */
</style>
