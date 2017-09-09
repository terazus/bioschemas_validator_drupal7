<?php
/**
 * JSON-LD library which reads JSON-LD Bioschemas markup variables and checks each field restrictions
 * @package BioschemasProcessor
 * @author Batista Dominique <batistadominique@hotmail.com>
 * @copyright 2017
 */


/**
 * Class: PreProcessor
 * Prepare JSONs coming from Microdata and RDFa translation in order to be compliant with BSCProcessor
 *
 * @package BioschemasProcessor
 */

class PreProcessor extends StdClass{

	public $output_json;

	public function __construct($json, $format)
	{
		$formats = array('microdata', 'rdfa');
		if (!in_array($format, $formats))
		{
			trigger_error('Wrong format, should be "microdata" or "rdfa"');
		}
		else
		{
			if ($format=='microdata')
			{
				$output_json = $this->prepare_Microdata($json);
				$this->output_json = $output_json;
			}
			elseif($format=='rdfa')
			{
				$output_json = $this->prepare_RDFA($json);
				$this->output_json = $output_json;
			}
			
		}
	}

	private function prepare_RDFA($json_input){
		$new_json = array();

		// for each item in the json (relinking data together)
		foreach ($json_input as $item=>$item_fields){

			// Retrieving the key and values to fill in the array
			if(isset($item_fields->{'@id'})){
				$new_json[$item_fields->{'@id'}] = $item_fields;
			}
			else{
				if(isset($item_fields->{'https://schema.org/url'})){
					$new_json[$item_fields->{'https://schema.org/url'}] = $item_fields;
				}
				elseif (isset($item_fields->{'http://schema.org/url'})) {
					$new_json[$item_fields->{'http://schema.org/url'}] = $item_fields;
				}
				
			}

			// for each field of the current item
			foreach ($item_fields as $field_name=>$field_value){
				if (gettype($field_value) == 'array'){
					if ($field_name == '@type'){
						$new_json[$item_fields->{'@id'}]->{$field_name} = $field_value[0];
					}
					else{
						if (isset($field_value[0]->{'@id'})){
							$new_json[$item_fields->{'@id'}]->{$field_name} = $field_value[0]->{'@id'};
						}
						elseif(isset($field_value[0]->{'@value'})){
							$new_json[$item_fields->{'@id'}]->{$field_name} = $field_value[0]->{'@value'};
						}
					}
				}
			}
		}
		return $new_json;	
	}

	private function prepare_subRDFA($myjson, $sub_json){
		$new_json = new stdClass;

		foreach($sub_json as $sub_field=>$sub_value){
			$new_field = str_replace('http://schema.org/', '', $sub_field);
			if ($new_field == 'url'){
				$new_json->{$new_field} = $sub_value->{'@id'};
			}

			else{
				if(gettype($sub_value)=='object'){
					$new_subjson = new stdClass;
					$new_subjson->{"@type"} = str_replace('http://schema.org/','', $myjson[$sub_value->{'@id'}]->{'@type'});
					
					$ite = 0;
					foreach($myjson[$sub_value->{'@id'}] as $subfield_name=>$subfield_value){
						if($ite>1){
							$subfield_name =  str_replace('http://schema.org/','', $subfield_name);
							
							if (gettype($subfield_value)=='object'){
								if (isset($myjson[$subfield_value->{'@id'}])){
									//rewrite that line to have other subfields included
									$new_subjson->{$subfield_name} = $myjson[$subfield_value->{'@id'}]->{'http://schema.org/additionalType'};
								}
							}
							else{
								$new_subjson->{$subfield_name} = $subfield_value->{'@id'};
							}		}
						$ite++;
					}
					$new_json->{$new_field} = $new_subjson;
				}
				else{
					$new_json->{$new_field} = $sub_value;
				}					
			}
		}

		return $new_json;
	}

	private function prepare_Microdata($json){
		$processed_json = array();

		foreach ($json as $object){

			$object_as_json = new stdClass;
			$object_as_json->{'@context'} = 'http://schema.org';

			foreach ($object as $field_name=>$field_value){
				if ($field_name != '_class'){
					if(gettype($field_value)=='string'){
						$object_as_json->{str_replace('_', '@', $field_name)} = str_replace('http://schema.org/', '', str_replace('&quot;', "'", $field_value));
					}
					elseif(gettype($field_value)=='array'){
						$temp_values = array();
						foreach($field_value as $value){
							array_push($temp_values, str_replace('"', "'", $value));
						}
						$object_as_json->{str_replace('_', '@', $field_name)} = $temp_values;
					}

					elseif(gettype($field_value)=='object'){

						$this->prepare_subMicrodata($field_value);

						$subobject_as_json = new stdClass;
						foreach ($field_value as $subfield_name=>$subfield_value){
							if ($subfield_name != '_class'){
								$subobject_as_json->{str_replace('_', '@', $subfield_name)} = $subfield_value;
							}
						}
						$object_as_json->{str_replace('http://schema.org/', '', $field_name)} = $subobject_as_json;
					}
				}
			}

			array_push($processed_json, $object_as_json);
		}
		return $processed_json;
	}

	private function prepare_subMicrodata($json)
	{
		$object_as_json = new stdClass;
	
		foreach ($json as $field_name=>$field_value)
		{
			if ($field_name != '_class')
			{
				if(gettype($field_value)=='string'){
					$object_as_json->{str_replace('_', '@', $field_name)} = str_replace('http://schema.org/', '', str_replace('&quot;', "'", $field_value));
				}
			}

			elseif(gettype($field_value)=='array')
			{
				$temp_values = array();
				foreach($field_value as $value)
				{
					array_push($temp_values, str_replace('"', "'", $value));
				}
				$object_as_json->{str_replace('_', '@', $field_name)} = $temp_values;
			}

			elseif(gettype($field_value)=='object'){
				$object_as_json->{str_replace('_', '@', $field_name)} = $this->prepare_Microdata($field_value);
			}

		}

		return $object_as_json;
	}

}



















?>