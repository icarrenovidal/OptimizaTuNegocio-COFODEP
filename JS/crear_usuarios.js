document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("crearUsuarioForm");
    const errorDiv = document.getElementById("crearUsuarioError");
    const successDiv = document.getElementById("crearUsuarioSuccess");

    const passwordInput = form.password;
    const feedback = {
        length: document.getElementById("length"),
        number: document.getElementById("number"),
        special: document.getElementById("special")
    };

    function showMessage(div, message) {
        div.textContent = message;
        div.classList.remove("d-none");
        setTimeout(() => div.classList.add("d-none"), 5000);
    }

    function validateForm() {
        const nombre = form.nombre.value.trim();
        const apellido = form.apellido.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value.trim();
        const rol = form.rol.value;
        const estado = form.estado.value;

        // Regex para validación
        const nombreRegex = /^[A-Za-záéíóúÁÉÍÓÚ\s]+$/;
        const passwordRegex = /^(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!nombreRegex.test(nombre)) {
            showMessage(errorDiv, "Nombre inválido: solo letras y espacios permitidos.");
            return false;
        }

        if (!nombreRegex.test(apellido)) {
            showMessage(errorDiv, "Apellido inválido: solo letras y espacios permitidos.");
            return false;
        }

        if (!emailRegex.test(email)) {
            showMessage(errorDiv, "Formato de email inválido.");
            return false;
        }

        if (!passwordRegex.test(password)) {
            showMessage(errorDiv, "La contraseña debe tener mínimo 8 caracteres, al menos un número y un carácter especial.");
            return false;
        }

        if (!rol) {
            showMessage(errorDiv, "Selecciona un rol válido.");
            return false;
        }

        return true;
    }

    // Feedback visual de la contraseña
    passwordInput.addEventListener("input", () => {
        const value = passwordInput.value;

        // Longitud
        if (value.length >= 8) {
            feedback.length.classList.replace("invalid", "valid");
            feedback.length.textContent = "✔️ Mínimo 8 caracteres";
        } else {
            feedback.length.classList.replace("valid", "invalid");
            feedback.length.textContent = "❌ Mínimo 8 caracteres";
        }

        // Número
        if (/\d/.test(value)) {
            feedback.number.classList.replace("invalid", "valid");
            feedback.number.textContent = "✔️ Al menos un número";
        } else {
            feedback.number.classList.replace("valid", "invalid");
            feedback.number.textContent = "❌ Al menos un número";
        }

        // Carácter especial
        if (/[^A-Za-z0-9]/.test(value)) {
            feedback.special.classList.replace("invalid", "valid");
            feedback.special.textContent = "✔️ Al menos un carácter especial";
        } else {
            feedback.special.classList.replace("valid", "invalid");
            feedback.special.textContent = "❌ Al menos un carácter especial";
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Ocultar mensajes anteriores
        errorDiv.classList.add("d-none");
        successDiv.classList.add("d-none");

        // Validar formulario
        if (!validateForm()) return;

        try {
            const formData = new FormData(form);

            const response = await fetch("./../../PHP/cofodep/crear_usuarios.php", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                showMessage(successDiv, data.message);
                form.reset();
            } else {
                showMessage(errorDiv, data.message);
            }
        } catch (error) {
            console.error("Error al crear usuario:", error);
            showMessage(errorDiv, "Hubo un problema con el servidor.");
        }
    });
});
