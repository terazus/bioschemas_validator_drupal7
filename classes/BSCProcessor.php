<?php

// TODO: create new object when a @type is detected
// enable to pick up errors and warnings to put into the table header
// create templates for every supported object
// check for missing required and recommended fields 
// add a <td> (after field_name, to emulate a field_value <td>) for <tr> that have mouseover issues


require_once 'objects_templates.php';


class BSCProcessor extends stdClass{

	var $template_fields;
	var $message_output = '';
	var $values;
	var $error = array();
	var $warning = array();


	function __construct($json) {
		if (!isset($json)){
			$this->errors=TRUE;
		}
		else { 
			$this->values = $json;
			$this->template_fields = get_template(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"})));
			if ($this->template_fields!=null){
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="first_line"><th></th><th class="field_name">'.$this->values->{'@type'}.'</th> <th></th> </tr>'.$result;
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

		$output = '' ;

		foreach ($json as $field_name=>$field_value){
			if ($field_name!='@context' and $field_name!='@id'){

				if(gettype($field_value) == 'string'){
					$output .= $this->process_string_field($field_name, $field_value);
				}

				if(gettype($field_value) == 'object'){
					$output .= $this->process_object_field($field_name, $field_value, 0);
				}

				if(gettype($field_value) == 'array'){
					if ($this->template_fields[$field_name]['cardinality'] == True or !isset($this->template_fields[$field_name])){
						foreach ($field_value as $subfield_name=>$subfield_value){
							if (gettype($subfield_value) == 'string'){
								$output .= $this->process_string_field($field_name, $subfield_value);
							}
							elseif(gettype($subfield_value) == 'object'){
								$output .= $this->process_object_field($field_name, $subfield_value, 0);
							}
						}
					}
					else{
						$output.='<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"> </td> <td>'.$field_name.' Multiple values are not allowed for that field </td></tr>';
					}
				}

				else{

				}
			}
		}

		foreach ($this->template_fields as $field_name=>$field_value){
			if ($field_value['presence'] == 'required'){
				if (!isset($json->{$field_name})){
					$output .= '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"></td><td class="field_name error_field">'.$field_name.'</td><td class="field_value error_field"> A required field is missing </td> </tr>';
				}
			}
		}
		return $output;
	}

	function process_object_field($field_name, $field_value, $level){
		$suboutput = '';
		$error = false;
		$warning = false;
		
		foreach ($field_value as $subfield_name=>$subfield_value){

			if ($subfield_name == '@type'){
				if (!isset($this->template_fields[$field_name])){
					$suboutput.= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:20px" class="field_name"> @type </td> <td class="field_value">'.$field_value->{'@type'}.'</td></tr>';
					$warning = $field_name.' is not supported by Bioschemas specifications';
				}
				elseif (in_array($field_value->{'@type'}, $this->template_fields[$field_name]['values'])){
					$suboutput.= '<tr class="table_line"><td class="first_col"></td><td style="padding-left:20px" class="field_name"> @type</td><td class="field_value">'.$field_value->{'@type'}.'</td></tr>';
				}
				else{
					$suboutput.= '<tr class="table_line"><td class="first_col"></td><td style="padding-left:20px" class="field_name"> @type</td><td class="field_value">'.$field_value->{'@type'}.'</td></tr>';
					$error = $field_value->{'@type'}.' is not a valid target for field '.$field_name;
				}
			}
			else{
				if (gettype($subfield_value)== 'string'){
					$suboutput.= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:40px" class="field_name">'.$subfield_name.'</td><td class="field_value">'.$subfield_value.'</td></tr>';
				}
				elseif  (gettype($subfield_value)== 'object'){
					// I think this is where I should declare the new subojects (Person, Organization ...) in order to validate them aswell. Validate the sub-oject before validating the parent.
					// Can't validate sub-object yet
					$suboutput.=$this->recursive_print($subfield_name, $subfield_value, 1);
				}
				elseif  (gettype($subfield_value)== 'array'){
					foreach($subfield_value as $key=>$val){
						$suboutput.=$this->recursive_print($subfield_name, $val, 1);
					}
				}
			}
		}
		
		if(!$error){
			if(!$warning){
				$output.= '<tr class="table_line"><td class="fa first_col fa-check-circle" aria-hidden="true"> </td><td class="field_name">'.$field_name.'</td> <td class="field_value"></td></tr>';
			}
			else{
				$output.='<tr class="table_line"> <td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning">'.$field_name.'</td><td class="field_value field_warning">'.$warning.'</td></tr>';
			}
		}
		else{
			$output.='<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"></td><td class="field_name error_field">'.$field_name.'</td><td class="field_value error_field">'.$error.'</td></tr>';
		}
		$output.=$suboutput;
		return $output;
	}

	function recursive_print($field_name, $field_value, $level){
		$output = '';
		$padding = 20*$level;
		
		if (gettype($field_value)!='array'){
			$output.= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:'.$padding.'px" class="field_name">'.$field_name.'</td> <td class="field_value"></td> </tr>';
			$padding = $padding+20;
			foreach ($field_value as $subfield_name=>$subfield_value){
				if (gettype($subfield_value)=='string'){
					$output.= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:'.$padding.'px" class="field_name">'.$subfield_name.'</td><td class="field_value">'.$subfield_value.' </td> </tr>';
				}
				elseif (gettype($subfield_value)=='object'){
					$output.=$this->recursive_print($subfield_name, $subfield_value, $level+1);
				}
			}
		}
		else{
			foreach ($field_value as $subfield_name=>$subfield_value){
				if (gettype($subfield_value) == 'string'){
					$output .= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:'.$padding.'px" class="field_name">'.$subfield_name.'</td><td class="field_value">'.$subfield_value.' </td> </tr>';
				}
				elseif(gettype($subfield_value) != 'string'){
					$output .= '<tr class="table_line"> <td class="first_col"></td> <td style="padding-left:'.$padding.'px" class="field_name">'.$subfield_name.'</td> <td class="field_value"></td> </tr>';
					$output .= $this->recursive_print($field_name, $subfield_value, $level+1);
				}
			}
		}
		return $output;
	}

	function process_string_field($field_name, $field_value){
		$output = '';
		if (!isset($this->template_fields[$field_name])){
			$output.= '<tr class="table_line"> <td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning">'.$field_name.'</td><td class="field_value field_warning">'.$field_value.' </td> </tr>';
		}
		elseif (typeof($field_value) == $this->template_fields[$field_name]['type']){
			$output.= '<tr class="table_line"> <td class="fa first_col fa-check-circle" aria-hidden="true"></td><td class="field_name">'.$field_name.'</td><td class="field_value">'.$field_value.'</td></tr>';
		}
		else{
			if ($field_name!='@type'){
				$output.= '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"> </td> <td class="field_name error_field">'.$field_name.'</td><td class="field_value error_field">'.typeof($field_value).' is not a valid target for field '.$field_name.'</td></tr>';
			}
			else{
				$output.= '<tr class="table_line"> <td class="fa first_col fa-check-circle" aria-hidden="true"> </td> <td class="field_name">'.$field_name.'</td><td class="field_value">'.$field_value.'</td></tr>';
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
