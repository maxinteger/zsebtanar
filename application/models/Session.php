<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session extends CI_model {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct() {

		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->model('Exercises');
		$this->load->model('Database');

		return;
	}

	/**
	 * Start session
	 *
	 * Define session ID and initialize session variables.
	 *
	 * @return void
	 */
	public function startSession() {

		// $this->session->unset_userdata('sessionID');

		if (NULL === $this->session->userdata('sessionID')) {
			
			if (!$this->db->insert('sessions', '')) {
				show_error($this->db->_error_message());
			}
			$sessionID = $this->db->insert_id();

			$this->session->set_userdata('sessionID', $sessionID);

		}

		if (NULL === $this->session->userdata('Logged_in')) {
			
			$this->session->set_userdata('Logged_in', FALSE);

		}

		return;
	}

	/**
	 * Record action
	 *
	 * Data is recorded when user attempts to solve an exercise.
	 *
	 * @param  int 	  $id     Subtopic/Exercise ID
	 * @param  int    $level  Exercise level
	 * @param  string $result Result (correct/wrong/not_done)
	 *
	 * @return void
	 */
	public function recordAction($id, $level=NULL, $result=NULL) {

		$query 		= $this->db->get_where('exercises', array('id' => $id));
		$exercise 	= $query->result()[0];
		$name 		= $exercise->name;
		$level_max	= $exercise->level;

		$data['questID'] 	= $this->session->userdata('questID');
		$data['level']		= $level;
		$data['level_max']	= $level_max;
		$data['result']		= $result;
		$data['name'] 		= $name;

		$todo_length		= count($this->session->userdata('todo_list'));
		$data['todo'] 		= min(0, $todo_length-1);

		if (!$this->db->insert('actions', $data)) {
			show_error($this->db->_error_message());
		}

		return;
	}

	/**
	 * Update results
	 *
	 * Increases user level in case of correct answer.
	 * Decreases user level in case of wrong answer (if possible).
	 *
	 * @param  int 	  $id        Subtopic/Exercise ID
	 * @param  int    $level     Current exercise level
	 * @param  string $result    Result of action (correct/wrong/not_done)
	 *
	 * @return void
	 */
	public function UpdateResults($id, $level=NULL, $result=NULL) {

		$query 		= $this->db->get_where('exercises', array('id' => $id));
		$level_max 	= $query->result()[0]->level;
		$results 	= $this->session->userdata('results');

		if ($result == 'CORRECT') {

			$results[$id] = $level;
			$this->session->set_userdata('results', $results);	

		} elseif ($result == 'WRONG' && 
			isset($results[$id]) &&
			$results[$id] == $level_max) {

			$results[$id] = $level-1;
			$this->session->set_userdata('results', $results);
		}

		return;
	}

	/**
	 * Get user levels for current exercise
	 *
	 * Returns an array with 0s and 1s. 1 means the user has answered the exercise
	 * at the specific level correctly, O means the opposite. Data is used to update 
	 * star icons with javascript.
	 *
	 * @param  int 	 $id     Exercise ID
	 * @return array $levels Exercise levels (0 or 1)
	 */
	public function getUserLevels($id) {

		$query = $this->db->get_where('exercises', array('id' => $id));
		$exercise = $query->result()[0];
		$max_level = $exercise->level;

		$results = $this->session->userdata('results');
		if (isset($results[$id])) {
			$user_level = $results[$id];
		} else {
			$user_level = 0;
		}

		for ($i=1; $i <= $max_level; $i++) { 
			if ($i <= $user_level) {
				$levels[$i] = 1;
			} else {
				$levels[$i] = 0;
			}
		}

		return $levels;
	}

	/**
	 * Clear exercise results of subtopics from session
	 *
	 * @param  int 	$subtopicID Subtopic ID
	 * @return void
	 */
	public function clearResults($subtopicID) {

		$results = $this->session->userdata('results');

		$query = $this->db->get_where('exercises', array('subtopicID' => $subtopicID));
		foreach ($query->result() as $exercise) {

			$id = $exercise->id;
			if (isset($results[$id])) {
				unset($results[$id]);
			}
		}

		$this->session->set_userdata('results', $results);

		return;
	}

	/**
	 * Get next level of exercise
	 *
	 * @param  int $id    Exercise ID
	 * @return int $level Exercise level 
	 */
	public function getExerciseLevelNext($id) {

		$query = $this->db->get_where('exercises', array('id' => $id));
		$exercise = $query->result()[0];
		$level_max = $exercise->level;

		if (isset($this->session->userdata('results')[$id])) {

			$level_user = $this->session->userdata('results')[$id];
			$level = min($level_max, $level_user+1);

		} else {

			$level = 1;
		}

		return $level;
	}

	/**
	 * Save exercise ID
	 *
	 * Saves exercise ID to list. 
	 * Next exercise will be chosen according to the to the saved list.
	 *
	 * @param  int $id Exercise ID
	 * @return void
	 */
	public function saveExerciseID($id) {

		if (NULL !== $this->session->userdata('method')) {

			$method = $this->session->userdata('method');

			if ($method == 'exercise') {

				$todo_list = $this->session->userdata('todo_list');

				if (!in_array($id, $todo_list)) {
					array_push($todo_list, $id);
				}

				$this->session->set_userdata('todo_list', $todo_list);

			}
		}
		
		return;
	}

	/**
	 * Print session information
	 *
	 * @return void
	 */
	public function PrintInfo() {

		print_r('Session ID: '.$this->session->userdata('sessionID').'<br />');
		print_r('Quest ID: '.$this->session->userdata('questID').'<br />');
		print_r('Method: '.$this->session->userdata('method').' - ');
		print_r($this->session->userdata('goal').'<br />'.'To do list: ');
		print_r($this->session->userdata('todo_list'));
		print_r('<br />Results: ');
		print_r($this->session->userdata('results'));

		return;
	}

	/**
	 * Update to do list
	 *
	 * 1. Removes all exercises after current one (if any).
	 * 2. Adds exercise if not in to do list yet.
	 * 3. Removes exercise if user has completed it in all level.
	 *
	 * @param int $id Exercise ID
	 *
	 * @return void
	 */
	public function UpdateTodoList($id) {

		$level_max  = $this->Exercises->getMaxLevel($id);
		$level_user = $this->Exercises->getUserLevel($id);

		$todo_list = $this->session->userdata('todo_list');
		$results = $this->session->userdata('results');

		if (in_array($id, $todo_list)) {

			$index = array_search($id, $todo_list);

			if ($level_user == $level_max) {
				// Removes current & following exercises
				$todo_list = array_slice($todo_list, 0, $index);
			} else {
				// Removes following exercises
				$todo_list = array_slice($todo_list, 0, $index+1);
			}

		} else {

			if ($level_user < $level_max) {
				// Appends exercise to end of to do list
				$todo_list[] = $id;
			}
		}

		$todo_list = $this->session->set_userdata('todo_list', $todo_list);
		
		return;
	}

	/**
	 * Start quest
	 *
	 * Data is recorded when user starts quest (i.e. sets learning goal).
	 *
	 * @return void
	 */
	public function StartQuest() {

		$method = $this->session->userdata('method');
		$id 	= $this->session->userdata('goal');

		if ($method == 'exercise') {

			$query = $this->db->get_where('exercises', array('id' => $id));
			$class = $this->Database->GetClassName($id, 'exercise');

		} elseif ($method == 'subtopic') {

			$query = $this->db->get_where('subtopics', array('id' => $id));
			$class = $this->Database->GetClassName($id, 'subtopic');
			
		}

		$data['name'] 		= $query->result()[0]->name;
		$data['sessionID'] 	= $this->session->userdata('sessionID');
		$data['method'] 	= $method;
		$data['class'] 		= $class;
		$data['status'] 	= 'STARTED';

		if (!$this->db->insert('quests', $data)) {
			show_error($this->db->_error_message());
		}

		$questID = $this->db->insert_id();
		$this->session->set_userdata('questID', $questID);

		return;
	}

	/**
	 * Clear session
	 *
	 * Unsets session variables when quest is finished.
	 *
	 * @return void
	 */
	public function ClearSession() {

		$this->session->unset_userdata('method');
		$this->session->unset_userdata('goal');
		$this->session->unset_userdata('todo_list');

		return;
	}

	/**
	 * Complete quest
	 *
	 * Data is updated when user starts quest (i.e. sets learning goal),
	 * or completes it.
	 *
	 * @return void
	 */
	public function CompleteQuest() {

		$questID = $this->session->userdata('questID');

		$query = $this->db->get_where('quests', array('id' => $questID));
		$id = $query->result()[0]->id;

		$data['status'] = 'COMPLETED';

		$this->db->where('id', $id);
		$this->db->update('quests', $data); 

		$this->session->unset_userdata('questID');
		$this->session->unset_userdata('method');
		$this->session->unset_userdata('goal');

		return;
	}
}

?>