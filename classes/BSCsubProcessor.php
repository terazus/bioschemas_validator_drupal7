<?php

class BSCsubProcessor extends BSCProcessor{

	var $sublevel;
	var $field_name;
	var $parent;

	function __construct($json, $field_name, $level, $type_expected, $field_description, $parent)
	{
		$field_name = str_replace('https://schema.org/', '', str_replace('http://schema.org/', '', str_replace('_', '@', $field_name))) ; 
		if (!isset($json))
		{
			$this->errors=TRUE;
		}

		else 
		{
			$this->parent = $parent;
			$this->values = $json;
			$this->field_name = $field_name;
			$this->sublevel = $level;
			$padding = 20*($this->sublevel-1);
			$padding_plus = $padding+20;
			$padding = $padding.'px';
			$padding_plus = $padding_plus.'px';

			$path = './sites/all/modules/CUSTOM/bioschemas_crawler/specs/default/'.$parent.'/';
			$spec_path = $path.strtolower(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"}))).'.json';			
			$this->template_fields = $this->make_spec($spec_path);

			// Subobject template can be loaded
			if ($this->template_fields!=null)
			{

				/* Let's first check if the provided @type value is the expected type for this field */
				if ((!in_array(str_replace('http://schema.org/','',str_replace('https://schema.org/', '', $json->{"@type"})), $type_expected) and isset($type_expected)) or !isset($type_expected))
				{
					$result = $this->trigger_error($padding_plus, '@type', $json->{'@type'}.' is not a valid target for this field');
				}

				else
				{
					$result = $this->print_message($padding_plus, '@type', 'valid', $json->{'@type'});
				}

				/* Now we validate the subJSON */
				$result .= $this->validate_json($this->values);

				/* There is something in the error attribute*/
				if (count($this->error)>0)
				{
					$output = $this->trigger_error($padding, $field_name, json_encode($this->error[0]['error']));
					$this->message_output = $output.' '.$result;
				}

				/* There is something in the warning attribute */
				elseif (count($this->warning)>0) 
				{
					dpm($this->warning[0]['field']);
					$output = $this->trigger_error($padding, $field_name, 'Problem with subfield '.json_encode($this->warning[0]['field']));
					$this->message_output = $output.' '.$result;
				}

				/* There's no error and no warning */
				else 
				{
					$this->message_output = $this->print_message($padding, $this->field_name, 'valid', '').' '.$result;
				}
			}

			/* Couldn't load the template : the field isn't supported by Bioschemas */
			else
			{
				$result = $this->validate_json($this->values);
				$this->message_output = $this->trigger_error($padding, $field_name, 'This field is not supported by Bioschemas').' '.$result;
			}
		}
	}	
}

?>