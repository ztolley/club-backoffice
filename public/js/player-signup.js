function sendHeightToParent() {
    const height = document.body.scrollHeight + 50;
    window.parent.postMessage({
        iframeHeight: height
    }, "*");
}

/**
 * This function is called when the form is submitted.
 * It checks the form contents against simple simple rules (mostly around the associated contact
 * to ensure if a contact name is given then there is ether an email or phone provided for it.
 * @returns {boolean} - Returns true to allow the form to be submitted.
 */
function submitForm() {
    try {
        // Clear any form errors displayed
        document.querySelectorAll('.forms-error').forEach((error) => {
            error.innerText = '';
            error.setAttribute('aria-hidden', 'true');
        });

        // Check if the form is valid

        // Ensure that there is at least one contact provided.
        const primaryContactNameInput = document.querySelector('input[name="contacts[0][name]"]');
        const primaryContactNameValue = primaryContactNameInput.value.trim();

        if (primaryContactNameValue === '') {
            const primaryContactNameError = document.querySelector('#contacts-0-name-error');
            primaryContactNameError.innerText = 'Please provide at least one contact name.';
            primaryContactNameError.setAttribute('aria-hidden', 'false');
            primaryContactNameInput.focus();
            return false;
        }

        // Get the form contact names and check each one has at least an email or phone provided.
        const contactNameInputs = document
            .querySelectorAll('input[name^="contacts"][name$="[name]"]')

        for (let index = 0; index < contactNameInputs.length; index++) {
            const contactName = contactNameInputs[index];
            const contactNameValue = contactName.value.trim();
            const contactEmail = contactName.closest('.contact-section').querySelector(
                'input[name$="[email]"]');
            const contactPhone = contactName.closest('.contact-section').querySelector(
                'input[name$="[phone]"]');

            // If the contact name is not empty, check if email or phone is provided
            if (contactNameValue !== '' && !contactEmail.value.trim() && !contactPhone.value.trim()) {
                const contactError = document.querySelector(`#contacts-${index}-name-error`);
                contactError.innerText = 'Please provide either an email address or phone number.';
                contactError.setAttribute('aria-hidden', 'false');
                contactEmail.focus();
                return false;
            }
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        return false;
    }

    const submitButton = document.querySelector('#forms-submit');
    submitButton.setAttribute('disabled', 'disabled');
    submitButton.innerText = 'Sending...';
    return true;
}

window.addEventListener("load", sendHeightToParent);
window.addEventListener("resize", sendHeightToParent);

const form = document.querySelector('#player-signup-form');
if (form) {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        if (submitForm()) {
            form.submit();
        }
    });
}
