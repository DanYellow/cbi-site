const loggedDialog = document.querySelector('[data-logged-dialog]');

loggedDialog.showModal();
document
    .querySelector('[data-open-logged-dialog]')
    .addEventListener('click', (e) => {
        loggedDialog.showModal();
    });

document.querySelectorAll('[data-close-dialog]').forEach((item) => {
    item.addEventListener('click', (e) => {
        e.target.closest('dialog').close();
    });
});
