const PARAMS_ATTR = 'data-plugin-params';

export function parseParams(element: HTMLElement): Record<string, unknown> {
    const params: Record<string, unknown> = {};

    const json = element.getAttribute(PARAMS_ATTR);
    if (json) {
        try {
            Object.assign(params, JSON.parse(json));
        } catch {
            console.warn(`[PluginManager] Invalid JSON in ${PARAMS_ATTR}`, element);
        }
    }

    const OPTION_PREFIX = 'pluginOption';
    for (const [key, value] of Object.entries(element.dataset)) {
        if (!key.startsWith(OPTION_PREFIX)) continue;

        const rawKey = key.slice(OPTION_PREFIX.length);
        const paramKey = rawKey.charAt(0).toLowerCase() + rawKey.slice(1);
        params[paramKey] = castValue(value);
    }

    return params;
}

function castValue(value: string | undefined): unknown {
    if (value === undefined) return undefined;
    if (value === 'true') return true;
    if (value === 'false') return false;
    if (value === 'null') return null;
    if (value !== '' && !Number.isNaN(Number(value))) return Number(value);

    try {
        return JSON.parse(value);
    } catch {
        return value;
    }
}