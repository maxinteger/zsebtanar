<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Angletypes2 {

	// Define type of angle
	function Generate($level) {

		$CI =& get_instance();
		$CI->load->helper('language');

		$options = array(
			'nullszög',
			'hegyesszög',
			'derékszög',
			'tompaszög',
			'egyenesszög',
			'homorúszög',
			'teljesszög',
			'forgásszög'
		);

		$angles = array(
			0,
			rand(1,89),
			90,
			rand(91,179),
			180,
			rand(181,359),
			360,
			rand(365,720)
		);

		$index 		= rand(0,count($angles)-1);

		$angle_type = $options[$index];
		$angle 		= $angles[$index];
		$solution 	= $angle_type;


		$question = 'Milyen típusú '.The($angle).' $'.$angle.'°$-os szög?';

		$CI->load->library('Angletypes');

		$explanation = $CI->angletypes->Explanation();
		
		shuffle($options);
		$correct = array_search($angle_type, $options);

		return array(
			'question' 	=> $question,
			'correct' 	=> $correct,
			'options' 	=> $options,
			'solution'	=> $solution,
			'explanation' => $explanation,
			'hint_replace' => TRUE
		);
	}
}

?>