const fs = require('fs');
let file = 'resources/views/app.blade.php';
let content = fs.readFileSync(file, 'utf8');

let tags = `
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#f8fafc">
`;

content = content.replace(/<title inertia>/, tags + '    <title inertia>');
fs.writeFileSync(file, content);
