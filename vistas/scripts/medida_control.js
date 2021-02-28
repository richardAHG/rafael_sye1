var tabla;

//Función que se ejecuta al inicio
function init() {
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });
  //Cargamos los items al select tipo area
  $.post("../ajax/medida_control.php?op=selectTipo", function (r) {
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
  $("#lMedidaControl").addClass("active");
}

//Función limpiar
function limpiar() {
  $("#id").val("");
  $("#nombre").val("");
  $("#tipo").val("");
  $("#tipo").selectpicker("refresh");
}

function mostarSelectArea() {
  let tipo = document.getElementById("tipo");
  tipo.onchange = function (e) {
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
  let url = "../ajax/medida_control.php?op=listar";

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
    })
    .trigger("change");
}
//Función para guardar o editar

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  $("#btnGuardar").prop("disabled", true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "../ajax/medida_control.php?op=guardaryeditar",
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
    "../ajax/medida_control.php?op=mostrar",
    { id: id },
    function (data, status) {
      data = JSON.parse(data);
      data = data.data;
      mostrarform(true);

      $("#nombre").val(data.nombre);
      $("#tipo").val(data.tipo_id);
      $("#tipo").selectpicker("refresh");
      $("#id").val(data.id);
      mostarSelectAreaEdit(data.tipo_id);
    }
  );
}

//Función para desactivar registros
function desactivar(id) {
  bootbox.confirm(
    "¿Está Seguro de desactivar el Medida de Control?",
    function (result) {
      if (result) {
        $.post(
          "../ajax/medida_control.php?op=desactivar",
          { id: id },
          function (e) {
            data = JSON.parse(e);
            bootbox.alert(data.mensaje);
            $("#tbllistado").bootstrapTable('refresh');
          }
        );
      }
    }
  );
}

//Función para activar registros
function activar(id) {
  bootbox.confirm(
    "¿Está Seguro de activar el Medida de Control?",
    function (result) {
      if (result) {
        $.post(
          "../ajax/medida_control.php?op=activar",
          { id: id },
          function (e) {
            data = JSON.parse(e);
            bootbox.alert(data.mensaje);
            $("#tbllistado").bootstrapTable('refresh');
          }
        );
      }
    }
  );
}

init();
