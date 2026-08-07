import { defineConfig } from 'vite';
import { resolve } from 'path';
import { globSync } from 'glob';

const entries = globSync('src/EAdmin/**/*.ts').reduce((acc, file) => {
    const name = file.replace('src/EAdmin/', '').replace(/\.ts$/, '');
    acc[name] = resolve(__dirname, file);
    return acc;
}, {} as Record<string, string>);

export default defineConfig({
    resolve: {
        tsconfigPaths: true,
    },
    build: {
        outDir: 'public/build',
        manifest: true,
        rollupOptions: {
            input: entries,
        },
    },
});