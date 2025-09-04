document.addEventListener("DOMContentLoaded", () => {
    const ctxTop = document.getElementById("grafico-top-ventas").getContext("2d");
    let chartTopVentas;

    async function loadTopVentas() {
        try {
            const res = await fetch("./../../PHP/administracion/ventas_top.php");
            const data = await res.json();

            const labels = data.map(d => d.producto);
            const totals = data.map(d => d.cantidad);

            if (chartTopVentas) chartTopVentas.destroy();

            chartTopVentas = new Chart(ctxTop, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Top 5 productos más vendidos",
                        data: totals,
                        backgroundColor: [
                            "#8174A0",
                            "#A888B5",
                            "#EFB6C8",
                            "#E27D60",
                            "#5CB8A9"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom"
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || "";
                                    const value = context.raw || 0;
                                    return `${label}: ${value} ventas`;
                                }
                            }
                        }
                    }
                }
            });

        } catch (err) {
            console.error("Error cargando top ventas:", err);
        }
    }

    loadTopVentas();
});
