const fs = require('fs');
const Prism = require('prismjs');
const loadLanguages = require('prismjs/components/');
loadLanguages(['php']);

const source = fs.readFileSync('code.php', 'utf8');
const lines = source.split(/\n/);
const code = lines.slice(1).join('\n');
const html = Prism.highlight(code, Prism.languages.php, 'php');

console.log(html);
