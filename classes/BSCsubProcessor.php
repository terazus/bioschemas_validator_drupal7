<?php

class BSCsubProcessor extends BSCProcessor{

	var $sublevel;
	var $field_name;

	function __construct($json, $field_name, $level) {
		if (!isset($json)){
			$this->errors=TRUE;
		}
		else { 
			$this->values = $json;
			$this->field_name = $field_name;
			$this->sublevel = $level;
			$this->template_fields = get_template(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"})));
			$padding = 20*($this->sublevel-1);
			$padding = $padding.'px';
			if ($this->template_fields!=null){
				$result = $this->validate_json($this->values);
				if (count($this->error)>0) {
					$this->message_output = '
					<tr class="table_line">
						<td class="fa first_col fa-times-circle" aria-hidden="true"></td>
						<td class="field_name error_field" style="padding-left:'.$padding.'">'.$this->field_name.'</td> 
						<td class="field_value error_field">'.json_encode($this->error[0]['error']).'</td>
					</tr>'.$result;
				}
				elseif (count($this->warning)>0) {
					$this->message_output = '
					<tr class="table_line">
						<td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td>
						<td class="field_name field_warning" style="padding-left:'.$padding.'">'.$this->field_name.'</td> 
						<td class="field_value field_warning">'.json_encode($this->warning[0]['warning']).'</td>
					</tr>'.$result;
				}
				else {
					$this->message_output = '<tr class="table_line"><td class="fa first_col fa-check-circle"></td><td class="field_name" style="padding-left:'.$padding.'">'.$this->field_name.'</td> <td class="object_errors"></td> </tr>'.$result;
				}
			}
			else{
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="table_line"><td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning" style="padding-left:'.$padding.'">'.$this->values->{'@type'}.'</td> <td class="field_value field_warning">This field is not supported by Bioschemas</td> </tr>'.$result;
			}
		}
	}	
}

?>