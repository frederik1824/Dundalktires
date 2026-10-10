const fs = require('fs');

let css = fs.readFileSync('resources/css/app.css', 'utf8');

css = css.replace(/--color-brand-50:.*;/, '--color-brand-50: #fef2f2;');
css = css.replace(/--color-brand-100:.*;/, '--color-brand-100: #fee2e2;');
css = css.replace(/--color-brand-200:.*;/, '--color-brand-200: #fecaca;');
css = css.replace(/--color-brand-300:.*;/, '--color-brand-300: #fca5a5;');
css = css.replace(/--color-brand-400:.*;/, '--color-brand-400: #f87171;');
css = css.replace(/--color-brand-500:.*;/, '--color-brand-500: #ef4444;');
css = css.replace(/--color-brand-600:.*;/, '--color-brand-600: #dc2626;');
css = css.replace(/--color-brand-700:.*;/, '--color-brand-700: #b91c1c;');
css = css.replace(/--color-brand-800:.*;/, '--color-brand-800: #991b1b;');
css = css.replace(/--color-brand-900:.*;/, '--color-brand-900: #7f1d1d;');

fs.writeFileSync('resources/css/app.css', css);
