var tabla;

//Función que se ejecuta al inicio
function init() {
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });
  //Cargamos los items al select tipo area
  $.post("../ajax/gestion_peligrosG.php?op=selectTipoAts", function (r) {
    data = JSON.parse(r);
    options = data.data;

    options.forEach((element) => {
      let option = document.createElement("option");
      option.value = element.id;
      option.textContent = element.nombre;
      document.getElementById("tipo_ats_id").appendChild(option);
    });
    $("#tipo_ats_id").selectpicker("refresh");
  });

  $.post("../ajax/gestion_peligrosG.php?op=selecPG", function (r) {
    data = JSON.parse(r);
    options = data.data;

    options.forEach((element) => {
      let option = document.createElement("option");
      option.value = element.id;
      option.textContent = element.nombre;
      document.getElementById("peligroG_id").appendChild(option);
    });
    $("#peligroG_id").selectpicker("refresh");
  });

  $.post("../ajax/gestion_peligrosG.php?op=selecRG", function (r) {
    data = JSON.parse(r);
    options = data.data;

    options.forEach((element) => {
      let option = document.createElement("option");
      option.value = element.id;
      option.textContent = element.nombre;
      document.getElementById("riesgoG_id").appendChild(option);
    });
    $("#riesgoG_id").selectpicker("refresh");
  });

  $.post("../ajax/gestion_peligrosG.php?op=selecMG", function (r) {
    data = JSON.parse(r);
    options = data.data;

    options.forEach((element) => {
      let option = document.createElement("option");
      option.value = element.id;
      option.textContent = element.nombre;
      document.getElementById("medidaG_id").appendChild(option);
    });
    $("#medidaG_id").selectpicker("refresh");
  });

  $("#mAlmacen").addClass("treeview active");
  $("#lGestionPeligrosG").addClass("active");
}

//Función limpiar
function limpiar() {
  $("#id").val("");
  $("#tipo_ats_id").val("");
  $("#tipo_ats_id").selectpicker("refresh");
  $("#peligroG_id").val("");
  $("#peligroG_id").selectpicker("refresh");
  $("#riesgoG_id").val("");
  $("#riesgoG_id").selectpicker("refresh");
  $("#medidaG_id").val("");
  $("#medidaG_id").selectpicker("refresh");
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
  let url = "../ajax/gestion_peligrosG.php?op=listar";

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
        { field: "peligro", title: "Peligro" },
        { field: "riesgo", title: "Riesgo" },
        { field: "medida", title: "Medida" },
        { field: "tipo_ats", title: "Tipo ATS" },
        {
          //field: "estado",
          title: "Respuesta",
          formatter(value, row, index, field) {
            let span = `<span class="label bg-red">Incorrecta</span>`;
            if (row.respuesta == 1) {
              span = '<span class="label bg-green">Correcta</span>';
            }
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
    url: "../ajax/gestion_peligrosG.php?op=guardaryeditar",
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
    "../ajax/gestion_peligrosG.php?op=mostrar",
    { id: id },
    function (data, status) {
      data = JSON.parse(data);
      data = data.data;
      mostrarform(true);

      $("#tipo_ats_id").val(data.tipo_ats_id);
      $("#tipo_ats_id").selectpicker("refresh");
      $("#peligroG_id").val(data.peligro_id);
      $("#peligroG_id").selectpicker("refresh");
      $("#riesgoG_id").val(data.riesgo_id);
      $("#riesgoG_id").selectpicker("refresh");
      $("#medidaG_id").val(data.medida_id);
      $("#medidaG_id").selectpicker("refresh");
      $("#respuesta").val(data.respuesta);
      $("#id").val(data.id);
    }
  );
}

//Función para desactivar registros
function desactivar(id) {
  bootbox.confirm(
    "¿Está Seguro de desactivar la Gestión de Peligro?",
    function (result) {
      if (result) {
        $.post(
          "../ajax/gestion_peligrosG.php?op=desactivar",
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
    "¿Está Seguro de activar la Gestión de Peligro?",
    function (result) {
      if (result) {
        $.post(
          "../ajax/gestion_peligrosG.php?op=activar",
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
