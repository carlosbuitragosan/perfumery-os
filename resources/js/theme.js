const STORAGE_KEY = 'theme';

function applyTheme(theme) {
   const root = document.documentElement;

   if (theme === 'dark') {
      root.classList.add('dark');
      root.dataset.theme = 'dark';
   } else {
      root.classList.remove('dark');
      root.dataset.theme = 'light';
   }
}

export function initTheme() {
   const stored = localStorage.getItem(STORAGE_KEY);
   const prefersDark =
      window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

   const initialTheme = stored || (prefersDark ? 'dark' : 'light');
   applyTheme(initialTheme);

   window.setTheme = (theme) => {
      localStorage.setItem(STORAGE_KEY, theme);
      applyTheme(theme);
   };

   window.toggleTheme = () => {
      const current = localStorage.getItem('theme');
      const next = current === 'dark' ? 'light' : 'dark';

      setTheme(next);
   };
}
