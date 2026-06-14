const setSubmitLoading = (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || form.dataset.noLoading === 'true') {
        return;
    }

    const submitter = event.submitter instanceof HTMLElement
        ? event.submitter
        : form.querySelector('button[type="submit"], input[type="submit"]');

    if (!submitter || submitter.dataset.loading === 'true') {
        return;
    }

    submitter.dataset.loading = 'true';
    submitter.setAttribute('aria-busy', 'true');

    if ('disabled' in submitter) {
        submitter.disabled = true;
    }

    if (submitter.tagName === 'BUTTON') {
        submitter.dataset.originalHtml = submitter.innerHTML;
        submitter.classList.add('loading');

        const label = submitter.dataset.loadingLabel;

        if (label) {
            submitter.textContent = label;
        }
    }

    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        if (button !== submitter && 'disabled' in button) {
            button.disabled = true;
        }
    });
};

const resetSubmitLoading = () => {
    document.querySelectorAll('[data-loading="true"]').forEach((button) => {
        button.dataset.loading = 'false';
        button.removeAttribute('aria-busy');

        if ('disabled' in button) {
            button.disabled = false;
        }

        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
            delete button.dataset.originalHtml;
        }

        button.classList.remove('loading');
    });
};

document.addEventListener('submit', setSubmitLoading, true);
window.addEventListener('pageshow', resetSubmitLoading);
document.addEventListener('livewire:navigated', resetSubmitLoading);
