import { Plugin } from './Plugin';

export type PluginConstructor = new (element: HTMLElement, params: any) => Plugin<any>;

const registry = new Map<string, PluginConstructor>();

export function registerPlugin(name: string, ctor: PluginConstructor): void {
    if (registry.has(name)) {
        console.warn(`[PluginManager] Plugin "${name}" already registered.`);
    }
    registry.set(name, ctor);
}

export function getPlugin(name: string): PluginConstructor | undefined {
    return registry.get(name);
}

export function getRegisteredPluginNames(): string[] {
    return Array.from(registry.keys());
}

export function RegisterPlugin(name: string) {
    return function <T extends PluginConstructor>(target: T): T {
        registerPlugin(name, target);
        return target;
    };
}