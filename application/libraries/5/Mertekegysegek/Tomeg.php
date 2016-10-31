<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tomeg {

	// Class constructor
	function __construct() {

		$CI =& get_instance();
		$CI->load->helper('maths');
		$CI->load->helper('language');

		return;
	}

	// Get value of sausage
	function Generate($level) {

		$units = array(
			array(
				'short' => 'g',
				'long'	=> 'gramm',
				'long2'	=> 'grammnak',
				'mult'	=> 10
				),
			array(
				'short' => 'dkg',
				'long'	=> 'dekagramm',
				'long2'	=> 'dekagrammnak',
				'mult'	=> 100
				),
			array(
				'short' => 'kg',
				'long'	=> 'kilogramm',
				'long2'	=> 'kilogrammnak',
				'mult'	=> 1000
				),
			array(
				'short' => 't',
				'long'	=> 'tonna',
				'long2'	=> 'tonnának',
				)
			);

		if ($level <= 3) {
			$indexFrom 	= rand(1,2);
			$indexTo 	= $indexFrom - rand(1,1);
			$value 		= rand(1,9);
		} elseif ($level <= 6) {
			$indexFrom 	= rand(2,3);
			$indexTo 	= $indexFrom - rand(1,2);
			$value 		= rand(10,99);
		} else {
			$indexFrom 	= rand(3,3);
			$indexTo 	= $indexFrom - rand(2,3);
			$value 		= rand(100,999);
		}

		// Calculate multiplier
		$mult = 1;
		for ($i=$indexFrom; $i > $indexTo; $i--) { 
			$mult *= $units[$i-1]['mult'];
		}

		// Switch direction
		if (rand(1,2) == 1) {
			list($indexFrom, $indexTo) = array($indexTo, $indexFrom);
			$correct = $value;
			$value *= $mult;
		} else {
			$correct = $value * $mult;
		}

		$valueText		= BigNum($value);
		$correctText	= BigNum($correct);

		$unitFrom	= $units[$indexFrom];
		$unitTo 	= $units[$indexTo];

		$question = 'Számoljuk ki, hogy $'.$valueText.'$ '.$unitFrom['long'].' hány '.$unitTo['long2'].' felel meg!';

		$solution = '$'.$correctText.'\,\text{'.$unitTo['long'].'}$';

		$hints = $this->Hints($units, $indexFrom, $indexTo, $value);

		return array(
			'question' 	=> $question,
			'correct' 	=> $correct,
			'solution'	=> $solution,
			'hints'		=> $hints,
			'labels'	=> ['right' => '$\text{'.$unitTo['short'].'}$']
		);
	}

	function Hints($units, $indexFrom, $indexTo, $value) {

		// First page with details
		$page[] = $this->UnitsSummary($units);

		$details = 'Ez azt jelenti, hogy:<ul>';

		for ($i=0; $i < count($units)-1; $i++) {

			$mult 		= $units[$i]['mult'];
			$unitFrom1 	= $units[$i]['short'];
			$unitFrom2 	= $units[$i]['long'];
			$unitTo1 	= $units[$i+1]['short'];
			$unitTo2 	= $units[$i+1]['long2'];
			$end 		= ($i<count($units)-2 ? ',' : '.');

			$details .= '<li>$'.$mult.'\,\text{'.$unitFrom1.'}=1\,\text{'.$unitTo1.'}$, azaz $'.$mult.'$ '.$unitFrom2.' $1$ '.$unitTo2.' felel meg'.$end.'</li>';

		}

		$details .= '</ul>';

		$page[] = [$details];
		$hints[] = $page;

		// Additional pages

		if ($indexFrom > $indexTo) {

			for ($i=$indexFrom; $i > $indexTo; $i--) {

				$mult 		= $units[$i-1]['mult'];
				$unitFrom 	= $units[$i]['short'];
				$unitTo 	= $units[$i-1]['short'];
				$valueNew 	= $value * $mult;

				$valueText		= BigNum($value);
				$valueNewText	= BigNum($valueNew);

				$result 	= ($i == $indexTo+1 ? '$<span class="label label-success">$'.$valueNewText.'$</span>$' : $valueNewText); 

				$hints[][] 	= $this->UnitsSummary($units).'Az ábráról leolvasható, hogy $1\,\text{'.$unitFrom.'}='.$mult.'\,\text{'.$unitTo.'}$, azaz<br /><br /><div class="text-center">$'.$valueText.'\,\text{'.$unitFrom.'}='.$valueText.'\cdot'.$mult.'\,\text{'.$unitTo.'}='.$result.'\,\text{'.$units[$i-1]['short'].'}$</div>';

				$value = $valueNew;
			}

		} else {

			for ($i=$indexFrom; $i < $indexTo; $i++) { 

				$mult 		= $units[$i]['mult'];
				$unitTo 	= $units[$i+1]['short'];
				$unitFrom 	= $units[$i]['short'];
				$valueNew 	= $value / $mult;

				$valueText		= BigNum($value);
				$valueNewText	= BigNum($valueNew);

				$result 	= ($i == $indexTo-1 ? '$<span class="label label-success">$'.$valueNewText.'$</span>$' : $valueNewText); 

				$hints[][] = $this->UnitsSummary($units).'Az ábráról leolvasható, hogy $'.$mult.'\,\text{'.$unitFrom.'}=1\,\text{'.$unitTo.'}$, azaz<br /><br /><div class="text-center">$'.$valueText.'\,\text{'.$unitFrom.'}='.$valueText.':'.$mult.'\,\text{'.$unitTo.'}='.$result.'\,\text{'.$unitTo.'}$</div>';

				$value = $valueNew;
			}
		}

		return $hints;
	}

	function UnitsSummary($units) {

		$text = '<div class="alert alert-info"><strong>Tömeg-mértékegységek</strong>$$';

		foreach ($units as $index => $unit) {
			$text .= '\text{'.$unit['short'].'}';
			if ($index < count($units)-1) {
				$text .= '\overset{\small{\cdot'.$unit['mult'].'}}{\longrightarrow}';
			}
		}

		$text .= '$$</div>';

		return $text;
	}
}

?>