document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#movimientosStockTable tbody');

    fetch('./../../PHP/administracion/obtener_movimientos_stock.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert('Error cargando movimientos: ' + data.error);
                return;
            }

            data.forEach(mov => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${mov.id_movimiento}</td>
                    <td>${mov.nombre_producto || '-'}</td>
                    <td>${mov.codigo_lote || '-'}</td>
                    <td>${mov.tipo}</td>
                    <td>${mov.cantidad}</td>
                    <td>${mov.fecha}</td>
                    <td>${mov.origen}</td>
                    <td>${mov.observacion}</td>
                `;
                tableBody.appendChild(tr);
            });

            // Inicializar DataTable
            $('#movimientosStockTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                order: [[5, 'desc']]
            });
        })
        .catch(err => {
            console.error('Error al obtener movimientos:', err);
            alert('No se pudo cargar los movimientos de stock.');
        });
});
