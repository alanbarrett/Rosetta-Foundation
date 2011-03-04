<?php

class Tasks
{
	/*
		Save to the database a new task. $tags is an array or tag_ids.
	*/
	var $s;
	
	function Tasks(&$smarty)
	{
		$this->s = &$smarty;
	}

	function create($content, $organisation_id, $tags)
	{
		$ret = false;
		$i = array();
		$i['content'] = '\''.$this->s->db->cleanse($content).'\'';
		$i['organisation_id'] = intval($organisation_id);
		$i['created_time'] = 'NOW()';
		if ($task_id = $this->s->db->Insert('task', $i))
		{
			$ret = $task_id;
			// The task has been created. Now save what it was tagged with.
			if ($tag_ids = $this->s->tags->parse($tags))
			{
				// We now have an array of tag_ids related to this task. Save this information to the datbase.
				foreach ($tag_ids as $tag_id)
				{
					$i = array();
					$i['task_id'] = intval($task_id);
					$i['tag_id'] = intval($tag_id);
					$i['created_time'] = 'NOW()';
					$this->s->db->Insert('task_tag', $i);
				}
			// todo, now test if it's working!
			}
		}
		return $ret;
	}

	public function getLatestTasks($nb_items = 10)
	{
		$ret = false;
		$q = 'SELECT id
				FROM task
				ORDER BY created_time DESC 
				LIMIT '.$this->s->db->cleanse($nb_items);
		if ($r = $this->s->db->Select($q))
		{
			$ret = array();
			foreach($r as $row)
			{
				// Add a new Job object to the array to be returned.
				$ret[] = new Task($this->s, $row['id']);
			}
		}
		return $ret;
	}
}