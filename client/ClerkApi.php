<?

namespace clerk\client;

class ClerkApi {
	
	private $publicKey;
	private $privateKey;
	private $cache = array();
	
	private $cacheFile = "cache.json";
	
	private $authHeader = array(
		'Accept: application/json',
		'Content-Type: application/json');
			
	
	
	public function __construct($config) { 
		
		if($config && json_decode($config,true))
		{
			$config = json_decode($config, true);
			if(!array_key_exists("privateKey",$config) && !array_key_exists("publicKey", $config))
			{
				return false;
			}
		}
		else false;
		
		$this->publicKey = $config["publicKey"];
		$this->privateKey = $config["privateKey"];
		$this->readCache();
		$this->expire();
	
	}
	
	private function readCache() {
		
		$this->cache = json_decode(file_get_contents($this->cacheFile), true);
		
	}
	
	private function updateFile() {
		file_put_contents($this->cacheFile, json_encode($this->cache));
	}
	
	private function expire() {
		
		$currentTime = time();
		
		foreach($this->cache as $key => $item) {
			
			if($item["expires"] < $currentTime)
			{
				unset($this->cache[$key]);
			}		
			
		}
		
		$this->updateFile();

	}
	
	
	private function counter($key) {
		$this->cache[$key]["count"]++;
		$this->updateFile();
				
	}
	
	public function get($query) {
		
		if(array_key_exists($query, $this->cache))
		{
			$this->counter($query);
		}
		else {
			
			$endpoint = "http://api.clerk.io/v2/search/search";
			$post = array(
				"key" => $this->publicKey,
				"query" => $query,
				"language" => "english",
				"limit" => 10,
				"attributes" => array("name", "image", "url")
			);
			
			$response = $this->fetch($endpoint, $post);
			if($response) {
				
				
				$this->cacheIt($query, $response);
				
			}
			
			
		}
		
		return $this->cache[$query];
	}
	
	
	private function fetch($endpoint, $post) {
				
		$curl = curl_init();
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => $endpoint,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_HTTPHEADER => $this->authHeader,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($post),
		));
		
		$output = array();

		$response = curl_exec($curl);
		$err = curl_error($curl);
		if($err)
		{
			return false;
		}
		
		return json_decode($response);
		
		
	}
	
	private function cacheIt($key, $results) {
		
		$this->cache[$key] = array("expires" => time() + (2 * 24 * 60 * 60), "count" => 1, "data" => $results, );	
		$this->updateFile();
	
	}
	
	
	
	
	
	
	
	
	
	
}
