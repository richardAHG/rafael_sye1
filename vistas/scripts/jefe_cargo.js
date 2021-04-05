var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });

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

    //Cargamos los items al select area
    $.post("../ajax/usuario.php?op=selectArea", function(r) {
        data = JSON.parse(r);
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("area_id").appendChild(option);
        });
        $("#area_id").selectpicker("refresh");
    });

    $("#mAcceso").addClass("treeview active");
    $("#lJefeCargo").addClass("active");
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

getSubArea();

function getSubArea() {
    let Carea = document.querySelector("#area_id");
    Carea.onchange = (e) => {
        // console.log(e.target.value);
        $("#subarea_id").html("");
        //Cargamos los items al select sub area
        $.post(
            "../ajax/usuario.php?op=selectSubArea", { idarea: e.target.value },
            function(r) {
                data = JSON.parse(r);
                options = data.data;

                options.forEach((element) => {
                    let option = document.createElement("option");
                    option.value = element.id;
                    option.textContent = element.nombre;
                    document.getElementById("subarea_id").appendChild(option);
                });
                $("#subarea_id").selectpicker("refresh");
            }
        );
    };
}

function showSubArea(idArea) {
    $("#subarea_id").html("");
    //Cargamos los items al select sub area
    $.post(
        "../ajax/usuario.php?op=selectSubArea", { idarea: idArea },
        function(r) {
            data = JSON.parse(r);
            options = data.data;

            options.forEach((element) => {
                let option = document.createElement("option");
                option.value = element.id;
                option.textContent = element.nombre;
                document.getElementById("subarea_id").appendChild(option);
            });
            $("#subarea_id").selectpicker("refresh");
        }
    );
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
    // mostarSelectArea();
}

//Función cancelarform
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar() {
    let url = "../ajax/jefe_cargo.php?op=listar";

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
                { field: "personal", title: "personal" },
                { field: "area", title: "area" },
                { field: "subarea", title: "subarea" },
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
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/jefe_cargo.php?op=guardaryeditar",
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
        "../ajax/jefe_cargo.php?op=mostrar", { id: id },
        function(data, status) {
            data = JSON.parse(data);
            data = data.data;
            mostrarform(true);

            $("#idParticipante").val(data.personal_id);
            $("#idParticipante").selectpicker("refresh");
            $("#area_id").val(data.area_id);
            $("#area_id").selectpicker("refresh");
            showSubArea(data.area_id);
            setTimeout(() => {
                $("#subarea_id").val(data.subarea_id);
                $("#subarea_id").selectpicker("refresh");
            }, 3000);

            $("#id").val(data.id);
            mostarSelectAreaEdit(data.tipo_id);
        }
    );
}

//Función para desactivar registros
function desactivar(id) {
    bootbox.confirm(
        "¿Está Seguro de desactivar al Jefe a cargo?",
        function(result) {
            if (result) {
                $.post(
                    "../ajax/jefe_cargo.php?op=desactivar", { id: id },
                    function(e) {
                        data = JSON.parse(e);
                        bootbox.alert(data.mensaje);
                        // tabla.ajax.reload();
                        $("#tbllistado").bootstrapTable("refresh");
                    }
                );
            }
        }
    );
}

//Función para activar registros
function activar(id) {
    bootbox.confirm(
        "¿Está Seguro de activar al Jefe a cargo?",
        function(result) {
            if (result) {
                $.post("../ajax/jefe_cargo.php?op=activar", { id: id }, function(e) {
                    data = JSON.parse(e);
                    bootbox.alert(data.mensaje);
                    // tabla.ajax.reload();
                    $("#tbllistado").bootstrapTable("refresh");
                });
            }
        }
    );
}

init();