@props([
   'messages',
])

@if ($messages)
   <ul {{ $attributes->merge(['class' => 'text-sm  space-y-1 text-red-600 dark:text-red-400']) }}>
      @foreach ((array) $messages as $message)
         <li>{{ $message }}</li>
      @endforeach
   </ul>
@endif
