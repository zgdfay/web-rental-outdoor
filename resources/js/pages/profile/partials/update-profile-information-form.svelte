<script>
    import { page, router, useForm } from "@inertiajs/svelte";
    import InputError from "@/components/input-error.svelte";
    import { Label } from "@/components/ui/label/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Input } from "@/components/ui/input/index.js";
    import { get } from "svelte/store";
    import { toast } from "svelte-sonner";

    let { mustVerifyEmail, status } = $props();

    const user = get(page).props.auth.user;

    const form = useForm({
        name: user.name,
        email: user.email,
    });

    // Function to handle form submission
    const updateProfile = async (e) => {
        e.preventDefault();

        $form.patch(route("profile.update"), {
            onSuccess: () => {
                toast.success("Profile updated successfully");
            },
        });
    };
</script>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Update your account's profile information and email address.
        </p>
    </header>

    <form onsubmit={updateProfile} class="mt-6 space-y-6">
        <!-- Name -->
        <div>
            <Label for="name">Name</Label>
            <Input
                id="name"
                type="text"
                class="mt-1 block w-full"
                bind:value={$form.name}
                required
                autofocus
                autocomplete="name"
            />
            {#if $form.errors.name}
                <InputError class="mt-2" message={$form.errors.name} />
            {/if}
        </div>

        <!-- Email -->
        <div>
            <Label for="email">Email</Label>
            <Input
                id="email"
                type="email"
                class="mt-1 block w-full"
                bind:value={$form.email}
                required
                autocomplete="username"
            />
            {#if $form.errors.email}
                <InputError class="mt-2" message={$form.errors.email} />
            {/if}
        </div>

        <!-- Email Verification -->
        {#if mustVerifyEmail && user.email_verified_at === null}
            <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                Your email address is unverified.
                <Link
                    href="/verification/send"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Click here to re-send the verification email.
                </Link>
            </p>

            {#if status === "verification-link-sent"}
                <div
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            {/if}
        {/if}

        <!-- Submit Button -->
        <div class="flex items-center gap-4">
            <Button disabled={$form.processing} type="submit">Save</Button>
        </div>
    </form>
</section>

<style>
    /* Add any necessary styles here */
</style>
