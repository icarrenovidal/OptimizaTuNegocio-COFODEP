document.addEventListener("DOMContentLoaded", () => {
    const usuariosActivosElement = document.getElementById("usuarios-activos");

    if (usuariosActivosElement) {
        fetch("./../../PHP/cofodep/usuarios_activos.php")
            .then(response => response.json())
            .then(data => {
                usuariosActivosElement.textContent = data.total ?? "0";
            })
            .catch(error => {
                console.error("Error cargando usuarios activos:", error);
                usuariosActivosElement.textContent = "Error";
            });
    }
});
