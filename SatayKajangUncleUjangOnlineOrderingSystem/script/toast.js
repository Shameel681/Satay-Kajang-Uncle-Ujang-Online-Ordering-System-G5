function showToast(message, type = "success") {
    const container = document.getElementById("toast-container") || (() => {
        const c = document.createElement("div");
        c.id = "toast-container";
        document.body.appendChild(c);
        return c;
    })();

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span>${message}</span>
        <button class="toast-close">&times;</button>
    `;
    container.appendChild(toast);

    // auto close
    setTimeout(() => {
        toast.classList.add("hide");
        setTimeout(() => toast.remove(), 400);
    }, 4000);

    // manual close
    toast.querySelector(".toast-close").onclick = () => {
        toast.classList.add("hide");
        setTimeout(() => toast.remove(), 400);
    };
}



