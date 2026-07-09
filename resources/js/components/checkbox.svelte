<script>
    export let checked;  // This will be bound to the parent component
    export let value = null;  // Default value is null
    
    // Emit the change back to the parent
    const updateChecked = (event) => {
      checked = event.target.checked;
    };
  </script>
  
  <input
    type="checkbox"
    bind:checked
    value={value}
    on:change={updateChecked}
    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
  />
  