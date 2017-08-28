<?php

class BSCsubProcessor extends BSCProcessor{

	var $sublevel;
	var $field_name;

	function __construct($json, $field_name, $level, $type_expected, $field_description) {
		if (!isset($json)){
			$this->errors=TRUE;
		}
		else {
			$this->values = $json;
			$this->field_name = $field_name;
			$this->sublevel = $level;
			$padding = 20*($this->sublevel-1);
			$padding_plus = $padding+20;
			$padding = $padding.'px';
			$padding_plus = $padding_plus.'px';

			$path = './sites/all/modules/CUSTOM/bioschemas_crawler/specs/';
			$spec_path = $path.strtolower(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"}))).'.json';			
			$this->template_fields = $this->make_spec($spec_path);

			if ($this->template_fields!=null){

				/* Let's first check if the provided @type value is the expected type for this field */
				if (!in_array(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"})), $type_expected) and isset($type_expected)){
					$result = '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"></td><td class="field_name error_field" style="padding-left:'.$padding_plus.'"> @type </td><td class="field_value error_field">'.$json->{"@type"}.'</td> </tr>';
					$error = array('field'=>$field_name,
						   'error'=>$json->{'@type'}.' not a valid target for this field');
					array_push($this->error, $error);
				}
				else{
					$result = '<tr class="table_line"> <td class="fa first_col fa-check-circle" aria-hidden="true"></td><td class="field_name" style="padding-left:'.$padding_plus.'"> @type </td><td class="field_value">'.$json->{'@type'}.'</td></tr>';
				}

				/* Now we validate the subJSON before */
				$result .= $this->validate_json($this->values);
				/* There is something in the error attribute*/
				if (count($this->error)>0) {
					$this->message_output = '
					<tr class="table_line">
						<td class="fa first_col fa-times-circle" aria-hidden="true"></td>
						<td class="field_name error_field" style="padding-left:'.$padding.'">'.$this->field_name.'</td> 
						<td class="field_value error_field">'.json_encode($this->error[0]['error']).'</td>
					</tr>'.$result;
				}
				/* There is something in the warning attribute */
				elseif (count($this->warning)>0) {
					$this->message_output = '
					<tr class="table_line">
						<td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td>
						<td class="field_name field_warning" style="padding-left:'.$padding.'">'.$this->field_name.'</td> 
						<td class="field_value field_warning">'.json_encode($this->warning[0]['warning']).'</td>
						<td class="field_description">'.$field_description.'</td>
					</tr>'.$result;
				}
				/* There's no error and no warning */
				else {
					$this->message_output = '<tr class="table_line"><td class="fa first_col fa-check-circle"></td><td class="field_name" style="padding-left:'.$padding.'">'.$this->field_name.'</td> <td class="object_errors"></td> <td class="field_description">'.$field_description.'</td> </tr>'.$result;
				}
			}

			/* Couldn't load the template : the field isn't supported by Bioschemas */
			else{
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="table_line"><td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning" style="padding-left:'.$padding.'">'.$this->values->{'@type'}.'</td> <td class="field_value field_warning">This field is not supported by Bioschemas</td> </tr>'.$result;
			}
		}
	}	
}

?>