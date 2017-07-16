<?php

// TODO: create new object when a @type is detected
// enable to pick up errors and warnings to put into the table header
// create templates for every supported object
// check for missing required and recommended fields 
// add a <td> (after field_name, to emulate a field_value <td>) for <tr> that have mouseover issues


require_once 'objects_templates.php';
require_once 'BSCsubProcessor.php';


class BSCProcessor extends stdClass{

	var $template_fields;
	var $message_output = '';
	var $values;
	var $error = array();
	var $warning = array();
	var $sublevel = 1;


	function __construct($json) {
		if (!isset($json)){
			$this->errors=TRUE;
		}
		else { 
			$this->values = $json;
			$this->template_fields = get_template(strtolower(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"}))));
			if ($this->template_fields!=null){
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="first_line"><th></th><th class="field_name">'.$this->values->{'@type'}.'</th> <th class="field_description"> </th> <th class="object_errors">'.count($this->error).' error(s) & '.
				count($this->warning).' warning(s) </th> </tr>'.$result;
			}
			else{
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="first_line"><th></th><th class="field_name">'.$this->values->{'@type'}.' (UNSUPPORTED OBJECT) </th> <th></th> </tr>'.$result;
			}
		}
	}

	function make_table(){
		return '<table class="bioschemas_validation">'.$this->message_output.'</table>';
	}

	function validate_json($json){

		$padding = $this->sublevel*20;
		$padding = $padding.'px';
		$output = '';
		$output = '' ;

		foreach ($json as $field_name=>$field_value){
			if ($field_name!='@context' and $field_name!='@id'){

				if(gettype($field_value) == 'string' and $field_name!='@type'){
					$output .= $this->process_string_field($field_name, $field_value);
				}

				if(gettype($field_value) == 'object'){
					$output .= $this->process_object_field($field_value, $field_name, $this->sublevel+1);
				}

				if(gettype($field_value) == 'array'){
					if ($this->template_fields[$field_name]['cardinality'] == True or !isset($this->template_fields[$field_name])){
						foreach ($field_value as $subfield_name=>$subfield_value){
							if (gettype($subfield_value) == 'string'){
								$output .= $this->process_string_field($field_name, $subfield_value);
							}
							elseif(gettype($subfield_value) == 'object'){
								$output .= $this->process_object_field($subfield_value, $field_name, $this->sublevel+1);
							}
						}
					}
					else{

						// MULTIPLE VALUES ERROR
						$local_error = array(
							"field"=>$field_name,
							"error"=>"Multiple values not allowed");
						$output.='<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"> </td> <td>'.$field_name.' Multiple values are not allowed for that field </td> <td class="field_description"></td> </tr>';
						array_push($this->error, $local_error);
					}
				}

				else{

				}
			}
		}

		// ERRORS AND WARNINGS FOR MISSING FIELDS
		foreach ($this->template_fields as $field_name=>$field_value){
			if ($field_value['presence'] == 'required'){
				if (!isset($json->{$field_name})){

					// REQUIRED FIELD MISSING ERROR
					$local_error = array(
							"field"=>$field_name,
							"error"=>"Required field missing");
					$output .= '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"></td><td class="field_name error_field" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value error_field"> A required field is missing </td> </tr>';
					array_push($this->error, $local_error);
				}
			}
			elseif ($field_value['presence'] == 'recommended'){
				if (!isset($json->{$field_name})){

					// REQUIRED FIELD MISSING ERROR
					$local_warning = array(
							"field"=>$field_name,
							"warning"=>"Required field missing");
					$output .= '<tr class="table_line"> <td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value field_warning"> A recommended field is missing </td> </tr>';
					array_push($this->warning, $local_warning);
				}
			}
		}
		return $output;
	}

	function process_object_field($field_value, $field_name, $level){

		$subobject = new BSCsubProcessor($field_value, $field_name, $level, $this->template_fields[$field_name]['values'], $this->template_fields[$field_name]['description']);

		if (count($subobject->error)>0){
			$error = array('field'=>$field_name,
						   'error'=>'error with subfield '.$subobject->error[0]['field']);
			array_push($this->error, $error);
		}
		elseif (count($subobject->warning)>0){
			$warning = array('field'=>$field_name,
							'warning'=>'error with subfield '.$subobject->warning[0]['field']);
			array_push($this->warning, $warning);
		}
		$message .= $subobject->message_output;	
		return $message; 
	}

	function process_string_field($field_name, $field_value){
		$output = '';

		$padding = $this->sublevel*20;
		$padding = $padding.'px';
		$output = '';

		// UNSUPPORTED STRING FIELD WARNING
		if (!isset($this->template_fields[$field_name])){
			$output.= '<tr class="table_line"> <td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value field_warning">'.$field_value.' </td> </tr>';
			$local_warning = array(
						'field'=>$field_name,
						'error'=>'Field not supported');
			array_push($this->warning, $local_warning);
		}


		elseif (in_array(typeof($field_value), $this->template_fields[$field_name]['type'])){
			$output.= '<tr class="table_line"> <td class="fa first_col fa-check-circle" aria-hidden="true"></td><td class="field_name" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value">'.$field_value.'</td> <td class="field_description">'.$this->template_fields[$field_name]['description'].'<td></tr>';
		}

		else{
			// NEED SOME TWEEKING AROUND HERE TO IMPLEMENT CORRECT ERRORS/WARNINGS
			// UNEXPECTED TARGET TYPE ERROR
			if ($field_name!='@type'){
				$output.= '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"> </td> <td class="field_name error_field" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value error_field">'.typeof($field_value).' is not a valid target for field '.$field_name.'</td></tr>';
				$local_error = array('field'=>$field_name, 'error'=>"unexpected type as target");
				array_push($this->error, $local_error);
			}
			else{
				$output.= '<tr class="table_line"> <td class="fa first_col fa-check-circle" aria-hidden="true"> </td> <td class="field_name" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value">'.$field_value.'</td></tr>';
			}
		}
		return $output;
	}
}


function typeof($val){
	if (gettype($val)=='string'){
		if (isDate(str_replace('.', '&', str_replace('/','-', $val)))) {
			return 'date';
		}
		elseif (filter_var($val, FILTER_VALIDATE_URL)){
			return 'uri';
		}
		else{
			return 'string';
		}
	}
	else{
		return gettype($val);
	}
}

function isDate($date){
	return (bool)strtotime($date);
}


?>
