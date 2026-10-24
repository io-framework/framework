/**
 * io.js: Io's client half. One dependency-free script that
 * enhances plain HTML through io-* attributes; nothing to mount or hydrate.
 * Served by the framework itself (View::ioJsTag()), which also passes
 * non-default CSRF names as data attributes on the <script> tag.
 */

import { installCsrfFetch, readCsrfToken } from './csrf';
import { registerOnClick, registerOnSubmit } from './directives';
import { delegate, onReady } from './dom';
import { dispatchHandler, registerHandler } from './handlers';

const script = document.currentScript as HTMLScriptElement | null;
const csrfCookie = script?.dataset.csrfCookie ?? 'csrf_token';
const csrfHeader = script?.dataset.csrfHeader ?? 'X-CSRF-Token';

installCsrfFetch(csrfCookie, csrfHeader);

/**
 * The public API, also what a plugin script (shipped via a server-side
 * ClientAsset hook, loaded after this file) builds its own io-* directives on.
 */
export const Io = {
    handlers: { register: registerHandler, dispatch: dispatchHandler },
    csrf: { token: () => readCsrfToken(csrfCookie) },

    /** A delegated io-* directive: `handler` runs for `eventType` on any element carrying `attribute`, now or added later. */
    directive: (attribute: string, eventType: string, handler: (el: HTMLElement, event: Event) => void, capture = false): void =>
        delegate(eventType, attribute, handler, capture),
};

declare global {
    interface Window {
        Io: typeof Io;
    }
}

window.Io = Io;
onReady(() => {
    registerOnClick();
    registerOnSubmit();
});
