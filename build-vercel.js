import { execSync } from 'child_process';
import { readFileSync, writeFileSync, existsSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));

// Step 1: Run Vite build
console.log('Building Vue assets with Vite...');
execSync('vite build', { stdio: 'inherit' });

// Step 2: Read the manifest
const manifestPath = path.join(__dirname, 'public/build/.vite/manifest.json');
const manifestFallback = path.join(__dirname, 'public/build/manifest.json');

let manifest;
try {
    manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf-8'));
} catch {
    manifest = JSON.parse(fs.readFileSync(manifestFallback, 'utf-8'));
}

// Step 3: Get the main JS and CSS entry points
const appEntry = manifest['resources/js/app.js'] || manifest['resources/css/app.css'];
const jsFile   = manifest['resources/js/app.js']?.file;
const cssFiles = manifest['resources/js/app.js']?.css || [];
const appCss   = manifest['resources/css/app.css']?.file;

// Build link tags for CSS
let cssLinks = '';
if (appCss) {
    cssLinks += `    <link rel="stylesheet" href="/build/${appCss}">\n`;
}
cssFiles.forEach(css => {
    cssLinks += `    <link rel="stylesheet" href="/build/${css}">\n`;
});

// Step 4: Generate the static index.html
const html = `<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Inventory Kedairacing</title>
${cssLinks}    </head>
    <body>
        <div id="app"></div>
        <script type="module" src="/build/${jsFile}"></script>
    </body>
</html>
`;

const outputPath = path.join(__dirname, 'public/index.html');
fs.writeFileSync(outputPath, html);

console.log(`✅ Generated static index.html → public/index.html`);
console.log(`   JS:  /build/${jsFile}`);
console.log(`   CSS: ${cssLinks.trim()}`);
