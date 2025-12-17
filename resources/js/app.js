import './bootstrap';
import { initTheme } from './theme';

initTheme();

// Create blend form that adds a new field to it.
document.addEventListener('DOMContentLoaded', () => {
   const container = document.querySelector('[data-testid="ingredients-container"]');
   const addButton = document.querySelector('[data-testid="add-ingredient"]');
   const template = document.querySelector('[data-testid="ingredient-template"]');

   if (!container || !addButton || !template) {
      return;
   }

   let index = 1;

   addButton.addEventListener('click', () => {
      const html = template.innerHTML.replaceAll('__INDEX__', index);
      container.insertAdjacentHTML('beforeend', html);

      index++;
   });
});
