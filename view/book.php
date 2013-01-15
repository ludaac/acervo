<div id="bookPage" style="display: none;">
    <div class="row">
	   <div class="span7 offset2" style="margin-top: 1em; margin-bottom 1em;">
		  <input id="bkeys" name="keys" type="text" style="width: 80%"/>
		  <button id="filBut">Filtrar</button>
	   </div>
    </div>
    <div class="row">
	   <div class="span12">
		  <table id="bookData"></table>
		  <div id="bookPager"></div>
	   </div>
    </div>
	<div class="row">
	   <div class="control span9 offset1">
		  <button id="bookNew">Nuevo</button>
		  <button id="bookModif">Modificar</button>
		  <button id="bookCl">Clasificar</button>
		  <button id="bookAdd">Agregar ejemplares</button>
		  <button id="bookBorrow">Préstamo</button>
		  <button id="bookRemove">Eliminar título</button>
	   </div>
    </div>
	<div id="modBookDialog" title="Modificar libro">
		<p id="mbookError"></p>
		<form id="modBookForm" action=".">
			<label>Código:</label>
			<input id="mbknum" name="code" type="text" />
			<label>ISBN:</label>
			<input id="mbkisbn" name="isbn" type="text" />
			<label>Título:</label>
			<input id="mbktitle" name="title" type="text" />
			<label>Autor:</label>
			<input id="mbkauthor" name="author" type="text" />
			<label>Editorial:</label>
			<input id="mbkedit" name="edit" type="text" />
			<label>Edición:</label>
			<input id="mbked" name="ed" type="text" />
			<input id="mbkid" name="id" type="hidden" />
		</form>
	</div>
	<div id="newBookDialog" title="Nuevo libro">
		<p id="nbookError"></p>
		<form id="newBookForm" action=".">
			<label>Código:</label>
			<input id="nbknum" name="code" type="text" />
			<label>ISBN:</label>
			<input id="nbkname" name="isbn" type="text" />
			<label>Título:</label>
			<input id="nbktitle" name="title" type="text" />
			<label>Autor:</label>
			<input id="nbkauthor" name="author" type="text" />
			<label>Editorial:</label>
			<input id="nbkedit" name="edit" type="text" />
			<label>Edición:</label>
			<input id="nbked" name="ed" type="text" />
			<label>Clasificación:</label>
			<select id="clSel" name="clss"></select>
			<label>Ejemplares:</label>
			<select id="nbkCopy" name="ncp">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
			</select>
		</form>
	</div>
	
	<div id="bookClDialog" title="Clasificar">
		<form id="bookClForm">
			<label>Nueva clasificación:</label>
			<select id="mbkcl" name="idc"></select>
			<input id="idb" type="hidden" name="idb" />
		</form>
	</div>
	
	<div id="bookAddDialog" title="Añadir ejemplares">
		<form id="bookAddForm">
			<label>Ejemplares nuevos:</label>
			<select id="mbkadd" name="ncp">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
			</select>
			<input id="aidb" type="hidden" name="idb" />
		</form>
	</div>
	<!-- Préstamo -->
	<div id="borrowDialog" title="Nuevo préstamo">
		<p id="borrError"></p>
		<form id="borrowForm" action=".">
			<label>Número de control:</label>
			<input type="text" name="memcd" id="memcd" />
			<label>Nombre:</label>
			<input type="text" name="memnm" id="memnm" />
			<label>Fecha de entrega:</label>
			<input type="text" name="findt" id="date" />
			<input type="hidden" name="idb" id="bbk" />
			<input type="hidden" name="uid" value="<?php echo $_SESSION['uid']; ?>" />
		</form>
	</div>
</div>
<script>
	var grid = $("#bookData").jqGrid({
		url: "control/book/response.php",
		datatype: "json",
		postData: {
			isSearch: function() { return $("#bkeys").val() != ''; },
			searchString: function() { return $("#bkeys").val(); }
		},
		colNames: ['Código', 'ISBN', 'Título', 'Autor', 'Editorial', 'Edición', 'Clasificación'],
		colModel: [
			{name:'code', index:'num', width: 50},
			{name:'isbn', index:'name', width: 100},
			{name:'title', index:'title', width: 230},
			{name:'author',	index:'author',	width: 200},
			{name:'editorial', index:'editorial', width: 100},
			{name:'edition', index:'edition', width: 50},
			{name:'name', index:'name', width: 170}
		],
		rowNum: 10,
		pager: "bookPager",
		viewrecords: true
	});
	$("#bookData").jqGrid('navGrid', '#bookPager',
			{ edit: false, add: false, del: false, search: false, refresh: true }
	);

	$("#modBookDialog").dialog({
		autoOpen: false,
		buttons: {
			"Guardar": function() {
				var clre = new RegExp("^[A-Z]{1}[0-9]+");
				var clre2 = new RegExp("^([0-9]{9,})");
				if (!clre.test($("#mbknum").val())) {
					$("#mbookError").empty().addClass("ui-state-error")
						.html("Escriba un código válido");
				}
				else if($("#mbkisbn").val() != '' &&
						!clre2.test($.trim($("#mbkisbn").val())) ){
					$("#mbookError").empty().addClass("ui-state-error")
						.html("Escriba un ISBN válido");
				}
				else if ($.trim($("#mbktitle").val()) == '' ||
						$.trim($("#mbkauthor").val()) == '' ||
						$.trim($("#mbkedit").val()) == '') {
					$("#mbookError").empty().addClass("ui-state-error")
						.html("Todos los campos son obligatorios");
				}
				else if(!isNumber($.trim($("#mbked").val()))) {
					$("#mbookError").empty().addClass("ui-state-error")
						.html("El campo edición debe ser numérico");
				}
				else {
					$("#modBookForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#mbookError").removeClass("ui-state-error").empty();
			$("#mbknum").val('');
			$("#mbkisbn").val('');
			$("#mbktitle").val('');
			$("#mbkauthor").val('');
			$("#mbkedit").val('');
			$("#mbkid").val(0);
		}
	});
	$("#newBookDialog").dialog({
		autoOpen: false,
		buttons: {
			"Añadir": function() {
				var clre = new RegExp("^[A-Z]{1}[0-9]+");
				var clre2 = new RegExp("^[0-9]{9,}");
				if (!clre.test($("#nbknum").val())) {
					$("#nbookError").empty().addClass("ui-state-error")
						.html("Escriba un código válido");
				}
				else if($("#nbkname").val() != '' &&
						!clre2.test($.trim($("#nbkname").val())) ){
					$("#nbookError").empty().addClass("ui-state-error")
						.html("Escriba un ISBN válido");
				}
				else if ($.trim($("#nbktitle").val()) == '' ||
						$.trim($("#nbkauthor").val()) == '' ||
						$.trim($("#nbkedit").val()) == '') {
					$("#nbookError").empty().addClass("ui-state-error")
						.html("Todos los campos son obligatorios");
				}
				else if(!isNumber($.trim($("#nbked").val()))) {
					$("#nbookError").empty().addClass("ui-state-error")
						.html("El campo edición debe ser numérico");
				}
				else {
					$("#newBookForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function() {
			$("#nbookError").removeClass("ui-state-error").empty();
			$("#nbknum").val('');
			$("#nbkname").val('');
			$("#nbktitle").val('');
			$("#nbkauthor").val('');
			$("#nbkedit").val('');
		}
	});
	$("#bookClDialog").dialog({
		autoOpen: false,
		buttons: {
			"Guardar": function() {
				$("#bookClForm").submit();
			},
			"Cancelar": function() {
				$(this).dialog('close');
			}
		}
	});
	$("#bookAddDialog").dialog({
		autoOpen: false,
		buttons: {
			"Guardar": function() {
				$("#bookAddForm").submit();
			},
			"Cancelar": function() {
				$(this).dialog('close');
			}
		}
	});
	$('#borrowDialog').dialog({
		autoOpen: false,
		buttons: {
			"Añadir": function() {
				var today = new Date();
				var find = $.datepicker.parseDate("yy-mm-dd", $("#date").val());
				if(!isNumber($.trim($("#memcd").val()))) {
					$("#borrError").empty().addClass('ui-state-error')
						.html("El campo número de control debe ser numérico");
				}
				else if($.trim($("#memnm").val()) == '') {
					$("#borrError").empty().addClass('ui-state-error')
						.html("Todos los campos son obligatorios");
				}
				else if(find == null) {
					$("#borrError").empty().addClass('ui-state-error')
						.html("Seleccione una fecha");
				}
				else if(find <= today) {
					$("#borrError").empty().addClass('ui-state-error')
						.html("Ingrese una fecha válida");
				} else {
					$("#borrowForm").submit();
					$(this).dialog('close');
				}
			}
		},
		beforeClose: function(event, ui) {
			$("#borrError").removeClass("ui-state-error").empty();
			$("#date").val('');
			$("#memnm").val('');
			$("#memcd").val('');
		}
	});
	$("#bookNew").button().click(function() {
		$.post("control/class/clresponse.php", function(data) {
			var opts = "";
			for(i = 0; i < data.length; i++) {
				opts += '<option value="'+data[i].idclass;
				opts += '">'+data[i].clmain+'.'+data[i].clsub;
				opts += ' '+data[i].name+'</option>';
			}
			$("#clSel").empty().html(opts);
		}, "json");
		$("#newBookDialog").dialog('open');
	});
	$("#bookRemove").button().click(function() {
		var id = $("#bookData").jqGrid("getGridParam", "selrow");
		if(id) {
			$.post("control/book/rmbook.php", {sid: id}, function(data) {
				displayMessage(data);
			}, "json");
		} else {
			alert("Seleccione un registro");
		}
	});
	$("#bookModif").button().click(function() {
		var id = $("#bookData").jqGrid("getGridParam", "selrow");
		if(id) {
			var ret = $("#bookData").jqGrid("getRowData", id);
			$("#modBookDialog").dialog('open');
			$("#mbknum").val(ret.code);
			$("#mbkisbn").val(ret.isbn);
			$("#mbktitle").val(ret.title);
			$("#mbkauthor").val(ret.author);
			$("#mbkedit").val(ret.editorial);
			$("#mbked").val(ret.edition);
			$("#mbkid").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	$("#filBut").button().click(function() {
		grid.trigger('reloadGrid');
	});
	$("#bookCl").button().click(function() {
		var id = $("#bookData").jqGrid("getGridParam", "selrow");
		if(!id) {
			alert("Seleccione un registro");
			return;
		}
		$.post("control/class/clresponse.php", function(data) {
			var opts = "";
			for(i = 0; i < data.length; i++) {
				opts += '<option value="'+data[i].idclass;
				opts += '">'+data[i].clmain+'.'+data[i].clsub;
				opts += ' '+data[i].name+'</option>';
			}
			$("#mbkcl").empty().html(opts);
		}, "json");
		$("#bookClDialog").dialog('open');
		$("#idb").val(id);
	});
	$("#bookAdd").button().click(function() {
		var id = $("#bookData").jqGrid("getGridParam", "selrow");
		if(id) {
			$("#bookAddDialog").dialog('open');
			$("#aidb").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	$("#bookBorrow").button().click(function() {
		var id = $("#bookData").jqGrid("getGridParam", "selrow");
		if(id) {
			$("#borrowDialog").dialog('open');
			$("#bbk").val(id);
		} else {
			alert("Seleccione un registro");
		}
	});
	$("#newBookForm").submit(function(event) {
		event.preventDefault();
		$.post("control/book/clean.php", $("#newBookForm").serialize(),
			function(data) {
				displayMessage(data);
			},
		"json");
	});
	$("#modBookForm").submit(function(event) {
		event.preventDefault();
		$.post('control/book/clean.php', $("#modBookForm").serialize(),
			function(data){
				displayMessage(data);
			},
		"json");
	});
	$("#bookClForm").submit(function(event) {
		event.preventDefault();
		$.post("control/class/clclean.php", $("#bookClForm").serialize(),
			function(data) {
				displayMessage(data);
			},
		"json");
	});
	$("#bookAddForm").submit(function(event) {
		event.preventDefault();
		$.post("control/book/addclean.php", $("#bookAddForm").serialize(),
			function(data) {
				displayMessage(data);
			},
		"json");
	});
	$("#borrowForm").submit(function(event) {
		event.preventDefault();
		$.post("control/borrow/bclean.php", $("#borrowForm").serialize(),
			function(data) {
				displayMessage(data, 1);
			},
		"json");
	});
</script>
