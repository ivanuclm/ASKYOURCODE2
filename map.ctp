<link href="/css/recursos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/openseadragon/openseadragon.min.js"></script>
<script type="text/javascript" src="/js/openseadragon/plugins/openseadragon-svg-overlay/openseadragon-svg-overlay.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#reset_form").bind("click", function (event) {
		$('#buscarForm').find("input,select").val("").prop('checked', false).change();
		if ($('.select2_array')[0]) {
			$(".select2_array").select2("val",""); //descomentar en caso de usar select2 en el buscador (y poner clase select2_array al select2)
			$(".select2_array").val(null).trigger("change"); //descomentar en caso de usar select2 en el buscador (y poner clase select2_array al select2)
		}
		//$("#buscarForm").submit();
		
	});
});


</script>
<style>
.loading {
	position: relative;
	/* left: 1em; */
	bottom: 16em;
	font-size: 30px;
	z-index: 10;
}

.loading-text{
	position: relative;
	/* left: 20em; */
	bottom: 1.2em;
	font-size: 30px;
	z-index: 10;
	text-align: center;
}
</style>
		
<?php
$date = $this->Session->read('date');
$hora_inicio = $this->Session->read('hora_inicio');
$hora_fin = $this->Session->read('hora_fin');
$tipo_id = $this->Session->read('tipo_recurso');
$location_id = $this->Session->read('location');
?>

<script type="text/javascript">
$(document).ready(function() {
	$('.select2_location').val('<?php echo $location_id; ?>');
	$('.select2_location').trigger('change');

	$('.select2_tipo_recurso').val('<?php echo $tipo_id; ?>');
	$('.select2_tipo_recurso').trigger('change');

	$('#search_recurso').on("keyup", function() {
		var value = $(this).val().toLowerCase();
		$("#recursos_list button").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
});
</script>

<!-- <div class="row">
	<div class="col-xs-8 row-no-gutters">
		<div class="back_breadcrumb" style="margin-bottom:4px" ></div>
	</div>
	<div class="col-xs-4 text-right">

	</div>
</div> -->

<?php
foreach ($recursos as $recurso) {
	$available = 1;
	$parcial = 0;
	if(in_array($recurso['Recurso']['id'], $recurso_ids)){
			$available = 0;
			$parcial = 0;
		}
	elseif(in_array($recurso['Recurso']['id'], $recurso_parcial_ids)){
			$available = 0;
			$parcial = 1;
		}
	echo $this->element('view_modal_template_outside', array(
		"url_ajax" => array("controller" => "recursos", "action" => "getRecursoInfo", $recurso['Recurso']['id'], 1, $available, $parcial, $date, $hora_inicio, $hora_fin, $tipo_id, $location_id),
		"id_container_ajax" => 'viewtemplate_container' . $recurso['Recurso']['id'],
		"class_modal" => $recurso['RecursoTipo']['class'],
		"title_modal" => "<span class='fa " . $recurso['RecursoTipo']['icon'] . "'></span>&nbsp;" 
		. __('%s %s', $recurso['RecursoTipo']['name_' . $this->Session->read('Config.language')], $recurso['Recurso']['name_' . $this->Session->read('Config.language')]),
		"entidad" => "viewModal", 
		"id" => $recurso['Recurso']['id']
		));
}
?>
<div class="row">
	<div class="col-md-2 padding6" id="search_div">
		<div class="well well-sm">
			<?php
				echo $this->Form->create(null, array('id'=>'buscarForm','class'=>'form-inline','type'=>'get','url' => array()));
					echo $this->Form->select2('location', array("div"=>array("class"=>"form-group"),"class" => "input input-sm form-control select2_location", "label"=>"<span class='fa fa-map-marker'></span>&nbsp;".__("Ubicación").":&nbsp;",'options'=>$locations,'empty'=>__('Select Location'),'value'=>$location_id));
					echo $this->Form->select2('tipo_recurso', array("div"=>array("class"=>"form-group"),"class" => "input input-sm form-control select2_tipo_recurso", "label"=>"<span class='fa fa-desktop'></span>&nbsp;".__("Tipo de Recurso").":&nbsp;",'options'=>$tipos_recurso,'empty'=>__('Todos los tipos'),'value'=>$tipo_id));

					// echo $this->Form->datepicker('date_reserva_search',array("value"=>$date,"div"=>array('class'=>'form-group'),"id"=>"date_reserva","class"=>"form-control input input-sm input_min_width",'label' => "<span class='fa fa-calendar'></span>&nbsp;".__('Fecha Reserva'),"dateFormat"=>"dd-mm-yy", 'required' => true));
						echo $this->Form->datepicker('date_reserva_search',array("value"=>$date,"div"=>array('class'=>'form-group'),"id"=>"date_reserva","class"=>"form-control input input-sm input_min_width",'label' => "<span class='fa fa-calendar'></span>&nbsp;".__('Fecha Reserva').":&nbsp;","dateFormat"=>"dd-mm-yy", 'required' => true));

						// echo $this->Form->dateTimePicker('date_reserva_value', array("value"=>date('d-m-Y'), "label"=>false, "id"=>'date_reserva',"dateFormat"=>"dd-mm-yy","div"=>array("class"=>"input-group form-group"), "class" => "datetimepicker form-control",'placeholder'=>__('Start Date'), 'before'=>'<span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span><label>&nbsp;'.__("Pickup Date").'</label></span>') );
					// echo $this->Form->daterangepicker("range_id_search",array("label"=>__("Fechas").":&nbsp;","class" => "input form-control", "style" => array('height: 30px !important;')));

					echo $this->Form->datetimepicker('hora_inicio_search',array("value"=>$hora_inicio,"div"=>array('class'=>'form-group'),'id' => 'hora_inicio_reserva','label' => "<span class='fa fa-clock-o'></span>&nbsp;".__('Hora Inicio').":&nbsp;","format"=>"HH.mm", 'class'=>'form-control input input-sm input_min_width',"type"=>"text", 'required' => true));

					echo $this->Form->datetimepicker('hora_fin_search',array("value"=>$hora_fin,"div"=>array('class'=>'form-group'),'id' => 'hora_fin_reserva','label' => "<span class='fa fa-clock-o'></span>&nbsp;".__('Hora Fin').":&nbsp;","format"=>"HH.mm", 'class'=>'form-control input input-sm input_min_width',"type"=>"text", 'required' => true));
					

					// echo "<div class='form-group'>";
					// 	echo $this->Form->hidden('tipo_recurso', array('value' => $tipo_id));
					// echo "</div>";


					echo "<div class='form-group'>";
						echo $this->Form->button('<span aria-hidden="true" class="fa fa-refresh"></span>&nbsp;'.__('Ver disponibilidad'),array("escape"=>false,"class"=>"btn btn-success","role"=>"button","type"=>"submit","id"=>"boton_buscar_front", "style"=>""));
					echo '</div>';

					// echo "<div class='form-group'>";
					// 	echo $this->Form->button('<span aria-hidden="true" class="fa fa-times"></span>',array("id"=>"reset_form","escape"=>false,"class"=>"btn btn-danger","role"=>"button","type"=>"button"));
					// echo '</div>';
					echo $this->Form->end();					
			?>
		</div>
		
	</div>
	<div class="col-md-8 padding0 osd-div">
	
		<div id="openseadragon1" style="width: 100%; height: 50em; border: 2px solid black">
			
		</div>
		<div class="loading">
			<div class="loading-text">
				<?php echo __('Loading'); ?>...
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
			var tipo_classes = ['success', 'warning', 'info', 'danger','default'];

			function viewResource(resource_id, date_reserva, available, parcial) {
				console.log(resource_id);
				console.log("available " + available);
				console.log("parcial " + parcial);
				// Make an AJAX call to get resource information
				$.ajax({
					method: 'POST',
					data: {
						resource_id: resource_id, 
						available: available,
						parcial: parcial,
						date_reserva: date_reserva,
						date: '<?php echo $date; ?>',
						hora_inicio: '<?php echo $hora_inicio; ?>',
						hora_fin: '<?php echo $hora_fin; ?>',
						tipo_id: '<?php echo $tipo_id; ?>',
						location_id: '<?php echo $location_id; ?>'
					},
					dataType: "html",
					url: '<?php echo Router::url(array('controller' => 'recursos', 'action' => 'getRecursoInfo'), true); ?>',
					success: function (response) {
						// Handle the success response
						console.log('Recurso conseguido');
						$('#viewModal' + resource_id).modal('show');
						console.log('modal shown');
					},
					error: function (xhr, status, error) {
						$.notify({
							icon: 'fa fa-times',
							message: '<?php echo __("fallo viewResource") . " <b>#"; ?>' + status,
							title: '<?php echo "<b>" . __("ERROR") . ":</b>&nbsp;"; ?>'
						}, {
							type: "danger",
							placement: {
								from: "top",
								align: "right"
							},
							offset: 10
						});
						// Handle the error response
						console.log("Error: ", status, error);
					}
				});
			}

			var drag;
			var selectionMode = false;
			var background = 'rgba(255, 0, 0, 0.3)';
			var tipo_id = '1';

			var currentOverlay = null;
			var saveClicked = false;

			// Iterate over the resourceAreas array

			var viewer = OpenSeadragon({
				id: "openseadragon1",
				prefixUrl: "/js/openseadragon/images/",
				tileSources: [{
						type: 'image',
						url: '<?php echo $map_url; ?>',
						overlays: <?php echo json_encode($overlays); ?>
					}]
			});

			var overlays = <?php echo json_encode($overlays); ?>;

			function areAllFullyLoaded() {
			var tiledImage;
			var count = viewer.world.getItemCount();
			for (var i = 0; i < count; i++) {
				tiledImage = viewer.world.getItemAt(i);
				if (!tiledImage.getFullyLoaded()) {
					return false;
				}
			}
			return true;
			}

			var isFullyLoaded = false;

			function updateLoadingIndicator() {
				// Note that this function gets called every time isFullyLoaded changes, which it will do as you 
				// zoom and pan around. All we care about is the initial load, though, so we are just hiding the 
				// loading indicator and not showing it again. 
				if (isFullyLoaded) {
					document.querySelector('.loading').style.display = 'none';
				}
				}

				viewer.world.addHandler('add-item', function(event) {
				var tiledImage = event.item;
				tiledImage.addHandler('fully-loaded-change', function() {
					var newFullyLoaded = areAllFullyLoaded();
					if (newFullyLoaded !== isFullyLoaded) {
					isFullyLoaded = newFullyLoaded;
					updateLoadingIndicator();
					}
				});
			});
			
			var auxClass = "info";

			viewer.addHandler('canvas-click', function (event) {
				var position = viewer.viewport.pointFromPixel(event.position);

				overlays.forEach(function (overlay) {
					var viewportRect = new OpenSeadragon.Rect(
							overlay.x,
							overlay.y,
							overlay.width,
							overlay.height
							);

					if (viewportRect.containsPoint(position)) {
						console.log(overlay);
						console.log(overlay.available);
						// Get the resource ID associated with the clicked area
						var resourceId = overlay.resourceId;
						console.log(resourceId);

						var date_reserva = <?php echo $date; ?>;

						var available = overlay.available;
						var parcial = overlay.parcial;

						if(available){
							$('#reservar_modal' + resourceId).removeClass('hidden');
							console.log("show reservar_modal" + resourceId);
						}else if(parcial){
							$('#reservar_modal' + resourceId).addClass('hidden');
							console.log("hide reservar_modal" + resourceId);
						}{
							$('#reservar_modal' + resourceId).addClass('hidden');
							console.log("hide reservar_modal" + resourceId);
						}

						
						

						// Open the modal with the resource details
	//				openModalWithResource(resourceId);
						viewResource(resourceId, date_reserva, available, parcial);
						// Prevent default zoom in action
						event.preventDefaultAction = true;
					}
				});
			});


//			new OpenSeadragon.MouseTracker({
//				element: viewer.element,
//				pressHandler: function (event) {
//					if (!selectionMode) {
//						return;
//					}
//
//					var overlayElement = document.createElement('div');
//					overlayElement.style.background = background;
//					var viewportPos = viewer.viewport.pointFromPixel(event.position);
//					viewer.addOverlay(overlayElement, new OpenSeadragon.Rect(viewportPos.x, viewportPos.y, 0, 0));
//
//					currentOverlay = overlayElement; // Store the overlay element
//
//					drag = {
//						overlayElement: overlayElement,
//						startPos: viewportPos
//					};
//				},
//				dragHandler: function (event) {
//					if (!drag) {
//						return;
//					}
//
//					var viewportPos = viewer.viewport.pointFromPixel(event.position);
//					var diffX = viewportPos.x - drag.startPos.x;
//					var diffY = viewportPos.y - drag.startPos.y;
//
//					var location = new OpenSeadragon.Rect(
//							Math.min(drag.startPos.x, drag.startPos.x + diffX),
//							Math.min(drag.startPos.y, drag.startPos.y + diffY),
//							Math.abs(diffX),
//							Math.abs(diffY)
//							);
//
//					viewer.updateOverlay(drag.overlayElement, location);
//				},
//				releaseHandler: function (event) {
//					var viewportPos = viewer.viewport.pointFromPixel(event.position);
//					var topLeftX = drag.startPos.x;
//					var topLeftY = drag.startPos.y;
//					var diffX = viewportPos.x - drag.startPos.x;
//					var diffY = viewportPos.y - drag.startPos.y;
//
//					var coordinates = {
//						x: Math.min(drag.startPos.x, drag.startPos.x + diffX),
//						y: Math.min(drag.startPos.y, drag.startPos.y + diffY),
//						width: Math.abs(diffX),
//						height: Math.abs(diffY)
//					};
//
//					console.log("releaseHandler");
//					console.log(coordinates.x);
//					console.log(typeof (coordinates.x));
//					console.log(coordinates.y);
//					console.log(coordinates.width);
//					console.log(coordinates.height);
//
//					saveX = coordinates.x;
//					saveY = coordinates.y;
//					saveWidth = coordinates.width;
//					saveHeight = coordinates.height;
//
//
//					drag = null;
//					selectionMode = false;
//					viewer.setMouseNavEnabled(true);
//
//					// Open the Bootstrap modal after selection is done
//					// Call the function to open the modal with coordinates
//					openModalWithCoordinates(coordinates, tipo_id, tipo_class, tipo_name);
//	//				$('#myModal').modal('show');
//				}
//			});
			
			var tipoId_old = 0;
			var open = false;
			

//			$('#myModal').on('hidden.bs.modal', function () {
//				var myForm = document.getElementById('form_recurso');
//				myForm.reset();
//			});

			$('#viewModal').on('hidden.bs.modal', function () {
				$('#div_view_modal_header').removeClass('modal-header-success');
				$('#div_view_modal_header').removeClass('modal-header-warning');
				$('#div_view_modal_header').removeClass('modal-header-info');
				$('#div_view_modal_header').removeClass('modal-header-danger');
			});

			$('.btn-resource').on('click', function () {
				var $this = $(this);
				var resourceId = parseInt($(this).attr('id').split('_')[1]);
				console.log("click");
				console.log(resourceId);

				// Look for the corresponding overlay
				var overlay = overlays.find(function (overlay) {
					console.log(overlay.resourceId);
					return overlay.resourceId === resourceId;
				});
				
				// If the button is currently selected, unselect it and zoom out
				if ($this.hasClass('btn-resource-selected')) {
					$this.removeClass('btn-resource-selected');
					viewer.viewport.goHome();  // Zoom out to original view
				}
				// Otherwise, select it and zoom in
				else {
					// First, unselect all other buttons
					$('.btn-resource').removeClass('btn-resource-selected');
					// Then, select the clicked button
					$this.addClass('btn-resource-selected');

					if (overlay) {
						// 10% margin
						var margin = 0.1 * Math.max(overlay.width, overlay.height);
						var overlayRect = new OpenSeadragon.Rect(overlay.x - margin, overlay.y - margin, overlay.width + 2 * margin, overlay.height + 2 * margin);

						viewer.viewport.fitBounds(overlayRect);
					}
				}
			});

		});
		</script>
	</div>
	<div class='col-md-2'>
	<?php
		if(isset($recursos) && $recursos){
			echo $this->Form->input('search_recurso', array('label' => false, 'div' => array('class' => 'form-group'), 'class' => 'form-control input', 'placeholder' => __('Busca un recurso...'), 'id' => 'search_recurso', 'type' => 'text', 'required' => false));

			echo '<div id="recursos_list" style="overflow:scroll; overflow-x: hidden; overflow-y: auto; height:47em;">';

			foreach ($recursos as $id => $recurso) {
				echo "<button type='button' class='btn btn-block btn-resource btn-resource-" . $recurso['RecursoTipo']['class'] . "' id='resource_" . $recurso['Recurso']['id'] . "'>";
				echo $recurso['Recurso']['name_' . $this->Session->read('Config.language')];
				echo "</button>";
			}

			echo '</div>';
		}
		?>
	</div>
</div>

