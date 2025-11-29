@props(['disabled' => false])

<input
   @disabled($disabled)
   {{
      $attributes->merge([
         'class' => 'shadow-sm transition ease-in-out duration-150',
      ])
   }}
/>
