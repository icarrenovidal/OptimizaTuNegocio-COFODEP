document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("grafico-ganancias");

    if (ctx) {
        fetch("./../../PHP/administracion/ventas_ganancias.php")
            .then(res => res.json())
            .then(data => {
                if (!data || data.error) {
                    console.error("Error al cargar ganancias:", data?.error);
                    return;
                }

                const labels = data.map(item => item.producto);
                const valores = data.map(item => item.ganancia);

                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Ganancia neta ($)",
                            data: valores,
                            backgroundColor: ["#8174A0", "#A888B5", "#EFB6C8", "#E27D60", "#5CB8A9"],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        indexAxis: "y",
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: "Top 5 productos por ganancia neta (mes actual)" }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { callback: value => "$" + value } }
                        }
                    }
                });
            })
            .catch(err => console.error("Error cargando ganancias:", err));
    }
});
