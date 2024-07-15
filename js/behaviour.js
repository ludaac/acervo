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
		}, "json");
		bc.fetch({
			'success': function(collection, response, options) {
				bv.render();
			},
			'error': function(c, r, o) {
				alert("No results");
			}
		});
	});
}

var BookModel = Backbone.Model.extend();

var BookCollection = Backbone.Collection.extend({
	url: 'control/search/response.php/book',
	model: BookModel
});

var BookView = Backbone.View.extend({
	el: "#result",

	render: function() {
		this.$el.empty();

		if (this.collection.length == 0)
			return this;

		var template = '<table class="result">';
		template += '<thead><tr class="ui-state-default">';
		template += "<th>Clasificación</th><th>Código</th>"
		template += "<th>ISBN</th><th>Título</th><th>Author</th>"
		template += "<th>Editorial</th><th>Edición</th><th>Disponibles</th>";
		template +=" </tr></thead>";
		template += "<tbody>";
		this.collection.each(function(model) {
			template += "<tr>";
			template += `<td>${model.get('clmain')}.${model.get('clsub')}</td>`;
			template += `<td>${model.get('code')}</td>`;
			template += `<td>${model.get('isbn')}</td>`
			template += `<td>${model.get('title')}</td>`;
			template += `<td>${model.get('author')}</td>`;
			template += `<td>${model.get('editorial')}</td>`;
			template += `<td>${model.get('edition')}</td>`;
			template += `<td>${model.get('av')}/${model.get('tt')}</td>`;
			template += "</tr>";
		}, this);
		template += "</tbody>";
		template += "</table>";
		this.$el.append(template);
		return this;
	}
});

var bc = undefined;
var bv = undefined;
$(document).ready(function () {
	bc = new BookCollection();
	bv = new BookView({collection: bc});

	bv.render();
})
