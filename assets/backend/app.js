const isPrivateGallerySwitch = document.querySelector(
    "[data-is-private-switch]"
);
if (isPrivateGallerySwitch) {
    const privateGalleryPassword = document.querySelector(
        "[data-password-input]"
    );

    const setInputStatus = (isActive) => {
        if (isActive) {
            privateGalleryPassword.removeAttribute("disabled");
        } else {
            privateGalleryPassword.setAttribute("disabled", "");
        }
    };
    // console.log(isPrivateGallerySwitch.checked)
    // setInputStatus(isPrivateGallerySwitch.checked);
    isPrivateGallerySwitch.addEventListener("change", (e) => {
        setInputStatus(e.target.checked);
    });
}
