export abstract class Plugin<TParams extends object = Record<string, unknown>> {
    protected readonly element: HTMLElement;

    protected readonly params: TParams;

    public constructor(element: HTMLElement, params: TParams) {
        this.element = element;
        this.params = params;
    }

    public abstract init(): void;

    public destroy(): void { }

    public getElement(): HTMLElement {
        return this.element;
    }

    public getParams(): Readonly<TParams> {
        return this.params;
    }
}