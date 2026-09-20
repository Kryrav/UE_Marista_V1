let repCharts = {};
const REP_C = { azul: '#1a3b5d', dorado: '#c4a35a', verde: '#27ae60', ambar: '#f39c12', rojo: '#e74c3c', celeste: '#2980b9' };

document.addEventListener('DOMContentLoaded', function(){
    cargarResumenFin();
    cargarAnual();
    cargarEstudiantes();
    // Carga ansiosa: los datos son pequeños y así no dependemos del evento de pestaña
    cargarMensual();
    cargarCurso();
    cargarCajero();
    cargarMatricula();
    document.querySelector('#repGestion').onchange = function(){
        cargarResumenFin(); cargarMensual(); cargarCurso(); cargarCajero(); cargarMatricula();
        document.querySelectorAll('.gestion-lbl').forEach(function(e){ e.innerHTML = 'gestión ' + gestionSel(); });
    };
    document.querySelectorAll('.gestion-lbl').forEach(function(e){ e.innerHTML = 'gestión ' + gestionSel(); });
    // OJO: los eventos de Bootstrap 4 (shown.bs.tab) solo llegan vía jQuery, no con addEventListener nativo
    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
        document.querySelector('#printTitulo').innerHTML = e.target.textContent.trim();
    });
    document.querySelector('#printTitulo').innerHTML = 'Resumen financiero';
}, false);

function gestionSel(){ return document.querySelector('#repGestion').value; }
function fmtBs(n){ return 'Bs. ' + parseFloat(n || 0).toFixed(2); }

function getJSON(url, cb){
    let r = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    r.open("GET", base_url + url, true);
    r.send();
    r.onreadystatechange = function(){
        if(r.readyState == 4){
            if(r.status == 200){
                try{
                    let o = JSON.parse(r.responseText);
                    if(o.status){ cb(o.data); return; }
                }catch(e){}
                swal("Error", "Respuesta inválida del reporte (" + url + ").", "error");
            }else{
                swal("Error", "No se pudo cargar el reporte (HTTP " + r.status + ").", "error");
            }
        }
    };
}

function tabla(id, rows, cols, order){
    let datos = rows.map(function(r){ return cols.map(function(c){ return r[c] !== null && r[c] !== undefined ? r[c] : ''; }); });
    let colDefs = cols.map(function(){ return { orderable: true }; });
    if($.fn.DataTable.isDataTable(id)){ $(id).DataTable().destroy(); }
    $(id).DataTable({
        data: datos, columns: colDefs, processing: true, serverSide: false, destroy: true,
        language: { url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" },
        dom: 'lBfrtip',
        buttons: [
            {"extend": "excelHtml5", "text": "<i class='fas fa-file-excel'></i> Excel", "className": "btn btn-success btn-sm"},
            {"extend": "pdfHtml5", "text": "<i class='fas fa-file-pdf'></i> PDF", "className": "btn btn-danger btn-sm", "orientation": "landscape"}
        ],
        iDisplayLength: 12, order: order || [[0, "asc"]]
    });
}

// ---- Resumen financiero ----
function cargarResumenFin(){
    getJSON('/Reportes/resumenFinanciero/' + gestionSel(), function(d){
        document.querySelector('#repResumenKpis').innerHTML =
            kpi('Matrículas', d.matriculas, REP_C.azul)
            + kpi('Cuotas', d.cuotas, REP_C.celeste)
            + kpi('Cobrado<br>' + fmtBs(d.cobrado), d.pagadas + ' pagadas', REP_C.verde)
            + kpi('Adeudado<br>' + fmtBs(d.adeudado), d.pendientes + ' pendientes', REP_C.ambar)
            + kpi('Vencido<br>' + fmtBs(d.vencido), d.vencidas + ' cuotas', REP_C.rojo)
            + kpi('% cobro', d.pct_cobro + '%', REP_C.dorado);
        miniDona(d);
    });
}

function kpi(titulo, valor, color){
    return '<div class="col-md-2 col-sm-4 col-6 mb-2"><div class="tile text-center" style="border-top:3px solid ' + color + ';">'
        + '<small class="text-muted">' + titulo + '</small><h4 style="color:' + color + ';">' + valor + '</h4></div></div>';
}

function miniDona(d){
    if(typeof Chart === 'undefined') return;
    let cv = document.querySelector('#repChartAnualMini');
    if(!cv) return;
    if(repCharts.mini){ repCharts.mini.destroy(); }
    repCharts.mini = new Chart(cv, { type: 'doughnut',
        data: { labels: ['Cobrado', 'Adeudado'], datasets: [{ data: [parseFloat(d.cobrado), parseFloat(d.adeudado)], backgroundColor: [REP_C.verde, REP_C.ambar], borderWidth: 2 }] },
        options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }, cutout: '60%' } });
}

// ---- Mensual ----
function cargarMensual(){
    getJSON('/Reportes/mensual/' + gestionSel(), function(rows){
        tabla('#tblMensual', rows.map(function(r){
            return { mes: r.mes, cuotas: r.cuotas, pagadas: r.pagadas, cobrado: fmtBs(r.cobrado), adeudado: fmtBs(r.adeudado), vencido: fmtBs(r.vencido) };
        }), ["mes", "cuotas", "pagadas", "cobrado", "adeudado", "vencido"]);
    });
}

// ---- Anual ----
function cargarAnual(){
    getJSON('/Reportes/anual', function(rows){
        tabla('#tblAnual', rows.map(function(r){
            let tot = parseFloat(r.cobrado) + parseFloat(r.adeudado);
            let pct = tot > 0 ? (100 * parseFloat(r.cobrado) / tot).toFixed(1) + '%' : '—';
            return { gestion: r.gestion, matriculas: r.matriculas, cobrado: fmtBs(r.cobrado), adeudado: fmtBs(r.adeudado), vencido: fmtBs(r.vencido), pct: pct };
        }), ["gestion", "matriculas", "cobrado", "adeudado", "vencido", "pct"]);
        if(typeof Chart !== 'undefined'){
            let cv = document.querySelector('#repChartAnual');
            if(repCharts.anual){ repCharts.anual.destroy(); }
            repCharts.anual = new Chart(cv, { type: 'bar',
                data: { labels: rows.map(function(r){ return r.gestion; }),
                    datasets: [
                        { label: 'Cobrado', data: rows.map(function(r){ return r.cobrado; }), backgroundColor: REP_C.azul, borderRadius: 4 },
                        { label: 'Adeudado', data: rows.map(function(r){ return r.adeudado; }), backgroundColor: REP_C.dorado, borderRadius: 4 }
                    ] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }, scales: { y: { beginAtZero: true } } } });
        }
    });
}

// ---- Por curso / cajero ----
function cargarCurso(){
    getJSON('/Reportes/porCurso/' + gestionSel(), function(rows){
        tabla('#tblCurso', rows.map(function(r){
            return { curso: r.curso, tutor: r.tutor || '—', matriculas: r.matriculas, cobrado: fmtBs(r.cobrado), adeudado: fmtBs(r.adeudado) };
        }), ["curso", "tutor", "matriculas", "cobrado", "adeudado"]);
    });
}

function cargarCajero(){
    getJSON('/Reportes/porCajero/' + gestionSel(), function(rows){
        tabla('#tblCajero', rows.map(function(r){
            return { cajero: r.cajero || '(sin registro)', cobros: r.cobros, total: fmtBs(r.total) };
        }), ["cajero", "cobros", "total"], [[2, "desc"]]);
    });
}

// ---- Matrícula ----
function cargarMatricula(){
    getJSON('/Reportes/matriculaStats/' + gestionSel(), function(d){
        tabla('#tblMatCur', d.por_paralelo.map(function(r){
            let pct = r.cupo > 0 ? Math.min(100, Math.round(100 * r.inscritos / r.cupo)) + '%' : '—';
            return { curso: r.curso, cupo: r.cupo, inscritos: r.inscritos, pct: pct };
        }), ["curso", "cupo", "inscritos", "pct"]);
        let h = '<table class="table table-sm table-bordered"><tbody>';
        d.por_tipo.forEach(function(r){ h += '<tr><td>' + r.tipo + '</td><td class="text-right">' + r.c + '</td></tr>'; });
        d.por_sexo.forEach(function(r){ h += '<tr><td>Sexo ' + r.sexo + '</td><td class="text-right">' + r.c + '</td></tr>'; });
        document.querySelector('#matTipoSexo').innerHTML = h + '</tbody></table>';
    });
}

// ---- Estudiantes ----
function cargarEstudiantes(){
    getJSON('/Reportes/estudiantesStats', function(d){
        let h = '<table class="table table-sm table-bordered"><tbody>';
        d.por_estado.forEach(function(r){
            let lbl = r.status == 1 ? 'Activos' : (r.status == 2 ? 'Inactivos' : 'Eliminados');
            h += '<tr><td>' + lbl + '</td><td class="text-right">' + r.c + '</td></tr>';
        });
        d.por_registro.forEach(function(r){ h += '<tr><td>' + r.estado_reg + '</td><td class="text-right">' + r.c + '</td></tr>'; });
        document.querySelector('#estEstados').innerHTML = h + '</tbody></table>';
        if(typeof Chart !== 'undefined'){
            let cv = document.querySelector('#repChartSexo');
            if(repCharts.sexo){ repCharts.sexo.destroy(); }
            repCharts.sexo = new Chart(cv, { type: 'doughnut',
                data: { labels: d.por_sexo.map(function(r){ return r.sexo == 'M' ? 'Masculino' : 'Femenino'; }),
                    datasets: [{ data: d.por_sexo.map(function(r){ return r.c; }), backgroundColor: [REP_C.azul, REP_C.dorado], borderWidth: 2 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }, cutout: '60%' } });
        }
        document.querySelector('#estSinTutN').innerHTML = d.sin_tutores.length;
        tabla('#tblSinTut', d.sin_tutores.map(function(r){ return { estudiante: r.estudiante, ci: r.ci }; }), ["estudiante", "ci"]);
        document.querySelector('#estSinFolio').innerHTML = d.sin_folio;
    });
}
