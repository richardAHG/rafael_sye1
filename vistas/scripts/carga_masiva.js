var tabla;

//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function(e) {
        validarFile(e);
    });

    // $("#mAlmacen").addClass("treeview active");
    $("#mAcceso").addClass("treeview active");
    $("#lCargaMasiva").addClass("active");
}

//Función limpiar
function limpiar() {
    $("#id").val("");
    $("#archivo").val("");
}

function guardarFile() {
    let tipo = document.getElementById("btnGuardarFile");
    tipo.onclick = function(e) {
        let valor = tipo.getAttribute("data-name-archivo");
        console.log(valor);
        //elimnamos el boton para que el usuario no pulse otra vez
        destroyBtnSaveFile();
        $.post(
            "../ajax/carga_masiva.php?op=guardar", { nombreArchivo: valor },
            function(e) {
                data = JSON.parse(e);
                console.log(data);
                bootbox.alert(data.mensaje);

                // tabla.ajax.reload();
                // $("#tbllistado").bootstrapTable("refresh");
                const span = document.createElement("span");
                const ol = document.createElement("ol");
                span.innerHTML = `<h3>${data.data.mensaje}</h3>
        <p>Total usuarios Cargados: ${data.data.totalReg}</p>
        <p>Total usuarios Insertados: ${data.data.totalInsertado}</p>
        <p>Total usuarios No insertados: ${data.data.noInsertado}</p>
        `;

                const li = document.createElement("li");
                li.innerText = `Listado de usuarios no insertados, porque su DNI ya existe en la Base de Datos`;
                ol.appendChild(li);
                data.data.data.forEach((element) => {
                    const li = document.createElement("li");
                    li.innerText = `Usuario: ${element}`;
                    ol.appendChild(li);
                });
                document.querySelector(".respuesta_error").appendChild(span);
                document.querySelector(".respuesta_error").appendChild(ol);
                $("#tbllistado").bootstrapTable("refresh");
            }
        );

    };
}

function destroyBtnSaveFile() {
    document.getElementById("btnGuardarFile").remove();
}

function cleanMsgErrorValidation() {
    let msg = document.querySelector(".respuesta_error");
    if (msg.textContent != "") {
        document.querySelector(".respuesta_error").innerHTML = "";
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
        cleanMsgErrorValidation();
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
    let url = "../ajax/usuario.php?op=listar";

    $("#tbllistado")
        .bootstrapTable({
            url: url,
            search: true,
            exportTypes: ["json", "csv", "txt", "excel"],
            columns: [
                { field: "nombre", title: "Nombre" },
                { field: "apepat", title: "apepat" },
                { field: "apemat", title: "apemat" },
                { field: "email", title: "email" },
                { field: "cargo", title: "cargo" },
                { field: "regimen", title: "regimen" },
                { field: "direccion", title: "direccion" },
                { field: "cell", title: "cell" },
                { field: "tipoDocumento", title: "tipoDocumento" },
                { field: "numero_documento", title: "numero_documento" },
                { field: "area", title: "area" },
                { field: "subarea", title: "subarea" },
                { field: "fecha_ingreso", title: "fecha_ingreso" },
                { field: "fecha_cese", title: "fecha_cese" },
                { field: "login", title: "login" },
                { field: "ats", title: "ats" },
                { field: "estado", title: "estado" },
                { field: "grupo_sanguineo", title: "grupo_sanguineo" }
            ],
        })
        .trigger("change");
}

//validar que input file no este vacio
function valideInputFile() {
    let isOk = document.querySelector("#archivo").files.length;
    if (isOk == 0) {
        bootbox.alert("Debe cargar un archivo");
    }
    return isOk;
}
//validar extencion de archivo cargado
function validarExtension() {
    // Obtener nombre de archivo
    let archivo = document.getElementById("archivo").value;
    // Obtener extensión del archivo
    extension = archivo.substring(archivo.lastIndexOf("."), archivo.length);
    let isok = 0;
    if (extension == ".xlsx" || extension == ".csv") {
        isok = 1;
    }
    return isok;
}

//Función para guardar o editar

function validarFile(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);
    let clean = valideInputFile();
    if (clean == 0) {
        return false;
    }
    let ext = validarExtension();
    if (ext == 0) {
        bootbox.alert("Archivo con extensión no permitida");
        return false;
    }
    $.ajax({
        url: "../ajax/carga_masiva.php?op=validar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            data = JSON.parse(datos);
            if (!data.estado) {
                const span = document.createElement("span");
                const ul = document.createElement("ul");
                span.innerHTML = `<h3>${data.mensaje}</h3>`;
                for (const property in data.error) {
                    const li = document.createElement("li");
                    li.innerText = `Columna: ${property} - Valores: ${data.error[property]}`;
                    ul.appendChild(li);
                }

                document.querySelector(".respuesta_error").appendChild(span);
                document.querySelector(".respuesta_error").appendChild(ul);
            } else {
                const button = document.createElement("button");
                button.type = "button";
                button.className = "btn btn-success";
                button.innerHTML = '<i class="fa fa-save"></i> Guardar';
                button.dataset.nameArchivo = data.archivo;
                button.setAttribute("id", "btnGuardarFile");
                document.querySelector(".seccion-botones").appendChild(button);
                guardarFile();
                $("#tbllistado").bootstrapTable("refresh");
            }

        },
    });
    limpiar();

}

//Función para desactivar registros
function desactivar(id) {
    bootbox.confirm(
        "¿Está Seguro desea realizar una carga masiva?",
        function(result) {
            if (result) {
                $.post("../ajax/carga_masiva.php?op=validar", { id: id }, function(e) {
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