document.addEventListener("DOMContentLoaded", () => {
    const emprendimientosActivosElement = document.getElementById("emprendimientos-activos");

    if (emprendimientosActivosElement) {
        fetch("./../../PHP/cofodep/emprendimientos_activos.php")
            .then(response => response.json())
            .then(data => {
                emprendimientosActivosElement.textContent = data.total ?? "0";
            })
            .catch(error => {
                console.error("Error cargando emprendimientos activos:", error);
                emprendimientosActivosElement.textContent = "Error";
            });
    }
});
