const fs = require('fs');
let file = 'resources/js/Pages/Purchases/Form.vue';
let content = fs.readFileSync(file, 'utf8');

content = content.replace(
    /<!-- Product search modal -->\n\s*<div v-if="showProductSearch"/,
    `<!-- Product search modal -->\n    <transition name="modal">\n    <div v-if="showProductSearch"`
);

// Close the transition
content = content.replace(
    /<\/div>\n    <\/div>\n  <\/AppLayout>/,
    `</div>\n    </div>\n    </transition>\n  </AppLayout>`
);

fs.writeFileSync(file, content);
