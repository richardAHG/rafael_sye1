var tabla;

//Función que se ejecuta al inicio
function init() {
  listar();
  $("#fecha_inicio").change(listar_);
  $("#fecha_fin").change(listar_);
  $("#mConsultaC").addClass("treeview active");
  $("#lConsulasC").addClass("active");
}

function createBtnReporteCumplimiento(fechaInicio, fechaFin) {
    deleteBtnReporte();
    elemento = document.createElement('a');
    var divreporteCumplimeinto = document.getElementById('divreporteCumplimiento');
    divreporteCumplimeinto.appendChild(elemento);
    elemento.textContent = 'Reporte Cumplimiento';
    elemento.className = "btn btn-primary btn-sm"
    elemento.setAttribute('href', `../ajax/consultas.php?op=rptCumplimiento&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
}

function deleteBtnReporte() {
    var divreporteCumplimeinto = document.getElementById('divreporteCumplimiento');
    divreporteCumplimeinto.innerHTML = '';
}

function listar_() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  createBtnReporteCumplimiento(fecha_inicio, fecha_fin);
  $.ajax({
    method: "POST",
    dataType: "json",
    url: "../ajax/consultas.php?op=atsfecha",
    data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
    success: function (response) {
      $("#consulta").modal("hide");
      $("#tbllistado").bootstrapTable("load", response);
    },
  });
}
//Función Listar
function listar() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  let url = `../ajax/consultas.php?op=atsfecha&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}`;

  $("#tbllistado")
    .bootstrapTable({
      url: url,
      search: true,
      exportTypes: ["json", "csv", "txt", "excel"],
      columns: [
        {
          title: "Opciones",
          formatter(value, row, index, field) {
            
             let btn = [`<a href="../reportes/rptConsultaAts.php?id=${row.id}" target="_blank" class="btn btn-info btn-xs mostrar"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>`];
            return btn.join(" ");
          },
          //events: {
            //"click .mostrar": (e, value, row, index) => {
            //  mostrar(row.id);
            //},
          //},
        },
        { field: "empleado", title: "Empleado" },
        { field: "numero_documento", title: "# Documento" },
        {
          //field: "estado",
          title: "Firma",
          formatter(value, row, index, field) {
            let span = `<img src="${row.firma_ruta}"  height='50px' width='50px' ></img>`;
            return span;
          },
        },
        { field: "fecha_creacion", title: "Fecha Inicio" },
        { field: "hora_fin", title: "Hora Final" },
        
        // {
        //   //field: "estado",
        //   title: "Estado",
        //   formatter(value, row, index, field) {
        //     let span = `<span class="label bg-red">Desactivado</span>`;
        //     if (row.estado == 1) {
        //       span = '<span class="label bg-green">Activado</span>';
        //     }
        //     return span;
        //   },
        // },
      ],
    })
    .trigger("change");
}

init();
