import { Plugin } from './Plugin';
import { getPlugin } from './Registry';
import { parseParams } from './Params';

const PLUGIN_ATTR = 'data-plugin';
const MOUNTED_ATTR = 'data-plugin-mounted';

interface MountedInstance {
    element: HTMLElement;
    pluginName: string;
    instance: Plugin;
}

export class PluginManager {
    private static mounted: MountedInstance[] = [];
    private static observer: MutationObserver | null = null;

    public static init(root: HTMLElement = document.body): void {
        const start = () => {
            this.scan(root);
            this.observe(root);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', start, { once: true });
        } else {
            start();
        }
    }

    public static scan(root: ParentNode = document): void {
        root.querySelectorAll<HTMLElement>(`[${PLUGIN_ATTR}]`).forEach((el) => this.mount(el));
    }

    public static mount(element: HTMLElement): void {
        if (element.getAttribute(MOUNTED_ATTR) === '1') return;

        const names = (element.getAttribute(PLUGIN_ATTR) ?? '')
            .split(',')
            .map((name) => name.trim())
            .filter(Boolean);

        if (names.length === 0) return;

        const params = parseParams(element);

        for (const name of names) {
            const Ctor = getPlugin(name);

            if (!Ctor) {
                console.warn(`[PluginManager] Plugin "${name}" not registered`, element);
                continue;
            }

            try {
                const instance = new Ctor(element, params);
                instance.init();
                this.mounted.push({ element, pluginName: name, instance });
            } catch (error) {
                console.error(`[PluginManager] Init plugin error "${name}"`, error);
            }
        }

        element.setAttribute(MOUNTED_ATTR, '1');
    }

    public static unmount(element: HTMLElement): void {
        const remaining: MountedInstance[] = [];

        for (const entry of this.mounted) {
            if (entry.element === element) {
                try {
                    entry.instance.destroy();
                } catch (error) {
                    console.error(`[PluginManager] Error destroy() "${entry.pluginName}"`, error);
                }
            } else {
                remaining.push(entry);
            }
        }

        this.mounted = remaining;
        element.removeAttribute(MOUNTED_ATTR);
    }

    public static getInstances(name: string): Plugin[] {
        return this.mounted.filter((entry) => entry.pluginName === name).map((entry) => entry.instance);
    }

    private static observe(root: HTMLElement): void {
        if (this.observer) return;

        this.observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                mutation.addedNodes.forEach((node) => this.handleAddedNode(node));
                mutation.removedNodes.forEach((node) => this.handleRemovedNode(node));
            }
        });

        this.observer.observe(root, { childList: true, subtree: true });
    }

    private static handleAddedNode(node: Node): void {
        if (!(node instanceof HTMLElement)) return;

        if (node.hasAttribute(PLUGIN_ATTR)) this.mount(node);
        node.querySelectorAll<HTMLElement>(`[${PLUGIN_ATTR}]`).forEach((el) => this.mount(el));
    }

    private static handleRemovedNode(node: Node): void {
        if (!(node instanceof HTMLElement)) return;

        if (node.hasAttribute(PLUGIN_ATTR)) this.unmount(node);
        node.querySelectorAll<HTMLElement>(`[${PLUGIN_ATTR}]`).forEach((el) => this.unmount(el));
    }

    public static disconnect(): void {
        this.observer?.disconnect();
        this.observer = null;
    }
}