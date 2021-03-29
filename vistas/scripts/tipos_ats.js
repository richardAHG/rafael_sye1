var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });
    //Cargamos los items al select tipo area
    $.post("../ajax/tipo_ats.php?op=selectTipo", function(r) {
        data = JSON.parse(r);
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("tipo").appendChild(option);
        });
        $("#tipo").selectpicker("refresh");
    });

    $("#mAlmacen").addClass("treeview active");
    $("#ltiposAts").addClass("active");
}

//Función limpiar
function limpiar() {
    $("#id").val("");
    $("#tipo").val("");
    $("#tipo").selectpicker("refresh");
    $("#codigo").val("");
    $("#version").val("");
    $("#fecha").val("");
    $("#actividad").val("");
    $("#especifico").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
        $("#parent-content").hide();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

//Función cancelarform
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar() {
    let url = "../ajax/tipo_ats.php?op=listar";

    $("#tbllistado")
        .bootstrapTable({
            url: url,
            search: true,
            exportTypes: ["json", "csv", "txt", "excel"],
            columns: [{
                    title: "Opciones",
                    formatter(value, row, index, field) {
                        let btn = [];
                        if (row.estado == 1) {
                            btn.push(
                                `<button class="btn btn-warning btn-xs mostrar"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>`,
                                `<button class="btn btn-danger btn-xs desactivar"><i class="fa fa-trash-o" aria-hidden="true"></i></button>`
                            );
                        } else {
                            btn.push(
                                `<button class="btn btn-primary btn-xs activar"><i class="fa fa-check-square-o" aria-hidden="true"></i></button>`
                            );
                        }
                        return btn.join(" ");
                    },
                    events: {
                        "click .mostrar": (e, value, row, index) => {
                            mostrar(row.id);
                        },
                        "click .desactivar": (e, value, row, index) => {
                            desactivar(row.id);
                        },
                        "click .activar": (e, value, row, index) => {
                            activar(row.id);
                        },
                    },
                },
                { field: "ats", title: "ATS" },
                { field: "codigo", title: "Codigo versión" },
                { field: "version", title: "version" },
                { field: "fecha", title: "fecha" },
                { field: "actividad", title: "actividad" },
                { field: "especifico", title: "especifico" },
                {
                    //field: "estado",
                    title: "Estado",
                    formatter(value, row, index, field) {
                        let span = `<span class="label bg-red">Desactivado</span>`;
                        if (row.estado == 1) {
                            span = '<span class="label bg-green">Activado</span>';
                        }
                        return span;
                    },
                },
            ],
        })
        .trigger("change");
}
//Función para guardar o editar

function guardaryeditar(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    let valor = 0;
    if (document.getElementById("especifico").checked == true) {
        valor = 1;
    }
    // console.log(checkbox2);
    // return;
    var formData = new FormData($("#formulario")[0]);
    formData.append("especifico_", valor);

    $.ajax({
        url: "../ajax/tipo_ats.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            data = JSON.parse(datos);
            bootbox.alert(data.mensaje);
            mostrarform(false);
            $("#tbllistado").bootstrapTable("refresh");
        },
    });
    limpiar();
}

function mostrar(id) {
    $.post(
        "../ajax/tipo_ats.php?op=mostrar", { id: id },
        function(data, status) {
            data = JSON.parse(data);
            data = data.data;
            mostrarform(true);

            $("#codigo").val(data.codigo);
            $("#version").val(data.version);
            $("#fecha").val(data.fecha);
            $("#actividad").val(data.actividad);
            $("#especifico").attr("checked", data.especifico);
            $("#tipo").val(data.tipo_ats);
            $("#tipo").selectpicker("refresh");
            $("#id").val(data.id);
        }
    );
}

//Función para desactivar registros
function desactivar(id) {
    bootbox.confirm("¿Está Seguro de desactivar el ATS?", function(result) {
        if (result) {
            $.post("../ajax/tipo_ats.php?op=desactivar", { id: id }, function(e) {
                data = JSON.parse(e);
                bootbox.alert(data.mensaje);
                $("#tbllistado").bootstrapTable("refresh");
            });
        }
    });
}

//Función para activar registros
function activar(id) {
    bootbox.confirm("¿Está Seguro de activar el ATS?", function(result) {
        if (result) {
            $.post("../ajax/tipo_ats.php?op=activar", { id: id }, function(e) {
                data = JSON.parse(e);
                bootbox.alert(data.mensaje);
                $("#tbllistado").bootstrapTable("refresh");
            });
        }
    });
}

init();