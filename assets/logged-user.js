const loggedDialog = document.querySelector("[data-logged-dialog]");

loggedDialog.showModal();
document.querySelector("[data-open-logged-dialog]").addEventListener("click", (e) => {
    loggedDialog.showModal();
})
