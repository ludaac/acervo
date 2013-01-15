<div id="borrowPage" class="grid_12 alpha omega" style="display: none;">
	<div class="grid_12 alpha omega" style="margin: 2em 0em;">
		<table id="borrowData"></table>
		<div id="borrowPager"></div>
	</div>
	<div class="control grid_2 push_5">
		<button id="borrowFree">Liberar</button>
	</div>
</div>
<script>
	$("#borrowData").jqGrid({
		url: "control/borrow/response.php",
		datatype: "json",
		colNames: ['Num. control', 'Nombre', 'Fecha inicio', 'Fecha final', 'Título', 'Ejemplar', 'Autorizó', 'Estado'],
		colModel: [
			{name:'mem_code', index:'mem_code', width: 75},
			{name:'mem_name', index:'mem_name', width: 150},
			{name:'init_date', index:'init_date', width: 85},
			{name:'final_date', index:'final_date', width: 80},
			{name:'title', index:'title', width: 220},
			{name:'copy_num', index:'copy_num', width: 60},
			{name:'usfname', index:'usfname', width: 150},
			{name:'status', index:'status', width: 75}
		],
		rowNum: 10,
		pager: "borrowPager",
		viewrecords: true
	});
	$("#borrowData").jqGrid('navGrid', '#borrowPager',
			{ edit: false, add: false, del: false, search: false, refresh: true });
	$("#borrowFree").button().click(function() {
		var idb = $("#borrowData").jqGrid("getGridParam", "selrow");
		if(idb) {
			$.post("control/borrow/tclean.php", {id: idb},
				function(data) {
					displayMessage(data);
				}
			);
		} else {
			alert("Seleccione un registro");
		}
		
	});
</script>