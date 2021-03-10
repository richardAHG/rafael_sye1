var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });

    $("#imagenmuestra").hide();
    //Mostramos los permisos
    $.post("../ajax/usuario.php?op=permisos&id=", function(r) {
        $("#permisos").html(r);
    });

    //Cargamos los items al select cargo
    $.post("../ajax/usuario.php?op=selectCargo", function(r) {
        data = JSON.parse(r);
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("cargo_id").appendChild(option);
        });
        $("#cargo_id").selectpicker("refresh");
    });

    //Cargamos los items al select de regimen
    $.post("../ajax/usuario.php?op=selectRegimen", function(r) {
        data = JSON.parse(r);
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("regimen_id").appendChild(option);
        });
        $("#regimen_id").selectpicker("refresh");
    });

    //Cargamos los items al select tipo documento
    $.post("../ajax/usuario.php?op=selectTipoDocumento", function(r) {
        data = JSON.parse(r);
        options = data.data;

        options.forEach((element) => {
            let option = document.createElement("option");
            option.value = element.id;
            option.textContent = element.nombre;
            document.getElementById("tipo_documento").appendChild(option);
        });
        $("#tipo_documento").selectpicker("refresh");
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

    // $.post("../ajax/usuario.php?op=selectSubArea",{ idarea: 1 },function (r) {
    //     data = JSON.parse(r);
    //     options = data.data;

    //     options.forEach((element) => {
    //       let option = document.createElement("option");
    //       option.value = element.id;
    //       option.textContent = element.nombre;
    //       document.getElementById("subarea_id").appendChild(option);
    //     });
    //     $("#subarea_id").selectpicker("refresh");
    //   }
    // );

    $("#mAcceso").addClass("treeview active");
    $("#lUsuarios").addClass("active");

    $("#frmEditClave").on("submit", function(e) {
        editarClave(e);
    });
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

//Función limpiar
function limpiar() {
    $("#nombre").val("");
    $("#ape_pat").val("");
    $("#ape_mat").val("");
    $("#email").val("");
    $("#cargo_id").val("");
    $("#regimen_id").val("");
    $("#direccion").val("");
    $("#cell").val("");

    $("#numero_documento").val("");
    $("#area_id").val("");
    $("#subarea_id").val("");
    $("#fecha_ingreso").val("");
    $("#fecha_cese").val("");
    $("#login").val("");
    $("#clave").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenactual").val("");
    $("#idusuario").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
        //$("#clave").attr('disabled',false);
        //$("#btnEditClave").show();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
        $("#clave").attr("disabled", false);
        $("#btnEditClave").hide();
    }
}

//Función cancelarform
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar() {
    //Función Listar
    let url = "../ajax/usuario.php?op=listar";

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
                        if (row.ats == 1) {
                            btn.push(
                                `<button class="btn btn-default btn-xs bloquearATS"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i></button>`
                            );
                        } else {
                            btn.push(
                                `<button class="btn btn-success btn-xs habilitarATS"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></button>`
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
                        "click .bloquearATS": (e, value, row, index) => {
                            bloquerATS(row.id);
                        },
                        "click .habilitarATS": (e, value, row, index) => {
                            HabilitarATS(row.id);
                        },
                    },
                },
                { field: "nombre_completo", title: "Nombre" },
                { field: "documento", title: "Documento" },
                { field: "cargo", title: "Cargo" },
                { field: "regimen", title: "Regimen" },
                { field: "area", title: "Area" },
                { field: "login", title: "Login" },
                {
                    //field: "estado",
                    title: "Imagen",
                    formatter(value, row, index, field) {
                        let span = `<img src='../files/usuarios/${row.imagen}'  height='50px' width='50px' ></img>`;
                        return span;
                    },
                },
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
        url: "../ajax/usuario.php?op=guardaryeditar",
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
    $("#clave").attr("disabled", false);
}

function mostrar(idusuario) {
    $("#clave").attr("disabled", true);

    $("#btnEditClave").show();
    $.post(
        "../ajax/usuario.php?op=mostrar", { idusuario: idusuario },
        function(data, status) {
            data = JSON.parse(data);
            data = data.data;
            mostrarform(true);

            $("#nombre").val(data.nombre);
            $("#ape_pat").val(data.ape_pat);
            $("#ape_mat").val(data.ape_mat);
            $("#email").val(data.email);
            $("#cargo_id").val(data.cargo_id);
            $("#cargo_id").selectpicker("refresh");
            $("#regimen_id").val(data.regimen_id);
            $("#regimen_id").selectpicker("refresh");
            $("#direccion").val(data.direccion);
            $("#cell").val(data.cell);

            $("#tipo_documento").val(data.tipo_documento);
            $("#tipo_documento").selectpicker("refresh");
            $("#numero_documento").val(data.numero_documento);

            $("#area_id").val(data.area_id);
            $("#area_id").selectpicker("refresh");
            showSubArea(data.area_id);
            setTimeout(() => {
                $("#subarea_id").val(data.subarea_id);
                $("#subarea_id").selectpicker("refresh");
            }, 3000);

            $("#fecha_ingreso").val(data.fecha_ingreso);
            $("#fecha_cese").val(data.fecha_cese);

            $("#login").val(data.login);
            //$("#clave").val(data.clave);
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
            $("#imagenactual").val(data.imagen);
            $("#idusuario").val(data.id);
        }
    );
    $.post("../ajax/usuario.php?op=permisos&id=" + idusuario, function(r) {
        $("#permisos").html(r);
    });
}

//Función para desactivar registros
function desactivar(idusuario) {
    bootbox.confirm("¿Está Seguro de desactivar el usuario?", function(result) {
        if (result) {
            $.post(
                "../ajax/usuario.php?op=desactivar", { idusuario: idusuario },
                function(e) {
                    data = JSON.parse(e);
                    bootbox.alert(data.mensaje);
                    $("#tbllistado").bootstrapTable("refresh");
                }
            );
        }
    });
}

//Función para activar registros
function activar(idusuario) {
    bootbox.confirm("¿Está Seguro de activar el Usuario?", function(result) {
        if (result) {
            $.post(
                "../ajax/usuario.php?op=activar", { idusuario: idusuario },
                function(e) {
                    data = JSON.parse(e);
                    bootbox.alert(data.mensaje);
                    $("#tbllistado").bootstrapTable("refresh");
                }
            );
        }
    });
}


function bloquerATS(idusuario) {
    bootbox.confirm(
        "¿Está Seguro de bloquear la generacion de ATs para el Usuario?",
        function(result) {
            if (result) {
                $.post(
                    "../ajax/usuario.php?op=bloquerATS", { idusuario: idusuario },
                    function(e) {
                        data = JSON.parse(e);
                        bootbox.alert(data.mensaje);
                        $("#tbllistado").bootstrapTable("refresh");
                    }
                );
            }
        }
    );
}

function HabilitarATS(idusuario) {
    bootbox.confirm(
        "¿Está Seguro de Habilitar la generación de ATS para el Usuario?",
        function(result) {
            if (result) {
                $.post(
                    "../ajax/usuario.php?op=habilitarATS", { idusuario: idusuario },
                    function(e) {
                        data = JSON.parse(e);
                        bootbox.alert(data.mensaje);
                        $("#tbllistado").bootstrapTable("refresh");
                    }
                );
            }
        }
    );
}


function editarClave(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#EditClave").prop("disabled", true);
    var formData = new FormData($("#frmEditClave")[0]);

    $.ajax({
        url: "../ajax/usuario.php?op=Editclave",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            data = JSON.parse(datos);
            bootbox.alert(data.mensaje);
            $("#EditClave").prop("disabled", false);
            $("#myModal").modal("hide");
            mostrarform(false);
        },
    });
    limpiar();
    $("#clave").attr("disabled", false);
}

init();