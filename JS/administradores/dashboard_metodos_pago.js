document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("grafico-metodos-pago");

    if (ctx) {
        fetch("./../../PHP/administracion/ventas_metodos.php")
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.metodo);
                const valores = data.map(item => item.cantidad);

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Métodos de pago",
                            data: valores,
                            backgroundColor: [
                                "#8174A0", // color1
                                "#A888B5", // color2
                                "#EFB6C8", // color3
                                "#E27D60", // color4
                                "#5CB8A9"  // color5
                            ],
                            borderWidth: 1,
                            borderColor: "#fff"
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom"
                            },
                            title: {
                                display: true,
                                text: "Métodos de pago más usados (mes actual)"
                            }
                        }
                    }
                });
            })
            .catch(error => console.error("Error cargando métodos de pago:", error));
    }
});
