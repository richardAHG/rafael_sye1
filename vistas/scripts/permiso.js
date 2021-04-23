var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();
    $("#mAsignar").addClass("treeview active");
    $("#lPermisos").addClass("active");
}

//Función mostrar formulario
function mostrarform(flag) {
    //limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").hide();
    }
}

//Función Listar
function listar() {
    let url = "../ajax/permiso.php?op=listar";

    $("#tbllistado")
        .bootstrapTable({
            url: url,
            search: true,
            exportTypes: ["json", "csv", "txt", "excel"],
            columns: [{ field: "nombre", title: "Nombre" }],
        })
        .trigger("change");
}

init();