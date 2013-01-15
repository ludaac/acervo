<div id="classPage" class="grid_12 alpha omega" style="display: none;">
	<div class="grid_8 push_2" style="margin: 2em auto;">
		<table id="data"></table>
		<div id="pager"></div>
	</div>
	<div class="clear"></div>
	<div class="control grid_3 push_4">
		<button id="new">Nueva</button>
		<button id="modif">Modificar</button>
	</div>
	<div id="modClassDialog" title="Modificar clasificación">
		<p id="mclassError"></p>
		<form id="modClassForm" action=".">
			<label>Número:</label>
			<input id="mclnum" type="text" name="clnum" />
			<label>Nombre:</label>
			<input id="mclname" type="text" name="clname" />
			<input id="mclid" type="hidden" name="id" />
		</form>
	</div>
	<div id="newClassDialog" title="Nueva clasificación">
		<p id="nclassError"></p>
		<form id="newClassForm" action=".">
			<label>Número:</label>
			<input id="nclnum" name="clnum" type="text" />
			<label>Nombre:</label>
			<input id="nclname" name="clname" type="text" />
		</form>
	</div>
</div>
<script>
	$("#data").jqGrid({
		url: "control/class/response.php",
		datatype: "json",
		colNames: ['Número', 'Nombre'],
		colModel: [
			{name:'num', index:'num', width: 110},
			{name:'name', index:'name', width: 500}
		],
		rowNum: 10,
		pager: "pager",
		viewrecords: true
	});
	// TODO: Add searching...
	$("#data").jqGrid('navGrid', '#pager',
		{ edit: false, add: false, del: false, search: false, refresh: true });
	$("#modClassDialog").dialog({
		autoOpen: false,
		buttons: {
			"Guardar": function () {
				var clre = new RegExp("^[0-9]{1,3}(\.[0-9]{1,5})$");
				if (!clre.test($("#mclnum").val())) {
					$("#mclassError").empty().addClass("ui-state-error")
						.html("Escriba una clasificación válida");
				}
				else if ($.trim($("#mclname").val()) == '') {
					$("#mclassError").empty().addClass("ui-state-error")
						.html("El campo nombre no puede ir vacío");
				}
				else {
					$("#modClassForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#mclnum").val('');
			$("#mclname").val('');
			$("#mclid").val(0);
			$("#mclassError").removeClass("ui-state-error").empty();
		}
	});
	$("#newClassDialog").dialog({
		autoOpen: false,
		buttons: {
			"Añadir": function () {
				var clre = new RegExp("^[0-9]{1,3}(\.[0-9]{1,5})$");
				if (!clre.test($("#nclnum").val())) {
					$("#nclassError").empty().addClass("ui-state-error")
						.html("Escriba una clasificación válida");
				}
				else if ($.trim($("#nclname").val()) == '') {
					$("#nclassError").empty().addClass("ui-state-error")
						.html("El campo nombre no puede ir vacío");
				}
				else {
					$("#newClassForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#nclnum").val('');
			$("#nclname").val('');
			$("#nclassError").removeClass("ui-state-error").empty();
		}
	});
	
	$("#new").button().click(function() {
		$("#newClassDialog").dialog('open');
	});
	$("#modif").button().click(function() {
		var id = $("#data").jqGrid("getGridParam", "selrow");
		if(id) {
			var ret = $("#data").jqGrid("getRowData", id);
			$("#modClassDialog").dialog('open');
			$("#mclnum").val(ret.num);
			$("#mclname").val(ret.name);
			$("#mclid").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	$("#newClassForm").submit(function(event) {
		event.preventDefault();
		$.post('control/class/clean.php', $("#newClassForm").serialize(),
			function(data){
				displayMessage(data);
			},
		"json");
	});
	$("#modClassForm").submit(function(event) {
		event.preventDefault();
		$.post('control/class/clean.php', $("#modClassForm").serialize(),
			function(data){
				displayMessage(data);
			},
		"json");
	});
</script>