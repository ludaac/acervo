<div id="userPage" style="display: none;">
    <div class="row">
	   <div class="span8 offset2" style="margin-top: 1em; margin-bottom: 1em;">
		  <table id="userData"></table>
		  <div id="userPager"></div>
	   </div>
    </div>
    <div class="row">
	   <div class="control span5 offset3">
		  <button id="userNew">Nuevo</button>
		  <button id="userEdit">Editar</button>
		  <button id="userPass">Cambiar contraseña</button>
	   </div>
    </div>
	<div id="userNewDialog" title="Nuevo usuario">
		<p id="nusError"></p>
		<form id="userNewForm">
			<label>Nombre completo:</label>
			<input id="nufname" type="text" name="name" />
			<label>Usuario:</label>
			<input id="nuname" type="text" name="uname" />
			<label>Contraseña:</label>
			<input id="nupswd" type="password" name="upswd" />
		</form>
	</div>
	<div id="userEditDialog" title="Modificar usuario">
		<p id="musError"></p>
		<form id="userEditForm">
			<label>Nombre completo:</label>
			<input id="mufname" type="text" name="usfname" />
			<label>Usuario:</label>
			<input id="muname" type="text" name="usuname" />
			<label>Estado:</label>
			<select id="mstat" name="stat">
				<option value="1">Activo</option>
				<option value="0">Inactivo</option>
			</select>
			<input id="muid" type="hidden" name="id" />
		</form>
	</div>
	<div id="userPassDialog" title="Cambiar contraseña">
		<p id="mpsError"></p>
		<form id="userPassForm">
			<label>Nueva contraseña:</label>
			<input id="fpass" type="password" name="npass"/>
			<label>Repetir:</label>
			<input id="spass" type="password" />
			<input id="mpid" type="hidden" name="id" />
		</form>
	</div>
</div>
<script>
	$("#userNewDialog").dialog({
		autoOpen:false,
		buttons: {
			"Añadir": function() {
				if($.trim($("#nufname").val()) == '' || $.trim($("#nuname").val()) == '' ||
						$.trim($("#nupswd").val()) == '') {
					$("#nusError").empty().addClass('ui-state-error').
						html("Todos los campos son obligatorios");
				}
				else {
					$("#userNewForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#nusError").removeClass('ui-state-error').empty();
			$("#nufname").val('');
			$("#nuname").val('');
			$("#nupswd").val('');
		}
	});
	$("#userEditDialog").dialog({
		autoOpen:false,
		buttons: {
			"Guardar": function() {
				if($.trim($("#mufname").val()) == '' ||
						$.trim($("#muname").val()) == '') {
					$("#musError").empty().addClass('ui-state-error').
						html("Todos los campos son obligatorios");
				}
				else {
					$("#userEditForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#musError").removeClass('ui-state-error').empty();
			$("#mufname").val('');
			$("#muname").val('');
		}
	});
	$("#userPassDialog").dialog({
		autoOpen:false,
		buttons: {
			"Guardar": function(){
				if($.trim($("#fpass").val()) != $.trim($("#spass").val())) {
					$("#mpsError").empty().addClass('ui-state-error')
						.html("Ambas contraseñas deben coincidir");
				}
				else {
					$("#userPassForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#mpsError").removeClass('ui-state-error').empty();
			$("#fpass").val('');
			$("#spass").val('');
		}
	});
	
	$("#userData").jqGrid({
		url: "control/user/response.php",
		datatype: "json",
		colNames: ['Nombre completo', 'Usuario', 'Estado'],
		colModel: [
			{name:'usfname', index:'mem_code', width: 270},
			{name:'usuname', index:'mem_name', width: 150},
			{name:'usstat', index:'init_date', width: 120}
		],
		rowNum: 10,
		pager: "userPager",
		viewrecords: true
	});
	$("#userData").jqGrid('navGrid', '#userPager',
			{ edit: false, add: false, del: false, search: false, refresh: true });

	$("#userEdit").button().click(function() {
		var id = $("#userData").jqGrid("getGridParam", "selrow");
		if(id) {
			var ret =$("#userData").jqGrid("getRowData", id);
			$("#userEditDialog").dialog('open');
			$("#mufname").val(ret.usfname);
			$("#muname").val(ret.usuname);
			$("#muid").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	
	$("#userNew").button().click(function() {
		$("#userNewDialog").dialog('open');
	});
	
	$("#userPass").button().click(function() {
		var id = $("#userData").jqGrid("getGridParam", "selrow");
		if(id) {
			$("#userPassDialog").dialog('open');
			$("#mpid").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	
	$("#userNewForm").submit(function(event) {
		event.preventDefault();
		$.post("control/user/clean.php", $("#userNewForm").serialize(),
			function(data) {
				displayMessage(data);
			}
		);
	});
	$("#userEditForm").submit(function(event) {
		event.preventDefault();
		$.post("control/user/clean.php", $("#userEditForm").serialize(),
			function(data) {
				displayMessage(data);
			}
		);
	});
	
	$("#userPassForm").submit(function(event) {
		event.preventDefault();
		$.post("control/user/clean.php", $("#userPassForm").serialize(),
			function(data) {
				displayMessage(data);
			}
		);
	});
</script>