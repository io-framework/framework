import { delegate } from './dom';
import { serializeForm } from './form';
import { dispatchHandler } from './handlers';

/** io-onclick="name": calls the handler with the element's dataset. */
export function registerOnClick(): void {
    delegate('click', 'io-onclick', (el, event) => {
        const name = el.getAttribute('io-onclick');
        if (name) dispatchHandler(name, el.dataset, event);
    });
}

/** io-onsubmit="name": calls the handler with the serialized form; the native submit is prevented unless io-default="true". */
export function registerOnSubmit(): void {
    delegate('submit', 'io-onsubmit', (form, event) => {
        const name = form.getAttribute('io-onsubmit');
        if (!name || !(form instanceof HTMLFormElement)) return;
        if (form.getAttribute('io-default') !== 'true') event.preventDefault();
        dispatchHandler(name, serializeForm(form), event);
    });
}
