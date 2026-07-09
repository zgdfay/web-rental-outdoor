<script>
  import { Link, useForm } from '@inertiajs/svelte';
  import GuestLayout from '@/layouts/guest-layout.svelte';
  import InputError from '@/components/input-error.svelte';
  import { Label } from '@/components/ui/label/index.js';
  import { Button } from '@/components/ui/button/index.js';
  import { Input } from '@/components/ui/input/index.js';

  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const submit = (e) => {
    e.preventDefault();
    $form.post(route('register'));
  };
</script>

<GuestLayout>
  <form onsubmit={submit} class="flex flex-col gap-4 py-4">
    <div class="flex flex-col gap-1">
      <Label for="name">Name</Label>

      <Input
        id="name"
        type="text"
        bind:value={$form.name}
        class="mt-1 block w-full"
        required
        autofocus
        autocomplete="name"
      />

      <InputError class="mt-2" message={$form.errors.name} />
    </div>

    <div class="flex flex-col gap-1">
      <Label for="email">Email</Label>

      <Input
        id="email"
        type="email"
        bind:value={$form.email}
        class="mt-1 block w-full"
        required
        autocomplete="username"
      />

      <InputError class="mt-2" message={$form.errors.email} />
    </div>

    <div class="flex flex-col gap-1">
      <Label for="password">Password</Label>

      <Input
        id="password"
        type="password"
        bind:value={$form.password}
        class="mt-1 block w-full"
        required
        autocomplete="new-password"
      />

      <InputError class="mt-2" message={$form.errors.password} />
    </div>

    <div class="flex flex-col gap-1">
      <Label for="password_confirmation">Confirm Password</Label>

      <Input
        id="password_confirmation"
        type="password"
        bind:value={$form.password_confirmation}
        class="mt-1 block w-full"
        required
        autocomplete="new-password"
      />

      <InputError class="mt-2" message={$form.errors.password_confirmation} />
    </div>

    <div class="mt-4 flex items-center justify-end gap-2">
      <Link
        href={route('login')}
        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
      >
        Already registered?
      </Link>

      <Button
        type="submit"
        class={'ms-4' + ($form.processing ? 'opacity-25' : '')}
        disabled={$form.processing}
      >
        Register
      </Button>
    </div>
  </form>
</GuestLayout>
