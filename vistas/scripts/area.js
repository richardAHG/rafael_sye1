var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });
    //Cargamos los items al select tipo area
    $.post("../ajax/area.php?op=selectTipo", function(r) {
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

    $.post("../ajax/area.php?op=selectArea", function(r) {
        data = JSON.parse(r);
        options = data.data;

        let option = document.createElement("option");
        option.value = 0;
        option.textContent = 'Selected';
        document.getElementById("parent").appendChild(option);

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("parent").appendChild(option);
        });

        // $("#parent").html(r);
        // $("#parent").prepend("<option >Selected</option>");
        $("#parent").selectpicker("refresh");
    });

    $("#mAsignar").addClass("treeview active");
    $("#lArea").addClass("active");
}

//Función limpiar
function limpiar() {
    $("#id").val("");
    $("#nombre").val("");
    $("#tipo").val("");
    $("#tipo").selectpicker("refresh");
    $("#parent").val("");
    $("#parent").selectpicker("refresh");
}

function mostarSelectArea() {
    let tipo = document.getElementById("tipo");
    tipo.onchange = function(e) {
        let tipo = e.target.value;
        if (tipo == 2) {
            $("#parent-content").show();
            $("#parent").selectpicker("refresh");
        } else {
            $("#parent-content").hide();
            $("#parent").val("");
            $("#parent").selectpicker("refresh");
        }
    };
}

function mostarSelectAreaEdit(tipo) {
    if (tipo == 2) {
        $("#parent-content").show();
    } else {
        $("#parent-content").hide();
    }
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
    mostarSelectArea();
}

//Función cancelarform
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar() {
    let url = "../ajax/area.php?op=listar";

    $("#tbllistado").bootstrapTable({
        url: url,
        search: true,
        exportTypes: ['json', 'csv', 'txt', 'excel'],
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
            { field: "nombre", title: "Nombre" },
            { field: "tipo", title: "Tipo" },
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
    }).trigger('change');

}

function listarBK() {
    tabla = $("#tbllistado")
        .DataTable({
            lengthMenu: [5, 10, 25, 75, 100], //mostramos el menú de registros a revisar
            aProcessing: true, //Activamos el procesamiento del datatables
            aServerSide: true, //Paginación y filtrado realizados por el servidor
            dom: "<Bl<f>rtip>", //Definimos los elementos del control de tabla
            buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdf"],
            ajax: {
                url: "../ajax/area.php?op=listar",
                type: "get",
                dataType: "json",
                error: function(e) {
                    console.log(e.responseText);
                },
            },

            language: {
                lengthMenu: "Mostrar : _MENU_ registros",
                buttons: {
                    copyTitle: "Tabla Copiada",
                    copySuccess: {
                        _: "%d líneas copiadas",
                        1: "1 línea copiada",
                    },
                },
            },
            bDestroy: true,
            iDisplayLength: 5, //Paginación
            order: [
                [0, "desc"]
            ], //Ordenar (columna,orden)
        });
    // .DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/area.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            data = JSON.parse(datos);
            bootbox.alert(data.mensaje);
            mostrarform(false);
            $("#tbllistado").bootstrapTable('refresh');
        },
    });
    limpiar();
}

function mostrar(id) {

    $.post("../ajax/area.php?op=mostrar", { id: id }, function(data, status) {
        data = JSON.parse(data);
        data = data.data;
        mostrarform(true);

        $("#nombre").val(data.nombre);
        $("#tipo").val(data.tipo_id);
        $("#tipo").selectpicker("refresh");
        $("#parent").val(data.parent_id);
        $("#parent").selectpicker("refresh");
        $("#id").val(data.id);
        mostarSelectAreaEdit(data.tipo_id);
    });
}

//Función para desactivar registros
function desactivar(id) {
    bootbox.confirm("¿Está Seguro de desactivar el Area?", function(result) {
        if (result) {
            $.post("../ajax/area.php?op=desactivar", { id: id }, function(e) {
                data = JSON.parse(e);
                bootbox.alert(data.mensaje);
                // tabla.ajax.reload();
                $("#tbllistado").bootstrapTable('refresh');
            });
        }
    });
}

//Función para activar registros
function activar(id) {
    bootbox.confirm("¿Está Seguro de activar el Area?", function(result) {
        if (result) {
            $.post("../ajax/area.php?op=activar", { id: id }, function(e) {
                data = JSON.parse(e);
                bootbox.alert(data.mensaje);
                // tabla.ajax.reload();
                $("#tbllistado").bootstrapTable('refresh');
            });
        }
    });
}

init();