<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mean {

	// Class constructor
	function __construct() {

		$CI =& get_instance();
		$CI->load->helper('maths');
		$CI->load->helper('language');

		return;
	}

	// Define range, mean, standard deviation
	function Generate($level) {

		$set = $this->Set($level);

		$question = 'Határozza meg az alábbi adatsor átlagát!';
		$correct = array_sum($set)/count($set);
		$solution = '$'.$correct.'$';
		$type = 'int';
		$hints = $this->Hints($set);

		$question .= '$$'.implode(';', $set).'$$';

		return array(
			'question' 	=> $question,
			'correct' 	=> $correct,
			'solution'	=> $solution,
			'type'		=> $type,
			'hints' 	=> $hints
		);
	}

	function Set($level) {

		if ($level <= 3) {
			$length = rand(2,3);
			$average = rand(2,3);
		} elseif ($level <= 6) {
			$length = rand(4,6);
			$average = rand(4,6);
		} else {
			$length = rand(7,9);
			$average = rand(7,9);
		}

		$total = $average*$length; // Total amount to be distributed among numbers

		for ($i=0; $i < $length; $i++) {
			$num = ($total > 0 ? rand(1, $total) : 0);
			$set[] = $num;
			$total -= $num;
		}

		sort($set);

		return $set;
	}

	function Hints($set) {

		$mean = array_sum($set)/count($set);
		$page[] = 'Az átlaghoz először meg kell határoznunk az adatsor <b>összegét</b>:'
			.'<div class="text-center">$'.implode("$$+$$", $set).'='.strval(array_sum($set)).'$</div>';
		$page[] = 'Most osszuk el az összeget az adatsor <b>méretével</b>:'
			.'$$\frac{'.strval(array_sum($set)).'}{'.count($set).'}='.$mean.'$$';
		$page[] = 'Tehát az adatsor átlaga: <span class="label label-success">$'.$mean.'$</span>.';
		$hints[] = $page;

		return $hints;
	}
}

?>