import { Plugin } from "./Plugin"
import { PluginManager } from "./PluginManager"
import { RegisterPlugin } from './Registry';

declare global {
    interface Window {
        eadmin: {
            Plugin: typeof Plugin,
            PluginManager: PluginManager,
            RegisterPlugin: typeof RegisterPlugin
        }
    }
}

window.eadmin.Plugin = Plugin;
window.eadmin.PluginManager = new PluginManager();
window.eadmin.RegisterPlugin = RegisterPlugin