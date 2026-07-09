<script>
    import { useForm } from "@inertiajs/svelte";

    import InputError from "@/components/input-error.svelte";
    import { Input } from "@/components/ui/input/index.js";
    import { Button } from "@/components/ui/button/index.js";
    import { Label } from "@/components/ui/label/index.js";

    import * as Dialog from "@/components/ui/dialog/index.js";

    let confirmingUserDeletion = $state(false);
    let passwordInput;

    let form = useForm({
        password: "",
    });

    $effect(() => {
        if (confirmingUserDeletion) {
            passwordInput.focus();
        }
    });

    const deleteUser = (e) => {
        e.preventDefault();
        $form.delete(route("profile.destroy"), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.focus(),
            onFinish: () => $form.reset(),
        });
    };

    const closeModal = () => {
        confirmingUserDeletion = false;

        $form.clearErrors();
        $form.reset();
    };
</script>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Delete Account
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Once your account is deleted, all of its resources and data will be
            permanently deleted. Before deleting your account, please download
            any data or information that you wish to retain.
        </p>
    </header>

    <Dialog.Root open={confirmingUserDeletion}>
        <Dialog.Trigger>
            <Button variant="destructive">Delete Account</Button>
        </Dialog.Trigger>
        <Dialog.Content>
            <Dialog.Header>
                <Dialog.Title
                    >Are you sure you want to delete your account?</Dialog.Title
                >
                <Dialog.Description>
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </Dialog.Description>
            </Dialog.Header>
            <form action="" onsubmit={deleteUser}>
                <div class="p-6">
                    <div class="mt-6">
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            bind:this={passwordInput}
                            bind:value={$form.password}
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                        />

                        <InputError
                            message={$form.errors.password}
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <Dialog.Close>
                            <Button>Cancel</Button>
                        </Dialog.Close>

                        <Button
                            type="submit"
                            variant="destructive"
                            class={"ms-3" +
                                ($form.processing ? "opacity-25" : "")}
                            disabled={$form.processing}
                        >
                            Delete Account
                        </Button>
                    </div>
                </div>
            </form>
        </Dialog.Content>
    </Dialog.Root>
</section>
