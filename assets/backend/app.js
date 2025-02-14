const isPrivateGallerySwitch = document.querySelector(
    '[data-is-private-switch]'
);

if (isPrivateGallerySwitch) {
    const privateGalleryPasswordInput = document.querySelector(
        '[data-password-input]'
    );
    const privateGalleryPasswordLabel = privateGalleryPasswordInput
        .closest('.form-group')
        .querySelector('.form-control-label');

    const setInputStatus = (isActive) => {
        if ("hasValue" in privateGalleryPasswordInput.dataset) {
            return;
        }

        if (isActive) {
            privateGalleryPasswordInput.removeAttribute('disabled');
            privateGalleryPasswordInput.setAttribute('required', '');
        } else {
            privateGalleryPasswordInput.setAttribute('disabled', '');
            privateGalleryPasswordInput.removeAttribute('required');
        }
        privateGalleryPasswordLabel.classList.toggle('required', isActive);
    };

    isPrivateGallerySwitch.addEventListener('change', (e) => {
        setInputStatus(e.target.checked);
    });
}
