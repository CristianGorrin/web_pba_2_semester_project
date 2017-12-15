<?php
/*
1. Show groups to user
2. Get selected group's longname, start, end and lessons
3. Get a lesson's subject
4. Get the subject's name and lessons
5. Remove subject's lessons from group's lessons
6. Repeat 3-6 until no more lessons
7. Show group's subjects to user
 */
class WebUntisAPI {
	private $base_url     = 'https://api.webuntis.dk/api/';
	private $api_key      = 'b4be51b712295d7e9dd122239a1826eb3d176a3d';
	private $call_counter = 0;

	private $headers      = [];
	private $curl_options = [];
	private $response     = false;

	public function __construct() {
		$this->SetHeader('X-API-KEY', $this->GetApiKey());

		$this->SetCurlOption(CURLOPT_RETURNTRANSFER, 1);
		$this->SetCurlOption(CURLOPT_SSL_VERIFYHOST, 0);
		$this->SetCurlOption(CURLOPT_SSL_VERIFYPEER, 0);
	}

	public function Call($path, $query = [], $method = 'GET') {
		$curl_handle = curl_init();

		// Build URL
		$query_string = '';
		foreach($query as $k => $v) {
			$query_string .= ($query_string == '' ? '?' : '&');
			$query_string .= $k . '=' . $v;
		}

		$url = $this->GetBaseUrl() . $path . $query_string;

		// Build headers
		$headers = [];
		foreach($this->GetHeaders() as $k => $v) {
			$headers[] = $k . ': ' . $v;
		}

		// Set cURL options
		$this->SetCurlOption(CURLOPT_HTTPHEADER,    $headers);
		$this->SetCurlOption(CURLOPT_URL,           $url);
		$this->SetCurlOption(CURLOPT_CUSTOMREQUEST, $method);

		curl_setopt_array($curl_handle, $this->GetCurlOptions());


		$this->SetResponse(curl_exec($curl_handle));

		$this->IncrementCallCounter();

		return $this->GetResponse();
	}

	private function IncrementCallCounter() {
		$this->call_counter++;
	}

	// Abstraction  layer
	public function ClassInfo($name) {
		$group = new StdClass();

		$group->name = $name;


		// Fetch group info
		$r = $this->Call('groups/' . $group->name, ['id_field' => 'name']);

		$group->id       = (int) $r->untis_id;
		$group->longname = $r->longname;
		$group->start    = new DateTime($r->start);
		$group->end      = new DateTime($r->end);

		$group->lessons   = [];
		$group->subjects  = [];
		$group->teachers  = [];
		$group->locations = [];


		// Fetch group lessons
		$r = $this->Call('groups/' . $group->id . '/lessons');

		$lessons = [];
		foreach($r as $key => $l) {
			$lessons[floor($key / 200)][] = $l->untis_id;
		}

		$temp = [
			's' => [],
			't' => [],
			'l' => []
		];
		foreach($lessons as $ids) {
			$r = $this->Lessons($ids);

			foreach($r as $l) {
				$group->lessons[$l->untis_id] = [
					'start'     => $l->start,
					'end'       => $l->end,
					'subjects'  => $l->subjects,
					'teachers'  => $l->teachers,
					'locations' => $l->locations
				];

				if(!empty($l->subjects)) {
					foreach($l->subjects as $ls) {
						if(!in_array($ls, $temp['s'])) {
							$temp['s'][] = $ls;
						}
					}
				}

				if(!empty($l->subjects)) {
					foreach($l->teachers as $lt) {
						if(!in_array($lt, $temp['t'])) {
							$temp['t'][] = $lt;
						}
					}
				}

				if(!empty($l->locations)) {
					foreach($l->locations as $ll) {
						if(!in_array($ll, $temp['l'])) {
							$temp['l'][] = $ll;
						}
					}
				}
			}
		}

		$data_requests = [];

		foreach($temp as $type => $ids) {
			$data_requests[$type] = '';

			foreach($ids as $id) {
				$data_requests[$type] .= $data_requests[$type] != '' ? ',' : '';
				$data_requests[$type] .= $id;
			}
		}

		$r = $this->Subjects($temp['s']);

		foreach($r as $s) {
			$group->subjects[$s->untis_id] = [
				'name'     => $s->name,
				'longname' => $s->longname
			];
		}


		$r = $this->Teachers($temp['t']);

		foreach($r as $t) {
			$group->teachers[$t->untis_id] = [
				'name'     => $t->name,
				'forename' => $t->forename,
				'longname' => $t->longname,
				'email'    => $t->email
			];
		}


		$r = $this->Locations($temp['l']);

		foreach($r as $l) {
			$group->locations[$l->untis_id] = [
				'name'     => $l->name,
				'longname' => $l->longname
			];
		}

		return $group;
	}

	public function Subjects($input = NULL) {
		$path = 'subjects';

		if($input === NULL) {
			$response = $this->Call($path);
		} else if(is_string($input)) {
			$response = $this->Call($path . '/' . $input, ['id_field' => 'name']);
		} else {
			if(is_array($input)) {
				$ids = '';

				foreach($input as $id) {
					$ids .= $ids != '' ? ',' : '';
					$ids .= $id;
				}
			} else {
				$ids = $input;
			}

			$response = $this->Call($path, ['untis_ids' => $ids]);
		}

		return $response;
	}

	public function Teachers($input = NULL) {
		$path = 'teachers';

		if($input === NULL) {
			$response = $this->Call($path);
		} else if(is_string($input)) {
			$response = $this->Call($path . '/' . $input, ['id_field' => 'name']);
		} else {
			if(is_array($input)) {
				$ids = '';

				foreach($input as $id) {
					$ids .= $ids != '' ? ',' : '';
					$ids .= $id;
				}
			} else {
				$ids = $input;
			}

			$response = $this->Call($path, ['untis_ids' => $ids]);
		}

		return $response;
	}

	public function Locations($input = NULL) {
		$path = 'locations';

		if($input === NULL) {
			$response = $this->Call($path);
		} else if(is_string($input)) {
			$response = $this->Call($path . '/' . $input, ['id_field' => 'name']);
		} else {
			if(is_array($input)) {
				$ids = '';

				foreach($input as $id) {
					$ids .= $ids != '' ? ',' : '';
					$ids .= $id;
				}
			} else {
				$ids = $input;
			}

			$response = $this->Call($path, ['untis_ids' => $ids]);
		}

		return $response;
	}

	public function Lessons($input = NULL) {
		$path = 'lessons';

		if($input === NULL) {
			$response = $this->Call($path);
		} else {
			if(is_array($input)) {
				$ids = '';

				foreach($input as $id) {
					$ids .= $ids != '' ? ',' : '';
					$ids .= $id;
				}
			} else {
				$ids = $input;
			}

			$response = $this->Call($path, ['untis_ids' => $ids]);
		}

		return $response;
	}


	// Getters/setters
	public function GetCallAmount() {
		return $this->call_counter;
	}

	private function GetBaseUrl() {
		return $this->base_url;
	}

	private function GetApiKey() {
		return $this->api_key;
	}

	public function GetHeaders() {
		return $this->headers;
	}

	public function SetHeader($key, $value = NULL) {
		if($value === NULL) {
			unset($this->headers[$key]);
		} else {
			$this->headers[$key] = $value;
		}
	}

	public function GetCurlOptions() {
		return $this->curl_options;
	}

	public function SetCurlOption($key, $value = NULL) {
		if($value === NULL) {
			unset($this->curl_options[$key]);
		} else {
			$this->curl_options[$key] = $value;
		}
	}

	public function GetResponse() {
		return $this->response;
	}

	private function SetResponse($response) {
		$this->response = json_decode($response);
	}
}

//$api = new WebUntisAPI();
//$class = $api->ClassInfo('opbwu17fint');
