document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("grafico-emprendimientos-evolucion");

    if (ctx) {
        fetch("./../../PHP/cofodep/emprendimientos_evolucion.php")
            .then(res => res.json())
            .then(data => {
                const labels = data.map(d => d.mes);
                const valores = data.map(d => Number(d.cantidad));

                new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Emprendimientos nuevos",
                            data: valores,
                            borderColor: "#A888B5",
                            backgroundColor: "rgba(168, 136, 181, 0.2)",
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
                                text: "Evolución de emprendimientos"
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
            .catch(err => console.error("Error cargando evolución de emprendimientos:", err));
    }
});
