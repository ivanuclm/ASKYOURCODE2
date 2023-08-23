<?php
App::uses('AppController', 'Controller');
/**
 * Recursos Controller
 *
 * @property Recurso $Recurso
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class RecursosController extends AppController {
	
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash', 'Search.Prg');


	public $paginate = array(
        'limit' => 100,
		'maxLimit' => 500,
        'order' => array(
           '.id' => 'ASC'
        )
	);
	
public function beforeFilter()
	{
		parent::beforeFilter();
		$this->set('title_for_layout', __('Recursos'));
		$this->layout="default";

		if($this->request->params['action']=="index"){
			$page="1";
			$sort="";
			$direction="";
			$limit="25";
			$cadena_previa=array();
			if(isset($this->request->params['named']['page'])){ $page=$this->request->params['named']['page']; $cadena_previa[]="page:".$page; }
			if(isset($this->request->params['named']['sort'])){ $sort=$this->request->params['named']['sort']; $cadena_previa[]="sort:".$sort; }
			if(isset($this->request->params['named']['direction'])){ $direction=$this->request->params['named']['direction']; $cadena_previa[]="direction:".$direction; }
			if(isset($this->request->params['named']['limit'])){ $limit=$this->request->params['named']['limit']; $cadena_previa[]="limit:".$limit; }

			$cad_previa_string=implode("/",$cadena_previa);
			$cad=array();

			foreach($this->request->query as $key=>$value){
				if(is_array($value)){
					foreach($value as $_keysub=>$_valuesub){
						$cad[]=$key."[]=".$_valuesub;
					}
				}else{
					$cad[]=$key."=".$value;
				}
			}

			$cad_salida=implode("&",$cad);
			if($cad_salida)
				$salida_final=$cad_previa_string."?".$cad_salida;
			else
				$salida_final=$cad_previa_string;

			$this->Session->write("Recursoview.params",$salida_final);

		}

	}

        function index_emp(){
            $this->index(1);
            $this->render("index");
        }

        function index_adm(){
            $this->index(null);
            $this->render("index");
        }

        function index($front = null){
            $this->Prg->commonProcess();
            $vars_search=$this->Prg->parsedParams();

			$this->layout="administracion";

            if($front){
                //index_emp
                    $this->layout="default_limpio";
                    $this->set('container_fluid', true);
                    $this->Navegacion->addToScrumb(__("Recursos Empleado"), "/recursos/map");
            }else{
                //index_adm
                    $this->Navegacion->addToScrumb(__("Recursos Admin"), "/recursos/map");
            }


            //PARA SACAR LAS COMPANIES DONDE TENGO PERMISOS
            $companies_rol=$this->Funcion->getCompaniesByAction('controllers/' . $this->request->params['controller'] . '/' .$this->request->params['action']);
            $companies = $this->Company->find('list',array('recursive' => -1,"conditions"=>array("Company.id"=>$companies_rol)));


//             $this->Paginator->settings=$this->paginate;
// //            $this->Paginator->settings['order']=array("EciTalon.talon_num DESC", "EciTalon.comision ASC");
//            $this->Paginator->settings['conditions'] = $this->Recurso->parseCriteria($vars_search);


			
			
			$user_id=$this->Session->read('Auth.User.id');

            if($front){
                //index_emp

            }else{
                //index_adm
            }



            $this->set(compact('companies', 'companies_rol','front','user_id'));

            $this->set("title", "<span class='fa fa-plane'></span>&nbsp;" .__("Listado de Recursos"));

            //para el excel
            $this->Session->write('Exportrecursos.datos_conditions', $this->Paginator->settings);
		}

		/*
		* MAP FUNCTION
		*/
		
		function map($tipo_id = null, $location_id = 1){
			$this->Prg->commonProcess();
            $vars_search=$this->Prg->parsedParams();
			$this->set("vars_search", $vars_search);

			$date = date('d-m-Y');
			if($this->request->query('date_reserva_search')){
				$date = $this->request->query('date_reserva_search');
			}
			$this->Session->write('date', $date);

			$dateObj = DateTime::createFromFormat('d-m-Y', $date);
			$correctDate = $dateObj->format('Y-m-d'); // Convert to 'Y-m-d' format



			$hora_inicio = date('H.i');
			if($this->request->query('hora_inicio_search')){
				$hora_inicio = $this->request->query('hora_inicio_search');
			}
			$this->Session->write('hora_inicio', $hora_inicio);

			$hora_fin = date('H.i', strtotime('+6 hour'));
			if($this->request->query('hora_fin_search')){
				$hora_fin = $this->request->query('hora_fin_search');
			}
			$this->Session->write('hora_fin', $hora_fin);

			if($this->request->query('tipo_recurso')){
				$tipo_id = $this->request->query('tipo_recurso');
			}
			$this->Session->write('tipo_recurso', $tipo_id);

			if($this->request->query('location')){
				$location_id = $this->request->query('location');
			}
			$this->Session->write('location', $location_id);

			
			$datetime_start = DateTime::createFromFormat('d-m-Y H.i', $date . ' ' . $hora_inicio);
			$correctDatetimeStart = $datetime_start->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format

			
			$datetime_end = DateTime::createFromFormat('d-m-Y H.i', $date . ' ' . $hora_fin);
			$correctDatetimeEnd = $datetime_end->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format

			//if the end date is before the start date, add one day to the end date
			if($datetime_end < $datetime_start){
				$datetime_end->modify('+1 day');
				$correctDatetimeEnd = $datetime_end->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format
			}


			

			$this->set(compact('date', 'correctDate', 'hora_inicio', 'correctDatetimeStart', 'hora_fin', 'correctDatetimeEnd', 'tipo_id', 'location_id'));

		


				
			// $reservas = $this->Recurso->RecReserva->find('all', array('recursive' => -1, 'contain' => array('Recurso', 'User'), 'conditions' => array('fecha' => $correctDate, 'hora_inicio <=' => $hora_inicio, 'hora_fin >=' => $hora_fin)));
			$reservas = $this->Recurso->RecReserva->find('all', array(
				'recursive' => -1, 
				'contain' => array('Recurso', 'User'), 
				'conditions' => array(
					'OR' => array(
								array(
									'datetime_inicio <=' => $correctDatetimeStart,
									'datetime_fin >' => $correctDatetimeStart
								),
								array(
									'datetime_inicio <' => $correctDatetimeEnd,
									'datetime_fin >=' => $correctDatetimeEnd
								),
								array(
									'datetime_inicio >=' => $correctDatetimeStart,
									'datetime_fin <=' => $correctDatetimeEnd
								)
							)
				)
			));

			//extract the IDs of the resources that are reserved
			$recurso_ids = array();
			$recurso_parcial_ids = array();
			$conflictThreshold = 60; //minutes
			foreach($reservas as $reserva){

				if($this->isPartialTimeConflict($reserva['RecReserva']['datetime_inicio'], $reserva['RecReserva']['datetime_fin'], $correctDatetimeStart, $correctDatetimeEnd)){
					$recurso_parcial_ids[] = $reserva['Recurso']['id'];
				}
				else{
					$recurso_ids[] = $reserva['Recurso']['id'];
				}
				//check if $reserva['RecReserva']['datetime_inicio'] is 60 minutes after $correctDatetimeStart or $reserva['RecReserva']['datetime_fin'] is 60 minutes before $correctDatetimeEnd
				//if so, then the resource is only partially reserved
				// $conflictStart = new DateTime($reserva['RecReserva']['datetime_inicio']);
				// $conflictEnd = new DateTime($reserva['RecReserva']['datetime_fin']);
				// $resourceId = $reserva['Recurso']['id'];
				
				// // Calculate the minute difference between dates
				// $startTimestamp = $conflictStart->getTimestamp();
				// $endTimestamp = $conflictEnd->getTimestamp();
				// $correctStartTimestamp = strtotime($correctDatetimeStart);
				// $correctEndTimestamp = strtotime($correctDatetimeEnd);

				// $startMinutes = abs(($startTimestamp - $correctStartTimestamp) / 60);
				// $endMinutes = abs(($endTimestamp - $correctEndTimestamp) / 60);


				// CakeLog::write('debug', 'datetime_inicio: ' . $reserva['RecReserva']['datetime_inicio'] . ' - datetime_fin: ' . $reserva['RecReserva']['datetime_fin']);
				// CakeLog::write('debug', 'correctDatetimeStart: ' . $correctDatetimeStart . ' - correctDatetimeEnd: ' . $correctDatetimeEnd);
				// CakeLog::write('debug', 'startMinutes: ' . $startMinutes);
				// CakeLog::write('debug', 'endMinutes: ' . $endMinutes);
				// CakeLog::write('debug', 'startTimestamp: ' . $startTimestamp);
				// CakeLog::write('debug', 'endTimestamp: ' . $endTimestamp);
				// CakeLog::write('debug', 'correctStartTimestamp: ' . $correctStartTimestamp);
				// CakeLog::write('debug', 'correctEndTimestamp: ' . $correctEndTimestamp);
				// CakeLog::write('debug', '-----------------------------------------------------------------------------------');
				
				
				// if ($startMinutes <= $conflictThreshold || $endMinutes <= $conflictThreshold) {
				// 	// Resource is "partial available" (conflict is 60 minutes or more away from start or end)
				// 	$recurso_parcial_ids[] = $resourceId;
				// 	CakeLog::write('debug', 'Resource ' . $resourceId . ' is partial available');
				// 	CakeLog::write('debug', '-----------------------------------------------------------------------------------');
				// }
				// else{
				// 	$recurso_ids[] = $resourceId;
				// }
			}
			$this->Session->write('recurso_ids', $recurso_ids);
			$this->set(compact('reservas', 'recurso_ids'));

			// $reservas_parcial = $this->Recurso->RecReserva->find('all', array(
			// 	'recursive' => -1, 
			// 	'contain' => array('Recurso', 'User'), 
			// 	'conditions' => array(
			// 		'Recurso.id NOT' => $recurso_ids,
			// 		'OR' => array(
			// 			array(
			// 				'datetime_inicio <=' => $correctDatetimeStart,
			// 				'datetime_fin >' => $correctDatetimeStart
			// 			),
			// 			array(
			// 				'datetime_inicio <' => $correctDatetimeEnd,
			// 				'datetime_fin >=' => $correctDatetimeEnd
			// 			)
			// 		)
			// 	)
			// ));

			// $recurso_parcial_ids = array();
			// foreach($reservas_parcial as $reserva){
			// 	$recurso_parcial_ids[] = $reserva['Recurso']['id'];
			// }
			$this->Session->write('recurso_parcial_ids', $recurso_parcial_ids);
			$this->set(compact('recurso_parcial_ids'));


//			$this->layout="default";
			$this->layout="default_limpio";
            $this->set('container_fluid', true);
			$this->Navegacion->addToScrumb(__("Reservas - Mapa"), "/recursos/map");


            //PARA SACAR LAS COMPANIES DONDE TENGO PERMISOS
            $companies_rol=$this->Funcion->getCompaniesByAction('controllers/' . $this->request->params['controller'] . '/' .$this->request->params['action']);

			$user_id=$this->Session->read('Auth.User.id');


			$tipo = null;			
			$conditions = array();
			$conditions_aux = array();
			$conditions_areas = array();
			$title = __("Recursos");
			if(isset($tipo_id) && $tipo_id){
				$conditions[] = array('RecursoTipo.id' => $tipo_id);
				$conditions_aux[] = array('Recurso.RecursoTipo.id' => $tipo_id);
				$conditions_areas[] = array('Recurso.tipo_id' => $tipo_id);
				$tipo = $this->Recurso->RecursoTipo->find("first", array('recursive' => -1, 'conditions' => $conditions));
				$title = $tipo['RecursoTipo']['names_' . $this->Session->read('Config.language')];
			}

			$location = null;
			
			if(isset($location_id) && $location_id){
				$conditions[] = array('Recurso.location_id' => $location_id);
				$conditions_aux[] = array('Recurso.location_id' => $location_id);
				$conditions_areas[] = array('Recurso.location_id' => $location_id);
				$location = $this->Recurso->Location->find("first", array('recursive' => -1, 'conditions' => array('Location.id' => $location_id)));
			}

			$conditions[] = array('Recurso.active' => '1');
			$conditions[] = array('Recurso.fantasma' => '0');
			
			$this->set("tipo", $tipo);
			
			$recursos = $this->Recurso->find("all", array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'), 'conditions' => $conditions, 'order' => array('tipo_id' => 'DESC', 'puesto_num' => 'ASC')));
			$this->set("recursos", $recursos);
			
			$areas = $this->Recurso->RecursoMapArea->find("all", array('recursive' => -1, 'contain' => array('Recurso', 'Recurso.RecursoTipo'), 'conditions' => $conditions_areas));
			$overlayList = [];

			foreach($areas as $area){
				//if area has a resourceId inside the $reservas, then it's not available, so $class = "not-available"
				$class = "available";
				$available_area = 1;
				$parcial_area = 0;
				if(in_array($area['Recurso']['id'], $recurso_ids)){
					$available_area = 0;
					$class = "not-available";
				}
				elseif(in_array($area['Recurso']['id'], $recurso_parcial_ids)){
					$parcial_area = 1;
					$class = "partial-available";
				}

				$overlay = [
					'id' => 'example-overlay' . $area['RecursoMapArea']['id'],
					'x' => (float) $area['RecursoMapArea']['topleftX'],
					'y' => (float) $area['RecursoMapArea']['topleftY'],
					'width' => (float) $area['RecursoMapArea']['diffX'],
					'height' => (float) $area['RecursoMapArea']['diffY'],
					'className' => 'area-' . $class,
					'resourceId' => $area['Recurso']['id'],
					'recursoTipo' => $area['Recurso']['RecursoTipo']['id'],
					'available' => $available_area,
					'parcial' => $parcial_area,
				];

				$overlayList[] = $overlay;
			}
			$this->set("overlays", $overlayList);


			$map_url = "/img/planos/PCYT_v5.jpg";
			if(isset($location) && $location){
				$map_url = $location['Location']['map_url'];
			}

			$this->set("map_url", $map_url);

			$locations = $this->Recurso->Location->find("list", array('recursive' => -1, 'fields' => array('id', 'name_' . $this->Session->read('Config.language'))));
			$this->set("locations", $locations);

			$tipos_recurso = $this->Recurso->RecursoTipo->find("list", array('recursive' => -1, 'fields' => array('id', 'names_' . $this->Session->read('Config.language')), 'conditions' => array('RecursoTipo.active' => '1')));
			$this->set("tipos_recurso", $tipos_recurso);

			
//			$groupRecursos = array();
//			foreach($recursoTipos as $tipo){
//				$groupRecursos[$tipo['RecursoTipo']['id']] = $tipo;
//				$groupRecursos[$tipo['RecursoTipo']['id']]['recursos'] = $this->Recurso->find("all", array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'), 'conditions' => array('tipo_id' => $tipo['RecursoTipo']['id'])));
//			}

//			$this->set("groupRecursos", $groupRecursos);			
            $this->set("title", "<span class='fa fa-plane'></span>&nbsp;" .__("Plano de " . $title));
			$this->set("calendar_search", true);		
		}
		
		function edit_map($location_id = 3){
			$this->Prg->commonProcess();
            $vars_search=$this->Prg->parsedParams();

//			$this->layout="default_limpio";
			$this->layout="administracion";
            $this->set('container_fluid', true);
			$this->Navegacion->addToScrumb(__("Editar Mapa"), "/recursos/edit_map");


            //PARA SACAR LAS COMPANIES DONDE TENGO PERMISOS
            $companies_rol=$this->Funcion->getCompaniesByAction('controllers/' . $this->request->params['controller'] . '/' .$this->request->params['action']);



//            $this->Paginator->settings=$this->paginate;
//            $this->Paginator->settings['order']=array("EciTalon.talon_num DESC", "EciTalon.comision ASC");
//            $this->Paginator->settings['conditions'] = $this->EciTalon->parseCriteria($vars_search);


            //$this->Paginator->settings['conditions'][]=array("Project.cae"=>1);

            //$row_notificaciones=array();
			
			
			$user_id=$this->Session->read('Auth.User.id');

//            $this->set(compact('companies', 'companies_rol','front','user_id'));
			
			$areas = $this->Recurso->RecursoMapArea->find("all", array('recursive' => -1, 'contain' => array('Recurso', 'Recurso.RecursoTipo'), 'conditions' => array('Recurso.location_id' => $location_id)));
			$overlayList = [];
//			debug($areas);

			foreach($areas as $area){
				$overlay = [
					'id' => 'example-overlay' . $area['RecursoMapArea']['id'],
					'x' => (float) $area['RecursoMapArea']['topleftX'],
					'y' => (float) $area['RecursoMapArea']['topleftY'],
					'width' => (float) $area['RecursoMapArea']['diffX'],
					'height' => (float) $area['RecursoMapArea']['diffY'],
					'className' => 'area-' . $area['Recurso']['RecursoTipo']['suffix'],
					'resourceId' => $area['Recurso']['id'],
					'recursoTipo' => $area['Recurso']['RecursoTipo']['id'],
					'visible' => true,
					'areaId' => $area['RecursoMapArea']['id']
				];

				$overlayList[] = $overlay;
			}
			$this->set("overlays", $overlayList);

			$location = $this->Recurso->Location->find("first", array('recursive' => -1, 'conditions' => array('Location.id' => $location_id)));

			$map_url = "/img/planos/PCYT_v5.jpg";
			if(isset($location) && $location){
				$map_url = $location['Location']['map_url'];
			}

			$this->set("map_url", $map_url);
			
			$recursoTipos = $this->Recurso->RecursoTipo->find("all", array('recursive' => -1));
			$this->set("recursoTipos", $recursoTipos);
			
			$recursos = $this->Recurso->find("all", array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo')));
			$this->set("recursos", $recursos);
			
			$groupRecursos = array();
			foreach($recursoTipos as $tipo){
				$groupRecursos[$tipo['RecursoTipo']['id']] = $tipo;
				$groupRecursos[$tipo['RecursoTipo']['id']]['recursos'] = $this->Recurso->find("all", array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'), 'conditions' => array('tipo_id' => $tipo['RecursoTipo']['id'])));
			}
			
			$this->set("groupRecursos", $groupRecursos);
			$this->set("title", "<span class='fa fa-plane'></span>&nbsp;" .__("Editar Plano"));
			
		}


		public function calendar($tipo_id = null){
			$this->Prg->commonProcess();
            $vars_search=$this->Prg->parsedParams();
			$this->set("vars_search", $vars_search);



// 			$this->layout="default";
			$this->layout="default_limpio";
            $this->set('container_fluid', true);
// 			$this->Navegacion->addToScrumb(__("Reservas - Mapa"), "/recursos/map");



            //PARA SACAR LAS COMPANIES DONDE TENGO PERMISOS
            // $companies_rol=$this->Funcion->getCompaniesByAction('controllers/' . $this->request->params['controller'] . '/' .$this->request->params['action']);

			$user_id=$this->Session->read('Auth.User.id');

			$this->set('tipo_id', $tipo_id);

			$conditions = array();
			if(isset($tipo_id) && $tipo_id){
				$conditions = array('RecursoTipo.id' => $tipo_id);
				$conditions_aux = array('Recurso.RecursoTipo.id' => $tipo_id);
				// $conditions_areas = array('Recurso.tipo_id' => $tipo_id);
				// $tipo = $this->Recurso->RecursoTipo->find("first", array('recursive' => -1, 'conditions' => $conditions));
				// $title = $tipo['RecursoTipo']['names_' . $this->Session->read('Config.language')];
			}
			$conditions[] = array('Recurso.active' => '1');

			$hashtag = "#";
			if($this->Session->read('Config.language') == "esp"){
				$hashtag = "Nº ";
			}

			$recursos = $this->Recurso->find("all", array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'), 'conditions' => $conditions));
			$recursos_json = array();
			//Build a JSON dictionary of resources, with the id being the resource id, the title being the resource name (depending on Config.language), and the group_id being the resource type id
			foreach($recursos as $recurso){
				$numeric_order = preg_replace('/[^0-9]+/', '', $recurso['Recurso']['name_' . $this->Session->read('Config.language')]);
				
				$rec_tipo_id = $recurso['RecursoTipo']['id'];
				if($rec_tipo_id == 1){
					$title = $hashtag . $numeric_order;
				} else {
					$title = $recurso['Recurso']['name_' . $this->Session->read('Config.language')];
				}

				$recursos_json[] = array(
					'id' => $recurso['Recurso']['id'],
					'title' => $title,
					'group_id' => $recurso['RecursoTipo']['id'],
					'group_name' => $recurso['RecursoTipo']['names_' . $this->Session->read('Config.language')],
					// add a numeric order property to each resource, that contains the number (can be of more than one digit) included at the end of the resource name
					'numeric_order' => intval($numeric_order),
					// 'users_show' => $recurso['Recurso']['users_show']
				);
			}
			$this->set("recursos", $recursos);
			$this->set("recursos_json", json_encode($recursos_json));

			//Build a JSON dictionary of reservas, with the id being the reserva id, the resourceId being the resource id, the start being the reserva start date, the end being the reserva end date, and the title being the reserva title

			$reservas = $this->Recurso->RecReserva->find('all', array('recursive' => -1, 'contain' => array('Recurso', 'Recurso.RecursoTipo', 'User')));
			$reservas_json = array();
			// foreach($reservas as $reserva){
			// 	$reservas_json[] = array(
			// 		'id' => $reserva['RecReserva']['id'],
			// 		'resourceId' => $reserva['Recurso']['id'],
			// 		'userId' => $reserva['User']['id'],
			// 		'start' => $reserva['RecReserva']['fecha'] . 'T' . str_replace(".", ":", $reserva['RecReserva']['hora_inicio']),
			// 		'end' => $reserva['RecReserva']['fecha'] . 'T' . str_replace(".", ":", $reserva['RecReserva']['hora_fin']),
			// 		'title' => $reserva['User']['name'],
			// 		'color' => $reserva['Recurso']['RecursoTipo']['color'],
			// 		'backgroundColor' => $reserva['Recurso']['RecursoTipo']['color'],
			// 	);
			// }

			foreach($reservas as $reserva) {
				$users_img_ldap = $this->viewVars['users_img_ldap'];

				// Get user's photo from LDAP
				if (isset($users_img_ldap[$reserva['User']['id']]) && $users_img_ldap[$reserva['User']['id']]) {
					$blob = $users_img_ldap[$reserva['User']['id']];
					$img = '<img style="width:20px" class="user_icon" src="data:image/png;base64,' . base64_encode($blob) . '"/>';
				} else {
					$img = "<span class='fa fa-user-circle-o text-black'></span>";
				}
			
				$reservas_json[] = array(
					'id' => $reserva['RecReserva']['id'],
					'resourceId' => $reserva['Recurso']['id'],
					'userId' => $reserva['User']['id'],
					'start' => $reserva['RecReserva']['fecha'] . 'T' . str_replace(".", ":", $reserva['RecReserva']['hora_inicio']),
					'end' => $reserva['RecReserva']['fecha'] . 'T' . str_replace(".", ":", $reserva['RecReserva']['hora_fin']),
					'title' => $img . $reserva['User']['name'],
					'color' => $reserva['Recurso']['RecursoTipo']['color'],
					'backgroundColor' => $reserva['Recurso']['RecursoTipo']['color']
				);
			}
			

			$this->set("reservas", $reservas);
			$this->set("reservas_json", json_encode($reservas_json));

			// $users_list = $this->User->find('list', array('recursive' => -1));
			// $this->set("users_list", $users_list);

			// $companies_all = $this->Company->find('list',array('recursive' => -1,"conditions"=>array()));
			// $this->set("companies_all",$companies_all);


            $this->set("title", "<span class='fa fa-plane'></span>&nbsp;" .__("Calendario"));
		}

	
		public function saveArea(){
			if($this->request->isAjax()) {
				$this->autoLayout = false;
				$this->autoRender = false;

				if(isset($this->request->data) && $this->request->data)
				{
					$this->Recurso->create();
					// Get the coordinates from the AJAX request
					$data = $this->request->data;

					$data['Recurso']['puesto_num'] = null;
					if($data['Recurso']['tipo_id'] == 1){
						$name_arr = explode(' ', $data['Recurso']['name_esp']);
						$last = end($name_arr);
						if(is_numeric($last)){
							$data['Recurso']['puesto_num'] = intval($last);
						}
					}

					// Save the coordinates in the database
					if ($this->Recurso->save($data['Recurso'])) {
						$recurso_id = $this->Recurso->getLastInsertID();
						$this->Recurso->RecursoMapArea->create();
						$data['RecursoMapArea']['recurso_id'] = $recurso_id;
						if ($this->Recurso->RecursoMapArea->save($data['RecursoMapArea'])) {
							  $response = array('success' => true, 'message' => 'Coordinates saved successfully');
						}
					} else {
					  $response = array('success' => false, 'message' => 'Error saving');
					}

					// Return the JSON response
					$this->response->type('json');
					echo json_encode($response);
					return;
				}
			}
		}

		public function editArea($area_id = null){
			if($this->request->isAjax()) {
				$this->autoLayout = false;
				$this->autoRender = false;

				if(isset($this->request->data) && $this->request->data)
				{
					if(isset($this->request->data['area_id']) && $this->request->data['area_id']){
						$area_id = $this->request->data['area_id'];
					}
					
					$response = array('success' => false, 'message' => 'NO AREA_ID');
					if($area_id){
						$this->Recurso->RecursoMapArea->id = $area_id;
						$save_arr = array("topleftX" => $this->request->data['x'], "topleftY" => $this->request->data['y'], "diffX" => $this->request->data['width'], "diffY" => $this->request->data['height']);
						if($this->Recurso->RecursoMapArea->save($save_arr)){
							$response = array('success' => true, 'message' => 'Coordinates saved successfully');
						} else {
							$response = array('success' => false, 'message' => 'Error saving');
						}
					}

					// Return the JSON response
					$this->response->type('json');
					echo json_encode($response);
					return;
				}
			}
		}

		
		/**
	 * delete method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null, $masivo = null) {
		$this->Recurso->id = $id;
		if (!$this->Recurso->exists()) {
			throw new NotFoundException(__('Invalid'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Recurso->delete()) {
			if ($masivo)
				return 1;
			else
				$this->Session->setFlash(__('Deleted'), 'success');
		} else {
			if ($masivo)
				return 0;
			else
				$this->Session->setFlash(__('Error', 'danger'));
		}

		if ($masivo)
			return 1;
		else
			return $this->redirect(array('action' => 'edit_map/' . $this->Session->read("Recursoview.params")));
	}
	

	/**
	 * getRecursoInfo method
	 *
	 * @param string $id
	 * @param bool $area_admin
	 * @param bool $available
	 * @param bool $parcial
	 * @param string $date
	 * @param string $hora_inicio
	 * @param string $hora_fin
	 * @param string $tipo_id
	 * @return void
	 */

	public function getRecursoInfo($id=null,$area_admin=true,$available=false,$parcial=false, $date="", $hora_inicio="", $hora_fin="", $tipo_id="", $location_id=""){

            $this->autoLayout = false;
            $this->autoRender = false;

            if ($this->request->is('ajax')) {

				if(isset($this->request->data['resource_id']) && $this->request->data['resource_id']){
					$id = $this->request->data['resource_id'];
				}
				if(isset($this->request->data['available']) && $this->request->data['available']){
					$available = $this->request->data['available'];
				}
				if(isset($this->request->data['parcial']) && $this->request->data['parcial']){
					$parcial = $this->request->data['parcial'];
				}
				if(isset($this->request->data['date']) && $this->request->data['date']){
					$date = $this->request->data['date'];
				}
				if(isset($this->request->data['hora_inicio']) && $this->request->data['hora_inicio']){
					$hora_inicio = $this->request->data['hora_inicio'];
				}

				if(isset($this->request->data['hora_fin']) && $this->request->data['hora_fin']){
					$hora_fin = $this->request->data['hora_fin'];
				}

				if(isset($this->request->data['tipo_id']) && $this->request->data['tipo_id']){
					$tipo_id = $this->request->data['tipo_id'];
				}

				if(isset($this->request->data['location_id']) && $this->request->data['location_id']){
					$location_id = $this->request->data['location_id'];
				}

                $datos = $this->Recurso->find('first', array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'),'conditions' => array('Recurso.id' => $id)));
				
				$datetime_start = DateTime::createFromFormat('d-m-Y H.i', $date . ' ' . $hora_inicio);
				$correctDatetimeStart = $datetime_start->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format

				
				$datetime_end = DateTime::createFromFormat('d-m-Y H.i', $date . ' ' . $hora_fin);
				$correctDatetimeEnd = $datetime_end->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format

				//if the end date is before the start date, add one day to the end date
				if($datetime_end < $datetime_start){
					$datetime_end->modify('+1 day');
					$correctDatetimeEnd = $datetime_end->format('Y-m-d H:i:s'); // Convert to 'Y-m-d H:i:s' format
				}

				$reservas = $this->Recurso->RecReserva->find('all', array(
					'recursive' => -1, 
					'contain' => array('Recurso', 'User'), 
					'conditions' => array(
						'Recurso.id' => $id,
						'OR' => array(
							array(
								'datetime_inicio <=' => $correctDatetimeStart,
								'datetime_fin >' => $correctDatetimeStart
							),
							array(
								'datetime_inicio <' => $correctDatetimeEnd,
								'datetime_fin >=' => $correctDatetimeEnd
							),
							array(
								'datetime_inicio >=' => $correctDatetimeStart,
								'datetime_fin <=' => $correctDatetimeEnd
							)
						)
						
					)
				));

				$reservas_parcial = array();
				if($parcial){
					$conflictThreshold = 60; //minutes
					foreach($reservas as $id => $reserva){
						//check if $reserva['RecReserva']['datetime_inicio'] is 60 minutes after $correctDatetimeStart or $reserva['RecReserva']['datetime_fin'] is 60 minutes before $correctDatetimeEnd
						//if so, then the resource is only partially reserved
						// $reservaStart = new DateTime($reserva['RecReserva']['datetime_inicio']);
						// $reservaEnd = new DateTime($reserva['RecReserva']['datetime_fin']);
						// $resourceId = $reserva['Recurso']['id'];
						
						// // Calculate the minute difference between dates
						// $startMinutes = $reservaStart->diff(new DateTime($correctDatetimeStart))->i;
						// $endMinutes = $reservaEnd->diff(new DateTime($correctDatetimeEnd))->i;
						
						if ($this->isPartialTimeConflict($reserva['RecReserva']['datetime_inicio'], $reserva['RecReserva']['datetime_fin'], $correctDatetimeStart, $correctDatetimeEnd, $conflictThreshold)) {
							// Resource is "partial available" (conflict is 60 minutes or more away from start or end)
							$reservas_parcial[] = $reserva;
							unset($reservas[$id]);
						}
					}
				}

				
				$this->set('datos',$datos);
				$this->set('id', $id);
				$this->set('area_admin', $area_admin);
				$this->set('available', $available);
				$this->set('parcial', $parcial);
				$this->set('date', $date);
				$this->set('hora_inicio', $hora_inicio);
				$this->set('hora_fin', $hora_fin);
				$this->set('tipo_id', $tipo_id);
				$this->set('location_id', $location_id);
				$this->set('reservas', $reservas);
				$this->set('reservas_parcial', $reservas_parcial);
				$this->set('correctDatetimeStart', $correctDatetimeStart);
				$this->set('correctDatetimeEnd', $correctDatetimeEnd);
				$this->render('/Elements/recursos/view_recurso', 'ajax');
				return;
            }else{
                return new CakeResponse(array('body'=> __("Invalid request"),'status'=>500));
            }
	}
	
	public function add() {
		$this->edit();
		$this->render("edit");
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
        
	public function edit($id = null) {
	$this->layout="administracion";
		//index_adm
		$this->Navegacion->addToScrumb(__("Recursos"), "/recursos/edit_map");
		
		$this->set("title", "<span class='fa fa-plane'></span>&nbsp;" .__("Recursos"));
		$this->set("title_legend", "<span class='fa fa-pencil-square-o'></span>&nbsp;" .__("Editar Recurso")." #".$id);
		
		$options = array('conditions' => array('Recurso.' . $this->Recurso->primaryKey => $id), "recursive" => -1, "contain" => array('RecursoTipo', 'RecursoMapArea'));
		$recurso = $this->Recurso->find('first', $options);

		

//		PARA CUANDO HAGA LAS FILIALES
//		
//		$company_id = null;
//		if(isset($recurso['Company']['id']) && $recurso['Company']['id']){
//			$company_id = $recurso['Company']['id'];
//		}
//		
		
//		$solicitante_id = null;
//		if(isset($recurso['Solicitante']['id']) && $recurso['Solicitante']['id']){
//			$solicitante_id = $recurso['Solicitante']['id'];
//		}
		
//		$responsable_id = null;
//		if(isset($recurso['Responsable']['id']) && $recurso['Responsable']['id']){
//			$responsable_id = $recurso['Responsable']['id'];
//		}
		
		$tipo_recurso_id = null;
		if(isset($recurso['RecursoTipo']['id']) && $recurso['RecursoTipo']['id']){
			$tipo_recurso_id = $recurso['RecursoTipo']['id'];
		}
		
//		$estado_id = null;
//		if(isset($recurso['EciEstado']['id']) && $recurso['EciEstado']['id']){
//			$estado_id = $recurso['EciEstado']['id'];
//		}
		
		// $users_list = $this->User->find('list', array('recursive' => -1));

		$this->set(compact('id', 'recurso', 'tipo_recurso_id'));

		
//		//PARA SACAR LAS COMPANIES DONDE TENGO PERMISOS
//		$companies_rol=$this->Funcion->getCompaniesByAction('controllers/' . $this->request->params['controller'] . '/' .$this->request->params['action']);
//
//		$user_id=$this->Session->read('Auth.User.id');
//
//		$companies = $this->Company->find('list',array('recursive' => -1,"conditions"=>array("Company.id"=>$companies_rol)));
//		$companies_all = $this->Company->find('list',array('recursive' => -1,"conditions"=>array()));
//
//		$estados_filter = $this->EciTalon->EciEstado->find('list', array('recursive'=>-1, "conditions"=>array("active"=>1, "id" => array(1, 2, 3)),'fields' => array('EciEstado.id','EciEstado.name_'.$this->Session->read('Config.language'))));
//
//		$this->set(compact('companies', 'companies_rol','user_id','estados_filter'));
//

		
		
		if (!$this->Recurso->exists($id)) {
			throw new NotFoundException(__('Recurso inválido'));
		}
		if ($this->request->is(array('post', 'put'))) {
//			$old_status_id = $recurso['RecursoEstado']['id'];
//			$new_status_id = $this->request->data['RecursoEstado']['estado_id'];
//
//			if($old_status_id == 2 && $new_status_id != 2){
//				$proyectos = $recurso['EciTalonProyecto'];
//				
//				foreach($proyectos as $proyecto){
//					$this->EciTalon->EciTalonProyecto->delete($proyecto['id']);
//				}
//			}

			$this->Recurso->id = $id;

			$data = $this->request->data;

			
			if(isset($this->request->data['Recurso']['puesto_num']) && $this->request->data['Recurso']['puesto_num']){
				$data['Recurso']['puesto_num'] = $this->request->data['Recurso']['puesto_num'];
			} else if(isset($this->request->data['Recurso']['tipo_id']) && $this->request->data['Recurso']['tipo_id'] == 1){
				$name_arr = explode(' ', $data['Recurso']['name_esp']);
				$last = end($name_arr);
				if(is_numeric($last)){
					$data['Recurso']['puesto_num'] = intval($last);
				}
			} else {
				$data['Recurso']['puesto_num'] = null;
			}

			
			if ($this->Recurso->save($data)) {
				$this->Session->setFlash(__('El recurso <b>%s</b> ha sido editado', $id));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('No se ha podido guardar el recurso') . "&nbsp;<b>#" . $id . "</b>");
			}
		} else {
			$options = array('conditions' => array('Recurso.' . $this->Recurso->primaryKey => $id));
			$this->request->data = $this->Recurso->find('first', $options);
		}	
	}
	
	public function home($id = null) {
		$this->layout="default_limpio";
		$this->set('container_fluid', true);
		//index_adm
		$this->Navegacion->addToScrumb(__("Reserva de Recursos"), "/recursos/home");
		
		$this->set("title", "<span class='fa fa-book'></span>&nbsp;" .__("Reservas"));
//		$this->set("title_legend", "<span class='fa fa-pencil-square-o'></span>&nbsp;" .__("Reservas")." #".$id);
		
		$recursoTipos = $this->Recurso->RecursoTipo->find("all", array('recursive' => -1));

		$dateTimeStart = date('Y-m-d H:i:s');  // Current date and time
		$dateTimeEnd = date('Y-m-d H:i:s', strtotime('+4 hours'));  // Current date and time plus 4 hours

		foreach ($recursoTipos as $key => $tipo) {
			// Total Resources
			$recursoTipos[$key]['total'] = $this->Recurso->find('count', array(
				'conditions' => array('Recurso.tipo_id' => $tipo['RecursoTipo']['id'])
			));
			
			// Available Resources
			
			$recursoTipos[$key]['available'] = $this->Recurso->RecReserva->find('count', array(
				'recursive' => -1, 
				'contain' => array('Recurso', 'User'), 
				'conditions' => array(
					'Recurso.tipo_id' => $tipo['RecursoTipo']['id'],
					'OR' => array(
						array(
							'datetime_inicio <=' => $dateTimeStart,
							'datetime_fin >' => $dateTimeStart
						),
						array(
							'datetime_inicio <' => $dateTimeEnd,
							'datetime_fin >=' => $dateTimeEnd
						)
					)
				)
			));
		}

		


		$this->set(compact('recursoTipos'));
	}

		public function filterResourcesAjax($term = ""){
			
            $this->autoLayout = false;
            $this->autoRender = false;

            if ($this->request->is('ajax')) {

				if(isset($this->request->data['search_recurso']) && $this->request->data['search_recurso']){
					$term = $this->request->data['search_recurso'];
				}

                $filteredRecursos = $this->Recurso->find('all', array('recursive' => -1, 'contain' => array('RecursoMapArea', 'RecursoTipo'),'conditions' => array('Recurso.name_' . $this->Session->read('Config.language') . ' LIKE' => '%' . $term . '%')));
				$results = array();
				foreach ($filteredRecursos as $id => $recurso) {
					$results[] = array(
						'id' => $recurso['Recurso']['id'],
						'name' => $recurso['Recurso']['name_' . $this->Session->read('Config.language')],
						'class' => $recurso['RecursoTipo']['class']
					);
				}
				
				echo json_encode($results);
				return;
            }else{
                return new CakeResponse(array('body'=> __("Invalid request"),'status'=>500));
            }
		}

		public function isPartialTimeConflict($start1, $end1, $start2, $end2, $maxConflictMinutes = 60) {
			// Convert the input strings to DateTime objects
			$start1 = new DateTime($start1);
			$end1 = new DateTime($end1);
			$start2 = new DateTime($start2);
			$end2 = new DateTime($end2);
			
			// Calculate the overlap duration in minutes
			$overlapMinutes = min($end1->getTimestamp(), $end2->getTimestamp()) - max($start1->getTimestamp(), $start2->getTimestamp());
			$overlapMinutes /= 60; // Convert to minutes
			
			// Check if the overlap duration is less than or equal to the specified maximum
			if ($overlapMinutes <= $maxConflictMinutes) {
				return true; // Partial conflict
			}
			
			return false; // No or full conflict
		}
	
}
?>