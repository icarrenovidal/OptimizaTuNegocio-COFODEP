document.addEventListener("DOMContentLoaded", () => {
    const totalHoy = document.getElementById("total-hoy");
    const totalAyer = document.getElementById("total-ayer");
    const totalEsteMes = document.getElementById("total-este-mes");
    const totalMesAnterior = document.getElementById("total-mes-anterior");
    const tipoSelect = document.getElementById("tipo-grafico");
    const ctx = document.getElementById("grafico-ventas").getContext("2d");

    let chartVentas;

    async function loadDashboard(tipo = "dia") {
        try {
            const res = await fetch(`./../../PHP/administracion/ventas_dashboard.php?tipo=${tipo}`);
            const data = await res.json();

            // Actualizar totales
            totalHoy.textContent = `$${Number(data.totales.hoy).toLocaleString()}`;
            totalAyer.textContent = `$${Number(data.totales.ayer).toLocaleString()}`;
            totalEsteMes.textContent = `$${Number(data.totales.este_mes).toLocaleString()}`;
            totalMesAnterior.textContent = `$${Number(data.totales.mes_anterior).toLocaleString()}`;

            // Preparar datos para gráfico
            const labels = data.grafico.map(d => d.label);
            const totals = data.grafico.map(d => d.total);

            // Destruir gráfico anterior si existe
            if (chartVentas) chartVentas.destroy();

            // Elegir tipo de gráfico
            const chartType = tipo === "dia" ? "line" : "bar";

            chartVentas = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: tipo === "dia" ? "Ventas por día" : "Ventas por mes",
                        data: totals,
                        backgroundColor: "#8174A0",
                        borderColor: "#8174A0",
                        fill: tipo === "dia" ? false : true,
                        tension: tipo === "dia" ? 0.3 : 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: "index", intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return `$${Number(value).toLocaleString()}`;
                                }
                            }
                        }
                    }
                }
            });

        } catch (err) {
            console.error("Error cargando dashboard:", err);
        }
    }

    // Cambio de vista
    tipoSelect.addEventListener("change", () => {
        loadDashboard(tipoSelect.value);
    });

    // Carga inicial
    loadDashboard(tipoSelect.value);
});
