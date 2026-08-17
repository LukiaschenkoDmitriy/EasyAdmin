import { defineConfig } from 'vite';
import { resolve } from 'path';
import { existsSync } from 'node:fs';
import { globSync } from 'glob';

const tsEntries = globSync('src/EAdmin/**/*.ts');

const scssEntries = globSync('src/EAdmin/**/*.scss').filter(file => !existsSync(file.replace(/\.scss$/, '.ts')));

const entries = [...tsEntries, ...scssEntries].reduce((acc, file) => {
    const name = file.replace('src/EAdmin/', '').replace(/\.(ts|scss)$/, '');
    acc[name] = resolve(__dirname, file);
    return acc;
}, {} as Record<string, string>);

export default defineConfig({
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: entries,
        },
    },
});