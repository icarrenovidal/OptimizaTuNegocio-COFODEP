document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("setupForm");
  const errorDiv = document.getElementById("setupError");
  const successDiv = document.getElementById("setupSuccess");

  const fileInput = document.getElementById("logo");
  const fileUpload = document.getElementById("fileUpload");
  const previewContainer = document.getElementById("previewContainer");

  // Función para mostrar mensajes
  function showMessage(div, message) {
    div.textContent = message;
    div.classList.remove("d-none");
    setTimeout(() => div.classList.add("d-none"), 5000);
  }

  // Mostrar preview de imagen
  fileUpload.addEventListener("click", () => fileInput.click());
  fileInput.addEventListener("change", () => {
    previewContainer.innerHTML = "";
    const file = fileInput.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.classList.add("preview-image");
        previewContainer.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });

  // Validación del formulario
  function validateForm() {
    const nombre = form.nombre.value.trim();

    if (!nombre) {
      showMessage(errorDiv, "El nombre del emprendimiento es obligatorio.");
      return false;
    }

    return true;
  }

  // Envío del formulario
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    errorDiv.classList.add("d-none");
    successDiv.classList.add("d-none");

    if (!validateForm()) return;

    try {
      const formData = new FormData(form);

      // Ruta ajustada a administracion
      const response = await fetch(
        "/OptimizaTuNegocio/OptimizaTuNegocio/PHP/administracion/procesar_setup_emprendimiento.php",
        {
          method: "POST",
          body: formData,
        }
      );

      const data = await response.json();

      if (data.success) {
        showMessage(successDiv, data.message);
        form.reset();
        previewContainer.innerHTML = "";

        // Redirigir después de 2 segundos
        setTimeout(() => {
          window.location.href = "./../../Pages/administracion/home_administracion.php"; // ajusta a la ruta real de tu home
        }, 2000);
      } else {
        showMessage(errorDiv, data.message);
      }
    } catch (error) {
      console.error("Error al configurar emprendimiento:", error);
      showMessage(errorDiv, "Hubo un problema con el servidor.");
    }
  });
});
