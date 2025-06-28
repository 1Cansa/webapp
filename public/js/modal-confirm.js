document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('customConfirmModal');
    const messageBox = document.getElementById('confirmMessage');
    const yesBtn = document.getElementById('confirmYes');
    const noBtn = document.getElementById('confirmNo');
    let confirmCallback = null;

    function showCustomConfirm(message, callback) {
        messageBox.textContent = message;
        modal.style.display = 'flex';
        confirmCallback = callback;
    }

    yesBtn.onclick = function () {
        modal.style.display = 'none';
        if (confirmCallback) confirmCallback(true);
    };

    noBtn.onclick = function () {
        modal.style.display = 'none';
        if (confirmCallback) confirmCallback(false);
    };

    document.querySelectorAll('a.confirm-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const message = this.dataset.confirm || 'Are you sure?';
            showCustomConfirm(message, (confirmed) => {
                if (confirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});
