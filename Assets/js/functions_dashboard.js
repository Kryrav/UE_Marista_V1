document.addEventListener('DOMContentLoaded', function(){
    if(typeof Chart === 'undefined' || !window.DASH) return;
    const AZUL = '#1a3b5d', DORADO = '#c4a35a', VERDE = '#27ae60', AMBAR = '#f39c12', ROJO = '#e74c3c';

    let cvB = document.querySelector('#dashMeses');
    if(cvB){
        new Chart(cvB, {
            type: 'bar',
            data: { labels: DASH.labels,
                datasets: [
                    { label: 'Cobrado', data: DASH.cobrado, backgroundColor: AZUL, borderRadius: 4 },
                    { label: 'Adeudado', data: DASH.adeudado, backgroundColor: DORADO, borderRadius: 4 }
                ] },
            options: { maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
                scales: { y: { beginAtZero: true, title: { display: true, text: 'Bs.' } } } }
        });
    }

    let cvD = document.querySelector('#dashEstado');
    if(cvD){
        new Chart(cvD, {
            type: 'doughnut',
            data: { labels: ['Cobrado', 'Adeudado vigente', 'Vencido'],
                datasets: [{ data: DASH.dona,
                    backgroundColor: [VERDE, AMBAR, ROJO], borderWidth: 2, borderColor: '#fff' }] },
            options: { maintainAspectRatio: false,
                plugins: { legend: { display: false },
                    tooltip: { callbacks: { label: function(c){ return ' ' + c.label + ': Bs. ' + c.parsed; } } } },
                cutout: '62%' }
        });
        document.querySelector('#dashLegend').innerHTML =
            '<span><i style="background:' + VERDE + '"></i>Cobrado</span>'
            + '<span><i style="background:' + AMBAR + '"></i>Adeudado vigente</span>'
            + '<span><i style="background:' + ROJO + '"></i>Vencido</span>';
    }
});
