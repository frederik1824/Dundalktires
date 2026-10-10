const fs = require('fs');
let file = 'resources/js/Pages/POS/Index.vue';
let content = fs.readFileSync(file, 'utf8');

// Replace card classes
content = content.replace(/class="card space-y-3"/g, 'class="card space-y-4"');
content = content.replace(/class="card space-y-2"/g, 'class="card space-y-4"');

// Fix buttons inside item search
content = content.replace(/<button type="button" @click="searchType='service'; itemResults=\[\]"/, '<button type="button" @click="searchType=\'service\'; itemResults=[]"');
content = content.replace(/<button type="button" @click="searchType='product'; itemResults=\[\]"/, '<button type="button" @click="searchType=\'product\'; itemResults=[]"');

fs.writeFileSync(file, content);
