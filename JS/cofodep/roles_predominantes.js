document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("grafico-roles");

    if (ctx) {
        fetch("./../../PHP/cofodep/roles_predominantes.php")
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.rol);
                const valores = data.map(d => Number(d.cantidad));

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Roles de usuario",
                            data: valores,
                            backgroundColor: [
                                "#8174A0", // emprendedor
                                "#A888B5", // mayorista
                                "#EFB6C8", // administrador
                                "#E27D60"  // cofodep
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: "bottom" },
                            title: {
                                display: true,
                                text: "Distribución de roles"
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Error cargando roles:", err));
    }
});
