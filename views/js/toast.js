const toastDetails = {
    timer: 5000,
    success: {
        icon: "fa-circle-check",
        text: "Hello World: This is a success toast."
    },
    error: {
        icon: "fa-circle-xmark",
        text: "Hello World: This is an error toast."
    },
    warning: {
        icon: "fa-triangle-exclamation",
        text: "Hello World: This is a warning toast."
    },
    info: {
        icon: "fa-circle-info",
        text: "Hello World: This is an information toast."
    },
    random: {
        icon: "fa-solid fa-star",
        text: "Hello World: This is a random toast."
    }
};

const removeToast = (toast) => {
    $(toast).modal("hide");
};

const createToast = (title, message, type = "success", icon = "fa-info", delay = 3000) => {
    const page_body = document.querySelector("body");
    const toast = document.createElement("div");
    toast.className = "modal toast";
    toast.setAttribute("tabindex", "-1");
    toast.setAttribute("role", "dialog");
    toast.setAttribute("aria-hidden", "true");

    switch (type) {
        case "success":
            toast.style.background = "var(--success) !important";
            toast.style.color = "var(--light) !important";
            break;
        case "error":
            toast.style.background = "var(--danger) !important";
            toast.style.color = "var(--light) !important";
            break;
        case "warning":
            toast.style.background = "var(--warning) !important";
            toast.style.color = "var(--gray-dark) !important";
            break;
        default:
            toast.style.background = "var(--info) !important";
            toast.style.color = "var(--light) !important";
            break;
    }

    const header = document.createElement("div");
    header.className = "modal-header";

    const iconElement = document.createElement("i");
    iconElement.className = `fa-solid ${icon}`;

    const titleElement = document.createElement("span");
    titleElement.textContent = title;

    const closeButton = document.createElement("i");
    closeButton.className = "fa-solid fa-xmark";
    closeButton.style.cursor = "pointer";
    closeButton.onclick = () => removeToast(toast);

    header.appendChild(iconElement);
    header.appendChild(titleElement);
    header.appendChild(closeButton);

    const body = document.createElement("div");
    body.className = "modal-body";

    const messageElement = document.createElement("span");
    messageElement.textContent = message;

    body.appendChild(messageElement);
    toast.appendChild(header);
    toast.appendChild(body);
    page_body.appendChild(toast);
    $(toast).modal("show");
    $(toast).on("hidden.bs.modal", () => {
        toast.remove();
    });
    toast.timeoutId = setTimeout(() => removeToast(toast), delay);
};
