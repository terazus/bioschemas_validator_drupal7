<?php
/**
 * JSON-LD library which reads JSON-LD Bioschemas markup variables and checks each field restrictions
 * @package BioschemasProcessor
 * @author Batista Dominique <batistadominique@hotmail.com>
 * @copyright 2017
 */

require_once 'BSCsubProcessor.php';

/**
 * Class: BSCProcessor
 * Implements a simple parser which loads a JSON-LD variable and checks every field for:
 * - presence (required, recommended or optionnal),
 * - terms under controlled vocabularies
 * - cardinality
 * - value type
 * 
 * Specifications are loaded from corresponding files under the /specs/ directory. 
 *
 * @package BioschemasProcessor
 */

class BSCProcessor extends stdClass
{

	protected $template_fields;
	public $message_output = '';
	public $values;
	public $error = array();
	public $warning = array();
	protected $sublevel = 1;
	protected $parent;


	/**
	 * Constructor
	 *
	 * You should first make sure your JSON-ld variable is valid (json_decode()).
	 *
	 * @param 	object 	$json 			A json-ld variable containing the fields to process
	 * @return 	object 	BSCProcessor 	An object that processes the fields and containing an HTML table, an array of errors and an array of warnings.
	 */
	public function __construct($json) 
	{
		if (!isset($json)){
			$this->errors=TRUE;
		}
		else 
		{ 
			$this->values = $json;
			$file_name = strtolower(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"})));
			if (!isset($json->{"@type"})){
				$file_name = strtolower(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"_type"})));
			}
			$path = './sites/all/modules/CUSTOM/bioschemas_crawler/specs/default/';
			$this->parent = $file_name;
			$spec_path = $path.$file_name.'/'.$file_name.'.json';
			$this->template_fields = $this->make_spec($spec_path);

			//dpm($this->template_fields);

			if ($this->template_fields!=null)
			{
				$result = $this->validate_json($this->values);
				$this->message_output = '<tr class="first_line"><th class="first_col"></th><th class="field_name">'.$this->values->{'@type'}.'</th> <th class="object_errors">'.count($this->error).' error(s) & '.
				count($this->warning).' warning(s) </th> </tr>'.$result;
				if (!isset($json->{"@type"})){
					$this->message_output = '<tr class="first_line"><th></th><th class="field_name">'.$this->values->{'_type'}.'</th> <th class="object_errors">'.count($this->error).' error(s) & '.
					count($this->warning).' warning(s) </th> </tr>'.$result;
				}
			}
			else{
				array_push($this->error, 'UNSUPPORTED');
			}
		}
	}

	protected function make_spec($file_path)
	{
		$raw_spec = json_decode(file_get_contents($file_path));
		$properties = $raw_spec->{'properties'};
		$spec = array();

		foreach ($properties as $property_name => $property_spec) {

			$field_spec = array();

			if (in_array($property_name, $raw_spec->{'required'})){
				$field_spec['presence'] = 'required';
			}
			elseif (in_array($property_name, $raw_spec->{'recommended'})){
				$field_spec['presence'] = 'recommended';
			}
			elseif (in_array($property_name, $raw_spec->{'optional'})){
				$field_spec['presence'] = 'optional';
			}

			$field_spec['description'] = $property_spec->{'description'};

			if (sizeof($property_spec->{'oneOf'})==1){
				$field_spec['cardinality'] = false;
			}
			elseif (sizeof($property_spec->{'oneOf'})>1){
				$field_spec['cardinality'] = true;
			}

			$field_spec['type'] = array();

			foreach ($property_spec->{'oneOf'} as $type_value)
			{
				if (!in_array($type_value->{'type'}, $field_spec['type']) and $type_value->{'type'}!='array')
				{			
					if ($type_value->{'type'} =='object')
					{
						array_push($field_spec['type'], $type_value->{'type'});
						$field_values = array(); 
						foreach($type_value->{'properties'}->{'type'}->{'enum'} as $value)
						{
							array_push($field_values, str_replace('http://schema.org/', '', $value));
						}
						$field_spec['values'] = $field_values;
					}
					elseif ($type_value->{'type'} =='string')
					{
						if (isset($type_value->{'format'}))
						{
							array_push($field_spec['type'], $type_value->{'format'});
						}
						else
						{
							array_push($field_spec['type'], $type_value->{'type'});
						}

						if (isset($type_value->{'enum'}))
						{
							$field_spec['controlled_vocabulary'] = array();
							foreach ($type_value->{'enum'} as $vocabulary_item)
							{
								array_push($field_spec['controlled_vocabulary'], $vocabulary_item);
								
							}
						}
					}
				}
			}

			$spec[$property_name] =  $field_spec;
		}
		return $spec;
	}

	public function make_table()
	{
		return '<table class="bioschemas_validation">'.$this->message_output.'</table>';
	}

	protected function validate_json($json)
	{

		$padding = $this->sublevel*20;
		$padding = $padding.'px';
		$output = '';
		$output = '' ;		

		/* For each field in the json */
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
							$field_name=>"Multiple values not allowed");
						$output.='<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"> </td> <td class="field_name" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value error_field"> Multiple values are not allowed for that field </td> </tr>';
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
							$field_name=>"Required field missing");
					$output .= '<tr class="table_line"> <td class="fa first_col fa-times-circle" aria-hidden="true"></td><td class="field_name error_field" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value error_field"> A required field is missing </td> </tr>';
					array_push($this->error, $local_error);
				}
			}
			elseif ($field_value['presence'] == 'recommended'){
				if (!isset($json->{$field_name})){

					// RECOMMENDED FIELD MISSING ERROR
					$local_warning = array(
							$field_name=>"A recommended field missing");
					$output .= '<tr class="table_line"> <td class="fa first_col fa-exclamation-triangle" aria-hidden="true"></td><td class="field_name field_warning" style="padding-left:'.$padding.'">'.$field_name.'</td><td class="field_value field_warning"> A recommended field is missing </td> </tr>';
					array_push($this->warning, $local_warning);
				}
			}
		}
		return $output;
	}

	protected function process_object_field($field_value, $field_name, $level)
	{

		$field_name = str_replace('http://schema.org/', '', $field_name);
		$subobject = new BSCsubProcessor($field_value, $field_name, $level, $this->template_fields[$field_name]['values'], $this->template_fields[$field_name]['description'], $this->parent);

		if (count($subobject->error)>0){

			if ($this->template_fields[$field_name]['presence'] == 'required'){
				$error = array($field_name=>$subobject->error);
				array_push($this->error, $error);
			}
			else{
				$warning = array('field'=>$field_name,
							   'error'=>$subobject->error);
				array_push($this->warning, $warning);
			}
		}


		elseif (count($subobject->warning)>0){
			if ($this->template_fields[$field_name]['presence'] != 'required'){
				$warning = array($field_name=>$subobject->warning);
				array_push($this->warning, $warning);
			}
			else{
				$error = array('field'=>$field_name,
							   'error'=>$subobject->warning);
				array_push($this->error, $error);
			}
		}


		$message .= $subobject->message_output;
		return $message; 
	}

	protected function process_string_field($field_name, $field_value)
	{
		$$field_name = str_replace('_', '@', $field_name);
		$output = '';

		$padding = $this->sublevel*20;
		$padding = $padding.'px';
		$output = '';

		$edam_API_URL = 'https://biosphere.france-bioinformatique.fr/edamontology/';

		// UNSUPPORTED STRING FIELD WARNING
		if (!isset($this->template_fields[$field_name])){
			$output = $this->trigger_error($padding, $field_name, $field_value.' (this field is not supported by Bioschemas)');
		}


		elseif (in_array(typeof($field_value), $this->template_fields[$field_name]['type']))
		{

			if (!isset($this->template_fields[$field_name]['controlled_vocabulary']))
			{
				$output = $this->print_message($padding, $field_name, 'valid', $field_value);
			}

			// Start processing controlled vocabulary: size() = 1 => EDAM TERM if size > 1 => not EDAM TERM 
			else{

				// Deal with EDAM TERMS
				if (sizeof($this->template_fields[$field_name]['controlled_vocabulary']) == 1)
				{
					$expected_table = strtolower(str_replace('EDAM/','', $this->template_fields[$field_name]['controlled_vocabulary'])[0]);

					$term_id = str_replace('http://edamontology.org/' ,'',$field_value);
					$term = explode('_', $term_id);
					$edam_api = $edam_API_URL.$expected_table.'/'.$term[1].'/?media=json';

					if ($term[0]!=$expected_table)
					{
						$output = $this->trigger_error($padding, $field_name, 'Provided term is from the wrong table (got '.$term[0].' but expects '.$expected_table.')');				
					}

					else
					{
						$term_api_call = file_get_contents($edam_api);
						if($term_api_call==false){
							$output = $this->trigger_error($padding, $field_name, 'Provided ID does not exist (got '.$term[1].')');
						}
						else{
							$term_values = json_decode($term_api_call);
							$output = $this->print_message($padding, $field_name, 'valid', $term_values->{'title'}.' (id '.$term[1].')');
						}
					}
				}

				// Non EDAM CV
				elseif($this->template_fields[$field_name]['controlled_vocabulary'] > 1)
				{
					if (!in_array($field_value, $this->template_fields[$field_name]['controlled_vocabulary']))
					{
						$output = $this->trigger_error($padding, $field_name, $field_value.' is not compliant with controlled vocabulary');
					}
					else{
						$output = $this->print_message($padding, $field_name, 'valid', $field_value);
					}
				}

			}
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

	protected function print_message($padding, $field_name, $field_state, $field_value)
	{
		// Choosing the icon
		if ($field_state == 'valid')
		{
			$icon = 'fa-check-circle';
			$field_css_class = 'valid_field';
		}
		elseif ($field_state == 'warning')
		{
			$icon = 'fa-exclamation-triangle';
			$field_css_class = 'field_warning';
		}
		elseif ($field_state == 'error')
		{
			$icon = 'fa-times-circle';
			$field_css_class = 'error_field';
		}



		// Build output
		$output = '<tr class="table_line"> 
			<td class="fa first_col '.$icon.'" aria-hidden="true"> </td>
			<td class="field_name '.$field_css_class.'" style="padding-left:'.$padding.'">'.$field_name.'</td>
			<td class="field_value '.$field_css_class.'">'.$field_value.'</td>
		</tr>';

		return $output;
	}

	protected function trigger_error($padding, $field_name, $field_value)
	{

		if (json_decode($field_value) != null){ $field_value = json_decode($field_value);}

		if ($this->template_fields[$field_name]['presence'] == 'required')
		{	
			$output = $this->print_message($padding, $field_name, 'error', $field_value);
			$local_error = array(
				$field_name=>$field_value);
			array_push($this->error, $local_error);	
		}
		else
		{
			$output = $this->print_message($padding, $field_name, 'warning', $field_value);
			$local_warning = array(
				$field_name=>$field_value);
			array_push($this->warning, $local_warning);
		}
		return $output;
	} 

}


function typeof($val)
{
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

function isDate($date)
{
	if ($date == 'get' or (bool)strtotime($date) == false){
		return false;
	}
	else{
		return true;
	}

}


?>
