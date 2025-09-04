document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("grafico-usuarios-evolucion");

    if (ctx) {
        fetch("./../../PHP/cofodep/usuarios_evolucion.php")
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.mes);
                const valores = data.map(d => Number(d.cantidad));

                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Usuarios nuevos",
                            data: valores,
                            borderColor: "#8174A0",
                            backgroundColor: "rgba(129, 116, 160, 0.2)",
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: "top" },
                            title: {
                                display: true,
                                text: "Evolución de usuarios nuevos"
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision:0
                                }
                            }
                        }
                    }
                });
            })
            .catch(err => console.error("Error cargando evolución de usuarios:", err));
    }
});
