$("#frmAcceso").on("submit", function (e) {
  e.preventDefault();
  logina = $("#logina").val();
  clavea = $("#clavea").val();

  $.post(
    "../ajax/usuario.php?op=verificar",
    { logina: logina, clavea: clavea },
    function (data) {
      console.log(data); 
      data = JSON.parse(data);
      if (data.acceso) {
        $(location).attr("href", "escritorio.php");
      } else {
        bootbox.alert("Usuario y/o Password incorrectos");
      }
    }
  );
});
