import { beforeEach, describe, expect, it, vi } from 'vitest';

async function loadApi() {
    vi.resetModules();
    const mod = await import('../src/index');
    return mod.Io;
}

describe('Io global API for plugin scripts', () => {
    beforeEach(() => {
        document.body.innerHTML = '';
    });

    it('directive() delegates to elements added later', async () => {
        const Io = await loadApi();
        const handler = vi.fn();
        Io.directive('io-sort', 'click', handler);
        document.body.innerHTML += '<a id="s" io-sort="name">sort</a>';

        document.getElementById('s')?.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(handler).toHaveBeenCalledOnce();
        expect((handler.mock.calls[0]?.[0] as HTMLElement).getAttribute('io-sort')).toBe('name');
    });

});
