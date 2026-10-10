const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(file));
        } else if (file.endsWith('.vue')) {
            results.push(file);
        }
    });
    return results;
}

const files = walk('./resources/js/Pages');

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    
    // Replace <Link href="..." class="btn btn-primary">{{ t('xxx.add') }}</Link>
    content = content.replace(/(<Link[^>]+class="[^"]*btn-primary[^"]*"[^>]*>)(\{\{\s*t\('[^']+'\)\s*\}\})<\/Link>/g, '$1\n                                <Icon name="plus" />\n                                $2\n                            </Link>');
    
    // Replace <Link href="..." class="btn btn-primary">Add Product</Link>
    content = content.replace(/(<Link[^>]+class="[^"]*btn-primary[^"]*"[^>]*>)(Add [A-Za-z]+)<\/Link>/g, '$1\n                                <Icon name="plus" />\n                                $2\n                            </Link>');

    if (content !== original) {
        if (!content.includes("import Icon from '@/Components/Icon.vue'")) {
            content = content.replace(/<script setup>([\s\S]*?)<\/script>/, (match, scriptBody) => {
                let newBody = "\nimport Icon from '@/Components/Icon.vue';\n" + scriptBody.trim();
                return `<script setup>\n${newBody}\n<\/script>`;
            });
        }
        fs.writeFileSync(file, content);
        console.log(`Updated ${file}`);
    }
});
