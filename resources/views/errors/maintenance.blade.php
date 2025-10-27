{{-- resources/views/errors/maintenance.blade.php --}}
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Maintenance Mode</title>

      {{-- Auto-refresh (only when Laravel passes $refresh) --}}
      @isset($refresh)
         <meta http-equiv="refresh" content="{{ $refresh }}" />
      @endisset

      <style>
         body {
            display: flex;
            height: 100vh;
            margin: 0;
            align-items: center;
            justify-content: center;
            font-family: system-ui, sans-serif;
            background: #0a0a0a;
            color: #f0f0f0;
         }
         h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
         }
      </style>
   </head>
   <body>
      <div>
         <h1>We’ll be back soon!</h1>
         <p>The site is currently under maintenance. Please check back in a few moments.</p>
      </div>
   </body>
</html>
