<?php

class Search
{
	protected $q;
	protected $sectionId;
	protected $results = NULL;

	public function __construct ($q = NULL, $sectionId = NULL)
	{
		if (func_num_args() > 0) {
			($this->q = $q) && ($this->sectionId = $sectionId);
		}
		else {
			$query = filter_input_array(INPUT_GET);
			($this->q = $query['q'] ?? '') && ($this->sectionId = $query['sectionId'] ?? NULL);
		}
	}

	public function getSearchResults ($maxCount = 25, $minScore = 30)
	{
		if (isset($this->results)) {
			return $this->results;
		}

		if (empty($q = strtolower($this->q))) {
			return $this->results = [];
		}

		$sectionId = $this->sectionId;
		$results   = $matches = [];

		if (empty($sectionId)) {

			$search = [];

			if (!empty($projectDirs = glob(\RenderDocs2PhpNet::getRootDir() . '*', GLOB_ONLYDIR))) {
				foreach ($projectDirs as $projectDir) {
					$sectionId = str_replace(\RenderDocs2PhpNet::getRootDir(), '', $projectDir);
					$project   = new \RenderDocs2PhpNet($sectionId);
					if (($score = $this->getSearchScore($q, [$name = $project->getMeta('name'), $desc = $project->getMeta('description')], 30)) !== FALSE) {
						$search[$sectionId]  = [$name, $desc];
						$matches[$sectionId] = $score;
					}
				}

				asort($matches);

				foreach ($matches as $sectionId => $score) {
					if (count($results) >= 25) {
						break;
					}

					$results[] = ['id' => $sectionId, 'text' => $search[$sectionId][0], 'score' => $score, 'desc' => $search[$sectionId][1]];
				}
			}

		}
		else {

			$project = new \RenderDocs2PhpNet($sectionId);
			$search  = $project->getSearch();

			foreach ($search as $path => $details) {
				if (($score = $this->getSearchScore($q, [$path, $details[0]], $minScore)) !== FALSE) {
					$matches[$path] = $score;
				}
			}

			asort($matches);

			foreach ($matches as $path => $score) {
				if (count($results) >= $maxCount) {
					break;
				}

				$results[] = ['id' => $path, 'text' => $path, 'score' => $score, 'desc' => htmlentities($search[$path][0]), 'refs' => $search[$path][1]];
			}
		}

		return $this->results = $results;
	}

	protected function getSearchScore ($needle, $haystacks, $minScore)
	{
		$score = $minScore + 1;

		foreach ($haystacks as $haystack) {
			if (!empty($haystack)) {
				$haystack     = substr(strtolower($haystack), 0, 255);
				$literal      = !(strpos($haystack, $needle) === FALSE);
				$currentScore = levenshtein($needle, $haystack, 1, 10, 100) + ($literal ? 0 : 10);
				$currentScore = $literal ? min($currentScore, $minScore) : $currentScore;
				$score        = min($score, $currentScore);
			}
		}

		if ($score > $minScore) {
			return FALSE;
		}

		return $score;
	}

	static public function getUrl ($path, $refs)
	{
		$nameIndex = strrpos($path, '\\');
		$ns        = $nameIndex !== FALSE ? substr($path, 0, $nameIndex) : '';
		$ns        = implode('-', array_filter(explode('\\', $ns))) ?: 'global';
		[$name, $attr] = array_pad(explode('::', $nameIndex !== FALSE ? substr($path, $nameIndex + 1) : $path), 2, NULL);
		if (!empty($refs)) {
			$url = \Urls::getDocUrl() . '/' . $ns . '/' . $refs[0] . '/' . $name;
		}
		else {
			$url = \Urls::getDocUrl() . '/' . $name;
		}
		$attr && count($refs) > 1 && ($url .= '/' . $refs[1] . '/' . $attr);

		return $url;
	}
}