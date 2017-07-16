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
					'description'=>'Homepage URL of the tool. If you want to provide an URL to a referencing ressource use the "sameAS" field instead of URL'
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
					'description'=>'Article that uses or quotes this tool. This field expects a CreativeWork object.'
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
					'description'=>'The EDAM toolType term of the tool. Pick among: 
						<ul> 
							<li> Command-line tool </li> 
							<li> Database portal</li> 
							<li> Desktop Application </li>
							<li> Library </li>
							<li> Ontologies</li>
							<li> Plug-in </li>
							<li> Script </li>
							<li> SPARQL </li>
							<li> Endpoint </li>
							<li> Suite </li>
							<li> Web Application </li>
							<li> Web API </li>
							<li> Web service </li>
							<li> Workbench </li>
							<li> Workflow </li>
						</ul> '
					),
				'keywords'=>array(
					'presence'=>'optionnal',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'The EDAM topic of the tool. Provide either an EDAM ID or an EDAM URL'
					),
				'potentialAction'=>array(
					'presence'=>'optionnal',
					'type'=>['object'],
					'values'=>['ControlAction'],
					'cardinality'=>True,
					'description'=>"Input, Output and API's URLs templates"
					),
				'offers'=>array(
					'presence'=>'optionnal',
					'type'=>['object'],
					'values'=>['Offer'],
					'cardinality'=>True,
					'description'=>'An offer to provide this item. Expects an Offer object. If the tool is free, indicate 0.00 as price.'
					),
				'softwareRequirements'=>array(
					'presence'=>'optionnal',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'Requierements before using the tool'
					),
				'dateCreated'=>array(
					'presence'=>'optionnal',
					'type'=>['date'],
					'cardinality'=>False,
					'description'=>'Date the tool was created'
					),
				'dateModified'=>array(
					'presence'=>'optionnal',
					'type'=>['date'],
					'cardinality'=>False,
					'description'=>'Latest update date of the tool'
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
					'cardinality'=>False,
					'description'=>'Price of the tool. Fill 0.00 if free. If you have price bundles (freemium model for instance), you may create a new offer for each bundle'
					),
				'priceCurrency'=>array(
					'presence'=>'recommended',
					'type'=>['string'],
					'cardinality'=>True,
					'description'=>'currencies accepted for payment'
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
					'cardinality'=>False,
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
					'cardinality'=>True,
					'description'=>'Input of the tool. A Dataset field is expected here.'
					),
				'result'=>array(
					'presence'=>'recommended',
					'type'=>['object'],
					'values'=>['Dataset'],
					'cardinality'=>True,
					'description'=>'Ouput of the tool. A Dataset field is expected here.'
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
					'type'=>['string', 'uri'],
					'cardinality'=>False,
					'description'=>'Fill either an EDAM ID or an EDAM valid URL from the data table'
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