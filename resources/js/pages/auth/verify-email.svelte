<script>
    import GuestLayout from "@/layouts/guest-layout.svelte";
    import { Button } from "@/components/ui/button/index.js";
    import { Link } from "@inertiajs/svelte";

    const { status } = $props();

    const form = useForm({
        processing: false,
    });

    const verificationLinkSent = $derived(
        () => status === "verification-link-sent",
    );

    const submit = (e) => {
        e.preventDefault();
        $form.post(route("verification.send"));
    };
</script>

<GuestLayout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Thanks for signing up! Before getting started, could you verify your
        email address by clicking on the link we just emailed to you? If you
        didn't receive the email, we will gladly send you another.
    </div>

    {#if verificationLinkSent}
        <div
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>
    {/if}

    <form onsubmit={submit}>
        <div class="mt-4 flex items-center justify-between">
            <Button
                type="submit"
                class={$form.processing ? "opacity-25" : ""}
                disabled={$form.processing}
            >
                Resend Verification Email
            </Button>

            <Link
                href={route("logout")}
                method="post"
                as="button"
                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
            >
                Log Out
            </Link>
        </div>
    </form>
</GuestLayout>
