<script module>
    export { default as layout } from '@/layouts/authenticated-layout.svelte';
</script>

<script>
    import DeleteUserForm from './partials/delete-user-form.svelte';
    import UpdatePasswordForm from './partials/update-password-form.svelte';
    import UpdateProfileInformationForm from './partials/update-profile-information-form.svelte';

    let { mustVerifyEmail, status } = $props();
</script>

<div class="py-12">
    <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
            <UpdateProfileInformationForm {mustVerifyEmail} {status} class="max-w-xl" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
            <UpdatePasswordForm class="max-w-xl" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
            <DeleteUserForm class="max-w-xl" />
        </div>
    </div>
</div>
