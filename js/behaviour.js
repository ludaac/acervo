var currentPageId = "#searchPage";

$.validator.addMethod(
	"regexp",
	function(value, element, regexp) {
		var re = new RegExp(regexp);
		return this.optional(element) || re.test(value);
	}
);

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function displayMessage(data, handler) {
	if(data == 100)
		alert("La acción se completó satisfactoriamente");
	else if(data == 150) {
		if(typeof handler == "undefined")
			alert("El código identificador ya existe");
		else
			alert("El libro no tiene ejemplares disponibles");
	}
	else if(data == 200)
		alert("Ocurrió un error al guardar. Intente de nuevo.");
	else
		alert(data);
}
/**
 * Inicializa todo tipo de componentes de la UI.
 */
$(document).ready(function() {
	loadDialogs();
	loadToolbar();
	loadButtons();
	loadEvents();
});

/**
 * Inicializa la barra de herramientas.
 */
function loadToolbar() {
	// Inicializa el botón de login.
	// Al presionarlo, se muestra el diálogo de login.
	$("#login").button({ icons: {primary: "ui-icon-home"}, text: "Entrar" })
		.click(function() { showDialog("#loginDialog"); });
		
	// Inicializa el botón de búsqueda.
	// Al presionarlo, se muestra la página de búsqueda.
	$("#search").button(
		{ icons: {primary: "ui-icon-search"}, text: "Búsqueda" }
	).click(function () {
		showNewPage("#searchPage");
	});
	
	// Inicializa el botón de clasificación.
	// Al presionarlo, se muestra la página de clasificaciones.
	$("#class").button(
		{ icons: {primary: "ui-icon-folder-collapsed"}, text: "Clasificaciones" }
	).click(function() {
		showNewPage("#classPage");
	});
	
	// 
	$("#books").button(
		{ icons: {primary: "ui-icon-bookmark"}, text: "Libros" }
	).click(function () {
		showNewPage("#bookPage");
	});
	
	$("#borrow").button(
		{ icons: {primary: "ui-icon-tag"}, text: "Préstamos" }
	).click(function() {
		showNewPage("#borrowPage");
	});
	$("#users").button(
		{ icons: {primary: "ui-icon-person"}, text: "Usuarios" }
	).click(function() {
		showNewPage("#userPage");
	});
	// TODO: Make a link with button.
	$("#exit").button(
		{ icons: {primary: "ui-icon-closethick"}, text: "Salir" }
	).click(function() {});
}

/**
 *
 */
function showDialog(dialogId) {
	$(dialogId).dialog('open');
	return false;
}

/**
 *
 */
function loadButtons() {
	// Botón de ayuda.
	$("#help").button(
		{ icons: {primary: "ui-icon-info"}, text: "Información" }
	).click(function() {
		$("#infoDialog").dialog('open');
		return false;
	});
	$("#date").datepicker({dateFormat: 'yy-mm-dd', showOn: "focus"});	
}

/**
 * Inicializa todos los diálogos a utilizar.
 */
function loadDialogs() {
	$("#loginDialog").dialog(
		{
			autoOpen: false, modal: true, resizable: false,
			buttons: {
				"Entrar": function() {
					if($("#user").val() == '' || $("#pswd").val() == '')
						$("#logMsg").empty().addClass("ui-state-error")
									.html("Ambos campos son obligatorios");
					else
						$("#logFrm").submit();					
					return false;
				}
			}
		}
	);
	$("#infoDialog").dialog( {autoOpen: false, minWidth: 340, maxWidth: 600});
	
}

/**
 *
 */
function showNewPage(pageId) {
	if(currentPageId == pageId)
		return false;

	$(currentPageId).fadeOut("slow");
	setTimeout(function() {
		$(pageId).fadeIn("slow");
	}, 700);
	
	if(currentPageId == '#searchPage') {
		$("#keys").val('');
		$("#result").empty();
	}
	
	currentPageId = pageId;
}

/**
 *
 */
function loadEvents() {
	$("#srch").submit(function(e) {
		e.preventDefault();
		
		if ($("#keys").val() == '')
			return false;
		
		$.post("control/search/response.php", $("#srch").serialize(),
		function(data) {	
			if (data.length == 0) {
				var msg = '<p class="ui-state-highlight ui-corner-all">'
				msg += 'No se encontraron resultados</p>';
				$('#result').empty().html(msg);
				return;
			}
			var table = createResultTable(data);
			$("#result").empty().html(table);
		}, "json");
	});
}

/**
 *
 */
 var datos;
function createResultTable(data) {
	datos = data;
	var html = "<table class='result'><thead><tr class='ui-state-default'>";
	html += "<th>Clasificación</th><th>Código</th>";
	html += "<th>ISBN</th><th>Título</th>";
	html += "<th>Autor</th><th>Editorial</th>";
	html += "<th>Edición</th><th>Disponibles</th>";
	html += "</tr><tbody>"
	for(i = 0; i < data.length; i++) {
		html += "<tr>";
		html += "<td>" + data[i].clmain + "." + data[i].clsub + "</td>";
		html += "<td>" + data[i].code + "</td>";
		if(data[i].isbn == null)
			html += "<td></td>";
		else
			html += "<td>" + data[i].isbn + "</td>";
		html += "<td>" + data[i].title + "</td>";
		html += "<td>" + data[i].author + "</td>";
		html += "<td>" + data[i].editorial + "</td>";
		html += "<td>" + data[i].edition + "</td>";
		html += "<td>" + data[i].av + " / " + data[i].tt + "</td>";
		html += "</tr>";
	}
	html += "</tbody></table>";
	return html;
}