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
    
    // Add import Icon from '@/Components/Icon.vue'; if not present and if there are buttons that need it
    let needsImport = false;
    
    // 1. Edit buttons: >{{ t('common.edit') }}</
    content = content.replace(/>\{\{\s*t\('common.edit'\)\s*\}\}<\/Link>/g, '>\n                                <Icon name="edit" />\n                                {{ t(\'common.edit\') }}\n                            </Link>');
    content = content.replace(/>Edit<\/Link>/g, '>\n                                <Icon name="edit" />\n                                Edit\n                            </Link>');
    
    // 2. Delete buttons: >{{ t('common.delete') }}</ or >{{ t('common.remove') }}</
    content = content.replace(/>\{\{\s*t\('common.delete'\)\s*\}\}<\/button>/g, '>\n                                <Icon name="delete" />\n                                {{ t(\'common.delete\') }}\n                            </button>');
    content = content.replace(/>\{\{\s*t\('common.remove'\)\s*\}\}<\/button>/g, '>\n                                <Icon name="delete" />\n                                {{ t(\'common.remove\') }}\n                            </button>');
    // For delete buttons, update classes
    content = content.replace(/class="([^"]*btn-ghost[^"]*text-red-500[^"]*)"/g, (match, classes) => {
        let newClasses = classes.replace('hover:text-red-600', 'hover:text-red-700 hover:bg-red-50 border-red-200');
        if (!newClasses.includes('border-red-200')) {
             newClasses += ' hover:bg-red-50 hover:border-red-200';
        }
        return `class="${newClasses}"`;
    });

    // 3. View buttons: >{{ t('common.view') }}</
    content = content.replace(/>\{\{\s*t\('common.view'\)\s*\}\}<\/Link>/g, '>\n                                <Icon name="view" />\n                                {{ t(\'common.view\') }}\n                            </Link>');
    content = content.replace(/>\{\{\s*t\('common.view'\)\s*\}\}<\/a>/g, '>\n                                <Icon name="view" />\n                                {{ t(\'common.view\') }}\n                            </a>');

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
