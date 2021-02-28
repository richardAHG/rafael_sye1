var tabla;

//Función que se ejecuta al inicio
function init() {
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });

  //Cargamos los items al select categoria
  $.post("../ajax/articulo.php?op=selectCategoria", function (r) {
    $("#idcategoria").html(r);
    $("#idcategoria").selectpicker("refresh");
  });
  $("#imagenmuestra").hide();
  $("#mAlmacen").addClass("treeview active");
  $("#lArticulos").addClass("active");
}

//Función limpiar
function limpiar() {
  $("#codigo").val("");
  $("#nombre").val("");
  $("#descripcion").val("");
  $("#stock").val("");
  $("#imagenmuestra").attr("src", "");
  $("#imagenactual").val("");
  $("#print").hide();
  $("#idarticulo").val("");
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
  let url = "../ajax/articulo.php?op=listar";

    // $("#tbllistado").bootstrapTable('destroy').bootstrapTable({
      $("#tbllistado").bootstrapTable({
      url: url,
      search : true,
      //refresh : true,
      // exportDataType: $("#tbllistado").val(),
      // exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
      columns: [
        {
          title: "Opciones",
          formatter(value, row, index, field) {
            let btn = [
              `<button class="btn btn-warning mostrar"><i class="fa fa-pencil"></i></button>`,
            ];
            if (row.condicion == 1) {
              btn.push(
                '<button class="btn btn-danger desactivar"><i class="fa fa-close"></i></button>'
              );
            } else {
              btn.push(
                '<button class="btn btn-primary activar"><i class="fa fa-check"></i></button>'
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
        { field: "categoria", title: "Categoría" },
        { field: "codigo", title: "Código" },
        { field: "stock", title: "Stock" },
        { field: "imagen", title: "Imagen" },
        {
          //field: "estado",
          title: "Estado",
          formatter(value, row, index, field) {
            let span = `<span class="label bg-red">Desactivado</span>`;
            if (row.condicion == 1) {
              span = '<span class="label bg-green">Activado</span>';
            }
            return span;
          },
        },
      ],
      //console.log(url);
    }).trigger('change');
  
 
}

// function tipoExport(){
//   let select = document.querySelector("#tipo_export");

//   select.onchange = (e)=>{
//     listar();
//   }
// }
// tipoExport();

function listar_old() {
  tabla = $("#tbllistado")
    .dataTable({
      lengthMenu: [5, 10, 25, 75, 100], //mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "<Bl<f>rtip>", //Definimos los elementos del control de tabla
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdf"],
      ajax: {
        url: "../ajax/articulo.php?op=listar",
        type: "get",
        dataType: "json",
        error: function (e) {
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
      order: [[0, "desc"]], //Ordenar (columna,orden)
    })
    .DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  $("#btnGuardar").prop("disabled", true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "../ajax/articulo.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      bootbox.alert(datos);
      mostrarform(false);
      tabla.ajax.reload();
    },
  });
  limpiar();
}

function mostrar(idarticulo) {
  $.post(
    "../ajax/articulo.php?op=mostrar",
    { idarticulo: idarticulo },
    function (data, status) {
      data = JSON.parse(data);
      mostrarform(true);

      $("#idcategoria").val(data.idcategoria);
      $("#idcategoria").selectpicker("refresh");
      $("#codigo").val(data.codigo);
      $("#nombre").val(data.nombre);
      $("#stock").val(data.stock);
      $("#descripcion").val(data.descripcion);
      $("#imagenmuestra").show();
      $("#imagenmuestra").attr("src", "../files/articulos/" + data.imagen);
      $("#imagenactual").val(data.imagen);
      $("#idarticulo").val(data.idarticulo);
      generarbarcode();
    }
  );
}

//Función para desactivar registros
function desactivar(idarticulo) {
  bootbox.confirm("¿Está Seguro de desactivar el artículo?", function (result) {
    if (result) {
      $.post(
        "../ajax/articulo.php?op=desactivar",
        { idarticulo: idarticulo },
        function (e) {
          bootbox.alert(e);
          //tabla.ajax.reload();
          $("#tbllistado").bootstrapTable("refresh");
        }
      );
    }
  });
}

//Función para activar registros
function activar(idarticulo) {
  bootbox.confirm("¿Está Seguro de activar el Artículo?", function (result) {
    if (result) {
      $.post(
        "../ajax/articulo.php?op=activar",
        { idarticulo: idarticulo },
        function (e) {
          bootbox.alert(e);
          //tabla.ajax.reload();
          $("#tbllistado").bootstrapTable("refresh");
        }
      );
    }
  });
}

//función para generar el código de barras
function generarbarcode() {
  codigo = $("#codigo").val();
  JsBarcode("#barcode", codigo);
  $("#print").show();
}

//Función para imprimir el Código de barras
function imprimir() {
  $("#print").printArea();
}

init();
