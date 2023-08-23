<style>
	.resourcemodal img {
		max-width: 100% !important;
		height: auto;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){	
		$('.fancybox').fancybox({ helpers: {
				overlay: {
				locked: false
				}
			},
			beforeShow : function() {
				var alt = this.element.find('img').attr('alt');
				this.inner.find('img').attr('alt', alt);
				this.title = alt;
			}
		});
		
		$(".description-div a").fancybox({ 
			animationEffect : 'fade'
		}).attr('data-fancybox', 'group-modal');
		
		$('[data-fancybox="group-modal"]').fancybox({
				helpers: {
					overlay: {
						locked: false
						}
					},
				beforeShow : function() {
					var alt = this.element.find('img').attr('alt');
					this.inner.find('img').attr('alt', alt);
					this.title = alt;
				}
			});

		$(".readonly").keydown(function(e){
			e.preventDefault();
		});

		$('.datetimepicker').datetimepicker({inline: false,sideBySide:true,locale:'es',ignoreReadonly: true});
		$('.datetimepicker_inicio').data("DateTimePicker").date(new Date("<?php echo $correctDatetimeStart;?>"));
		$('.datetimepicker_fin').data("DateTimePicker").date(new Date("<?php echo $correctDatetimeEnd;?>"));


	});
</script>
<div class="resourcemodal" id="resourcemodal<?php echo $datos['Recurso']['id']; ?>">

<div class="row">
<div class="col-md-7">
<dl class="dl-horizontal">
	<dt><?php echo __('ID'); ?></dt>
	<dd>
		<?php echo "#".$datos['Recurso']['id']; ?>
		&nbsp;
	</dd>
	<dt><?php echo __('Created'); ?></dt>
	<dd>
		<?php echo "<span class='fa fa-clock-o'></span>&nbsp;" .date("d/m/Y H:i:s",strtotime($datos["Recurso"]["created"])); ?>
		&nbsp;
	</dd>
	
	<dt><?php echo __('Limit date'); ?></dt>
	<dd>
		<?php 
		if(isset($datos['Recurso']['fecha_fin']) && $datos['Recurso']['fecha_fin'])
			echo "<span class='fa fa-calendar'></span>&nbsp;" .date("d/m/Y",strtotime($datos['Recurso']['fecha_fin'])); 
		else
			echo "-";
			
		?>
		&nbsp;
	</dd>
	<dt><?php echo __('Title'); ?></dt>
	<dd class='bold light-gray2 padding4'>
		<?php echo  $datos['Recurso']['name_'. $this->Session->read('Config.language')]; ?>
		&nbsp;
	</dd>

</dl>
</div>
<div class="col-md-5">
<?php 
	if($available && !$parcial){ ?>
		<script type="text/javascript">
			$(document).ready(function(){
				
				
				
				$(document).on('click', "#<?php echo 'button_send_modal'.$datos['Recurso']['id']; ?>" , function() {
					$.isLoading({ text: "<?php echo __("Loading"); ?>" });
				});
				
			});
		</script>
			
		
			<button class="btn btn-success btn-block btn-lg btn-reservar-modal" data-toggle="collapse" data-target="#popover-content-modal<?php echo $datos['Recurso']['id']; ?>" aria-expanded="false" aria-controls="popover-content-modal<?php echo $datos['Recurso']['id']; ?>"><?php echo "<span class='fa fa-book'></span>&nbsp;" . __("Reservar"); ?></button>

			<div id="popover-content-modal<?php echo $datos['Recurso']['id']; ?>" class="well well-sm collapse">
			  <?php
			  
			  echo $this->Form->create('RecReserva', array("url"=>array('controller'=>'rec_reservas','action'=>'edit',null),'id'=>'send_answer_form'.$datos['Recurso']['id']));

			  echo $this->Form->hidden('user_id', array('value' => $this->Session->read('Auth.User.id')));
			  echo $this->Form->hidden('recurso_id', array('value' => $datos['Recurso']['id']));
			  echo $this->Form->hidden('tipo_id', array('value' => $datos['Recurso']['tipo_id']));
			  echo $this->Form->hidden('location_id', array('value' => $datos['Recurso']['location_id']));
			  echo $this->Form->dateTimePicker('datetime_inicio',array('class'=>'form-control datetimepicker datetimepicker_inicio readonly','format'=>'YYYY-MM-DD HH:mm:ss', 'value'=>$correctDatetimeStart, 'label' => "<span class='fa fa-calendar'></span>&nbsp;" . __("Fecha y hora inicio"), 'id' => 'datetimepicker_inicio'.$datos['Recurso']['id'], 'placeholder' => __("Fecha inicio"), 'div' => array('class' => 'input-group form-group')));
				echo $this->Form->dateTimePicker('datetime_fin',array('class'=>'form-control datetimepicker datetimepicker_fin readonly','format'=>'YYYY-MM-DD HH:mm:ss', 'value'=>$correctDatetimeEnd, 'label' => "<span class='fa fa-calendar'></span>&nbsp;" . __("Fecha y hora fin"), 'id' => 'datetimepicker_fin'.$datos['Recurso']['id'], 'placeholder' => __("Fecha fin"), 'div' => array('class' => 'input-group form-group')));

				// echo $this->Form->input('fecha_reserva_disabled', array('value'=>$date,  'class' => 'form-control fecha-reserva-input', 'id' => 'fecha-input-'.$datos['Recurso']['id'], 'disabled' => true, 'label' => "<span class='fa fa-calendar'></span>&nbsp;" . __("Fecha")));
			//   echo $this->Form->hidden('fecha_reserva', array('value' => $date));
			//   echo $this->Form->input('hora_inicio_disabled', array('value'=>$hora_inicio, 'class' => 'form-control hora-inicio-input', 'id' => 'hora-inicio-input-'.$datos['Recurso']['id'], 'disabled' => true, 'label' => "<span class='fa fa-clock-o'></span>&nbsp;" . __("Hora inicio")));
			//   echo $this->Form->hidden('hora_inicio', array('value' => $hora_inicio));
			//   echo $this->Form->input('hora_fin_disabled', array('value'=>$hora_fin, 'class' => 'form-control hora-fin-input', 'id' => 'hora-fin-input-'.$datos['Recurso']['id'], 'disabled' => true, 'label' => "<span class='fa fa-clock-o'></span>&nbsp;" . __("Hora fin")));
			//   echo $this->Form->hidden('hora_fin', array('value' => $hora_fin)); 


			// echo $this->Form->daterangepicker("range_id_search",array("label"=>__("Fechas").":&nbsp;","class" => "input form-control", "style" => array('height: 30px !important;')));

			  echo $this->Form->input('observaciones', array('class' => 'form-control', 'id' => 'obs_reservar_modal'.$datos['Recurso']['id'], 'placeholder' => __("Comments")."...", 'type' => 'text', 'rows' => '2'));


			  ?>

				<div class="form-group col-xs-6">
					<button id="<?php echo 'button_send_modal'.$datos['Recurso']['id']; ?>" type="submit" class="btn btn-success btn-lg"><?php echo __("Reservar").">>";  ?></button> 
				</div>                        
				
				&nbsp;
				<br>
				&nbsp;
			 <?php
			 echo $this->Form->end();
			 ?>
			 
			</div>

	<?php }elseif($parcial){
			if(isset($reservas_parcial) && $reservas_parcial){
				// debug($reservas_parcial);
				$preplural = "s siguientes " . count($reservas_parcial);
				$postplural = "s";
				if(count($reservas_parcial) == 1){
					$preplural = " siguiente ";
					$postplural = "";
				}
				echo "<div class='alert alert-warning'><span class='fa fa-calendar'></span>&nbsp;" . __("Recurso disponible parcialmente para las fechas seleccionadas por la%s reserva%s:", $preplural, $postplural);
				echo "<ul>";
				foreach($reservas_parcial as $i => $reserva_parcial){
					echo "<li>" . __("%s <br><b>%s <span class='fa fa-long-arrow-right'></span> %s</b>", $reserva_parcial['User']['name'] ,date("d/m/Y H:i",strtotime($reserva_parcial['RecReserva']['datetime_inicio'])), date("d/m/Y H:i",strtotime($reserva_parcial['RecReserva']['datetime_fin']))) . "</li>";
					if($i == 5){
						echo "<li>...</li>";
						break;
					}
				}
				echo "</ul>";
				echo "</div>";
				//Button that redirects to Recursos/calendar/resourceID
				echo "<a href='" . $this->Html->url(array('controller' => 'recursos', 'action' => 'calendar', $datos['Recurso']['id'])) . "' class='btn btn-success btn-block btn-lg btn-reservar-modal'><span class='fa fa-calendar'></span>&nbsp;" . __("Ver calendario") . "</a>";
			}
			else{
				echo "NO DISPONIBLE PARCIALMENTE";
				debug($datos);
			}
		}else{
			if(isset($reservas) && $reservas){
				// debug($reservas);
				$preplural = "s siguientes " . count($reservas);
				$postplural = "s";
				if(count($reservas) == 1){
					$preplural = " siguiente ";
					$postplural = "";
				}
				echo "<div class='alert alert-danger'><span class='fa fa-calendar'></span>&nbsp;" . __("Recurso no disponible para las fechas seleccionadas por la%s reserva%s:", $preplural, $postplural);
				echo "<ul>";
				foreach($reservas as $i => $reserva){
					echo "<li>" . __("%s <br><b>%s <span class='fa fa-long-arrow-right'></span> %s</b>", $reserva['User']['name'] ,date("d/m/Y H:i",strtotime($reserva['RecReserva']['datetime_inicio'])), date("d/m/Y H:i",strtotime($reserva['RecReserva']['datetime_fin']))) . "</li>";
					if($i == 5){
						echo "<li>...</li>";
						break;
					}
				}
				echo "</ul>";
				echo "</div>";
			}
			else{
				echo "NO DISPONIBLE";
				debug($datos);
			}
		
		// if(isset($datos['Recurso']['fecha_fin']) && $datos['Recurso']['fecha_fin']){
		// 	echo "<div class='alert alert-danger'><span class='fa fa-calendar'></span>&nbsp;" . __("Recurso no disponible hasta el %s", date("d/m/Y",strtotime($datos['Recurso']['fecha_fin']))) . "</div>";
		// }
	} ?>
</div>

</div>


<div class="row">
	<div class="col-md-10">
		<dl class="dl-horizontal description-div">
			<dt><?php echo __('Description'); ?></dt>
			<dd>
				<?php 
				
				$content_info = $datos['Recurso']['description'];
				$id_recurso = $datos['Recurso']['id'];

				$pattern = '/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i';

					$imageCounter = 0;
					$imageTotal = substr_count($content_info, '<img'); // Count total img tags in content

					if($imageTotal > 0){
						$newDescription = preg_replace_callback($pattern, function($matches) use($id_recurso, &$imageCounter, $imageTotal) {
							$imageTag = $matches[0];
							$imageSrc = $matches[1];
							$imageCounter++; // Increment counter for each img
	
							$altText = __("Imagen") . " {$imageCounter}/{$imageTotal}"; // Construct alt text
	
							// Add the alt attribute
							$imageTag = str_replace('<img', '<img alt="'.$altText.'"', $imageTag);
	
							$linkTag = '<a href="' . $imageSrc . '" data-fancybox="group-modal" rel="grupo_recurso_' . $id_recurso . '">' . $imageTag . '</a>';
							return $linkTag;
						}, $content_info);
					
						echo $newDescription;
					}else{
						echo $content_info;
					}
					?>
				&nbsp;
			</dd>
		</dl>
	</div>
	<div class="col-md-2">
		&nbsp;
	</div>
</div>
</div>


