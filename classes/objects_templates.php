<?php

	function get_template($object_type){
		$objects_templates = array(	

			// Software spec
			"softwareapplication"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False,
					'description'=>''
					),
				'name'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False,
					'description'=>'Name of the tool'
					),
				'description'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False,
					'description'=>'Short description of the tool'
					),
				'url'=>array(
					'presence'=>'required',
					'type'=>['uri'],
					'cardinality'=>False,
					'description'=>'Homepage URL of the tool (for referencing ressources use the "sameAS" field)'
					),
				'featureList'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'EDAM operation ID (provide either the ID or URL to the term)'
					),
				'softwareVersion'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False,
					'description'=>'The version of the tool for which these metadata are provided'
					),
				'publisher'=>array(
					'presence'=>'recommended',
					'type'=>['object'],
					'values'=>['Person','Organization'],
					'cardinality'=>True,
					'description'=>'Publisher of the tool, you can either enter a Person or an Organization'
					),
				'Citation'=>array(
					'presence'=>'recommended',
					'type'=>['object'],
					'values'=>['CreativeWork'],
					'cardinality'=>True,
					'description'=>'Article that uses or quote this tool. This field expects a CreativeWork object.'
					),
				'license'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'The license the tool registered with. Try to provide a link to the license page.'
					),
				'applicationCategory'=>array(
					'presence'=>'optionnal',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'The EDAM toolType term of the tool. Pick among: <ul> <li> Command-line tool </li> <li> Database portal</li> <li> ... </li></ul> '
					),
				'keywords'=>array(
					'presence'=>'optionnal',
					'type'=>['string'],
					'cardinality'=>True
					),
				'potentialAction'=>array(
					'presence'=>'optionnal',
					'type'=>['object'],
					'values'=>['ControlAction'],
					'cardinality'=>True
					),
				'offers'=>array(
					'presence'=>'optionnal',
					'type'=>['object'],
					'values'=>['Offer'],
					'cardinality'=>True
					),
				'softwareRequirements'=>array(
					'presence'=>'optionnal',
					'type'=>['string'],
					'cardinality'=>True
					),
				'dateCreated'=>array(
					'presence'=>'optionnal',
					'type'=>['date'],
					'cardinality'=>False
					),
				'dateModified'=>array(
					'presence'=>'optionnal',
					'type'=>['date'],
					'cardinality'=>False
					)
			),

			"offer"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'price'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>False
					),
				'priceCurrency'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>False
					),
			),

			"person"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'name'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'email'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>False
					),
				'description'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>False
					),
			),

			"organization"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					)
			),

			"creativework"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'name'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'url'=>array(
					'presence'=>'required',
					'type'=>['uri'],
					'cardinality'=>False
					),
			),

			"controlaction"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'object'=>array(
					'presence'=>'recommended',
					'type'=>['object'],
					'values'=>['Dataset'],
					'cardinality'=>True
					),
				'result'=>array(
					'presence'=>'recommended',
					'type'=>['object'],
					'values'=>['Dataset'],
					'cardinality'=>True
					),
			),

			"dataset"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
					),
				'additionalType'=>array(
					'presence'=>'required',
					'type'=>['uri'],
					'cardinality'=>False
					)
			),

			// Event spec
			"event"=>array(
				'@type'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>False
				),
				'name'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>True
				),
				'description'=>array(
					'presence'=>'required',
					'type'=>['string'],
					'cardinality'=>True
				),
				'startDate'=>array(
					'presence'=>'required',
					'type'=>['date'],
					'cardinality'=>False
				),
				'endDate'=>array(
					'presence'=>'required',
					'type'=>['date'],
					'cardinality'=>False
				),
				'location'=>array(
					'presence'=>'required',
					'type'=>['object'],
					'values'=>['PostalAddress', 'Place'],
					'cardinality'=>False
				),
				'contact'=>array(
					'presence'=>'required',
					'type'=>['object'],
					'values'=>['Organization', 'Person'],
					'cardinality'=>True
				),
				'hostInstitution'=>array(
					'presence'=>'required',
					'type'=>['object'],
					'values'=>['Organization'],
					'cardinality'=>True
				),
				'eventType'=>array(
					'presence'=>'required',
					'type'=>['object'],
					'values'=>['eventType'],
					'cardinality'=>True
				),				
			)
		);

		if (isset($objects_templates[strtolower($object_type)])){
			return $objects_templates[strtolower($object_type)];
		}
		else{
			return false;
		}
	}

?>