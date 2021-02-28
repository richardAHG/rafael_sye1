var tabla;

//Función que se ejecuta al inicio
function init() {
    listar();
    //Cargamos los items al select cliente

    $.post("../ajax/ats.php?op=participanteATS", function(r) {
        data = r;
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("idParticipante").appendChild(option);
        });
        $("#idParticipante").selectpicker("refresh");
    });

    $("#fecha_inicio").change(listar_);
    $("#fecha_fin").change(listar_);
    $("#mConsultaC").addClass("treeview active");
    $("#lConsulasP").addClass("active");
}

function listar_() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idParticipante = $("#idParticipante").val();
    $.ajax({
        method: "POST",
        dataType: "json",
        url: "../ajax/consultas.php?op=atsparticipante",
        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            idParticipante: idParticipante,
        },
        success: function(response) {
            $("#consulta").modal("hide");
            $("#tbllistado").bootstrapTable("load", response);
        },
    });
}
//Función Listar
function listar() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idParticipante = $("#idParticipante").val();
    let url = `../ajax/consultas.php?op=atsparticipante&fecha_inicio=${fecha_inicio}&fecha_fin=${fecha_fin}&idParticipante=${idParticipante}`;

    $("#tbllistado")
        .bootstrapTable({
            url: url,
            search: true,
            exportTypes: ["json", "csv", "txt", "excel"],
            columns: [{
                    title: "Opciones",
                    formatter(value, row, index, field) {
                        // let btn = [`<button class="btn btn-info btn-xs mostrar"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>`];
                        let btn = [
                            `<a href="../reportes/rptConsultaAts.php?id=${row.id}" target="_blank" class="btn btn-info btn-xs mostrar"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>`,
                        ];

                        return btn.join(" ");
                    },
                    // events: {
                    //   "click .mostrar": (e, value, row, index) => {
                    //     mostrar(row.id);
                    //   },
                    // },
                },
                { field: "empleado", title: "Empleado" },
                { field: "numero_documento", title: "Documento" },
                {
                    //field: "estado",
                    title: "Firma",
                    formatter(value, row, index, field) {
                        let span = `<img src="${row.firma_ruta}"  height='50px' width='50px' ></img>`;
                        return span;
                    },
                },
                { field: "fecha_creacion", title: "fecha Inicio" },
                { field: "hora_fin", title: "hora fin" },
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

function mostrar(id) {
    // ../reportes/rptSubActividad.php
    $.ajax({
        method: "POST",
        dataType: "json",
        url: "../ajax/consultas.php?op=atsfecha",
        data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
        success: function(response) {
            $("#consulta").modal("hide");
            $("#tbllistado").bootstrapTable("load", response);
        },
    });
}

init();