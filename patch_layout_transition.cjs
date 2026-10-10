const fs = require('fs');
let file = 'resources/js/Layouts/AppLayout.vue';
let content = fs.readFileSync(file, 'utf8');

// Replace the slot block with a transition
content = content.replace(
    /<slot name="header" \/>\n\s*<slot \/>/,
    `<slot name="header" />\n                <transition name="page" mode="out-in">\n                    <div :key="$page.url.split('?')[0]">\n                        <slot />\n                    </div>\n                </transition>`
);

fs.writeFileSync(file, content);
