document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const errorDiv = document.getElementById("loginError");
  const errorMessage = document.getElementById("loginErrorMessage");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");

  function showError(message) {
    errorMessage.textContent = message;
    errorDiv.style.display = "flex"; // flex para alinear icono y texto
    setTimeout(() => errorDiv.classList.add("show"), 10);

    // Shake del formulario
    loginForm.classList.remove("shake");
    void loginForm.offsetWidth;
    loginForm.classList.add("shake");

    // Ocultar automáticamente después de 5 segundos
    setTimeout(hideError, 5000);
  }

  function hideError() {
    errorDiv.classList.remove("show");
    setTimeout(() => {
      errorDiv.style.display = "none";
    }, 300);
  }

  [emailInput, passwordInput].forEach(input => {
    input.addEventListener("input", hideError);
  });

  loginForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const response = await fetch("../PHP/procesar_login.php", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        window.location.href = data.redirect;
      } else {
        showError(data.message);
      }
    } catch (error) {
      console.error("Error en login:", error);
      showError("Hubo un problema con el servidor. Intenta nuevamente.");
    }
  });
});
