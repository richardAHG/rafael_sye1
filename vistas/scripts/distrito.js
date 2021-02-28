var tabla;

//Función que se ejecuta al inicio
function init() {
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });
  $("#mAlmacen").addClass("treeview active");
  $("#lDistrito").addClass("active");
}

//Función limpiar
function limpiar() {
  $("#id").val("");
  $("#nombre").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
  limpiar();
  if (flag) {
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnGuardar").prop("disabled", false);
    $("#btnagregar").hide();
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
  let url = "../ajax/distrito.php?op=listar";

  $("#tbllistado")
    .bootstrapTable({
      url: url,
      search: true,
      exportTypes: ["json", "csv", "txt", "excel"],
      columns: [
        {
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
    url: "../ajax/distrito.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      data = JSON.parse(datos);
      bootbox.alert(data.mensaje);
      mostrarform(false);
      $("#tbllistado").bootstrapTable('refresh');
    },
  });
  limpiar();
}

function mostrar(id) {
  $.post(
    "../ajax/distrito.php?op=mostrar",
    { id: id },
    function (data, status) {
      data = JSON.parse(data);
      data = data.data;
      mostrarform(true);

      $("#nombre").val(data.nombre);
      $("#id").val(data.id);
    }
  );
}

//Función para desactivar registros
function desactivar(id) {
  bootbox.confirm("¿Está Seguro de desactivar el Cargo?", function (result) {
    if (result) {
      $.post("../ajax/distrito.php?op=desactivar", { id: id }, function (e) {
        data = JSON.parse(e);
        bootbox.alert(data.mensaje);
        $("#tbllistado").bootstrapTable('refresh');
      });
    }
  });
}

//Función para activar registros
function activar(id) {
  bootbox.confirm("¿Está Seguro de activar el cargo?", function (result) {
    if (result) {
      $.post("../ajax/distrito.php?op=activar", { id: id }, function (e) {
        data = JSON.parse(e);
        bootbox.alert(data.mensaje);
        $("#tbllistado").bootstrapTable('refresh');
      });
    }
  });
}

init();
