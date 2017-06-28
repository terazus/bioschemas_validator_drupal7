<?php

	function get_template($object_type){
		$objects_templates = 
			array(	
				"softwareapplication"=>array(
					'@type'=>array(
						'presence'=>'required',
						'type'=>'string',
						'cardinality'=>False
						),
					'name'=>array(
						'presence'=>'required',
						'type'=>'string',
						'cardinality'=>False
						),
					'description'=>array(
						'presence'=>'required',
						'type'=>'string',
						'cardinality'=>False
						),
					'url'=>array(
						'presence'=>'required',
						'type'=>'uri',
						'cardinality'=>False
						),
					'featureList'=>array(
						'presence'=>'required',
						'type'=>'string',
						'cardinality'=>True
						),
					'softwareVersion'=>array(
						'presence'=>'required',
						'type'=>'string',
						'cardinality'=>False
						),
					'publisher'=>array(
						'presence'=>'recommended',
						'type'=>'object',
						'values'=>['Person','Organization'],
						'cardinality'=>True
						),
					'Citation'=>array(
						'presence'=>'recommended',
						'type'=>'object',
						'values'=>['CreativeWork'],
						'cardinality'=>True
						),
					'license'=>array(
						'presence'=>'recommended',
						'type'=>'string',
						'cardinality'=>True
						),
					'applicationCategory'=>array(
						'presence'=>'optionnal',
						'type'=>'string',
						'cardinality'=>True
						),
					'keywords'=>array(
						'presence'=>'optionnal',
						'type'=>'string',
						'cardinality'=>True
						),
					'potentialAction'=>array(
						'presence'=>'optionnal',
						'type'=>'object',
						'values'=>['ControlAction'],
						'cardinality'=>True
						),
					'offers'=>array(
						'presence'=>'optionnal',
						'type'=>'object',
						'values'=>['Offer'],
						'cardinality'=>True
						),
					'softwareRequirements'=>array(
						'presence'=>'optionnal',
						'type'=>'string',
						'cardinality'=>True
						),
					'dateCreated'=>array(
						'presence'=>'optionnal',
						'type'=>'date',
						'cardinality'=>False
						),
					'dateModified'=>array(
						'presence'=>'optionnal',
						'type'=>'date',
						'cardinality'=>False
						)
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