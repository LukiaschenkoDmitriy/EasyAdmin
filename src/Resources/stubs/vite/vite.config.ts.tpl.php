import { defineConfig } from 'vite';
import { resolve } from 'path';
import { existsSync } from 'node:fs';
import { globSync } from 'glob';

const tsEntries = globSync('src/EAdmin/**/*.ts');
const scssEntries = globSync('src/EAdmin/**/*.scss').filter(file => !existsSync(file.replace(/\.scss$/, '.ts')));

const coreEnties = globSync('vendor/easy-admin/core/src/TS/*.ts'); 

const entries = [...tsEntries, ...scssEntries, ...coreEnties].reduce((acc, file) => {
    const name = file.replace('src/EAdmin/', '').replace(/\.(ts|scss)$/, '');
    acc[name] = resolve(__dirname, file);
    return acc;
}, {} as Record<string, string>);

export default defineConfig({
    base: '/build/',
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: entries,
        },
    },
});