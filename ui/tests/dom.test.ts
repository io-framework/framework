import { describe, expect, it, vi } from 'vitest';
import { delegate, onReady } from '../src/dom';

describe('delegate', () => {
    it('resolves the attributed ancestor of the event target, including elements added later', () => {
        const handler = vi.fn();
        delegate('click', 'io-x', handler);
        document.body.innerHTML = '<div io-x="1"><span id="inner">i</span></div>';

        document.getElementById('inner')?.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        expect(handler).toHaveBeenCalledOnce();
        expect((handler.mock.calls[0]?.[0] as HTMLElement).getAttribute('io-x')).toBe('1');
    });
});

describe('onReady', () => {
    it('runs immediately once the document is parsed', () => {
        const fn = vi.fn();
        onReady(fn);
        expect(fn).toHaveBeenCalledOnce();
    });
});
