<?php
class CaddyfileReader {
	
	public function getConfig($path) {
		$content = file_get_contents($path);
		//Getting Domain
		preg_match_all('/((?:[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}\s*)+)\{/',$content, $matches);

			$domains = array_map('trim', $matches[1]);
			$domains = str_replace(' ',',',$domains);
			$domains = $domains[0];
		preg_match('/^http:\/\/([a-zA-Z0-9-]+\.[a-zA-Z0-9-]+\.[a-zA-Z]{2,}|[a-zA-Z0-9-]+\.[a-zA-Z]{2,})/', $content, $matches);
			if ( !empty($matches[1]) ) {
				$httpOnly = true;
			}
		//Getting Allowed IPS
		preg_match_all('/remote_ip\s+([0-9\.]+)/', $content, $ipMatches);
		if (!empty($ipMatches[1])) {
			$allowedIps = $ipMatches[1];
			$iprestriction = true;
		}
		
		//getting upstreams
	if (preg_match('/reverse_proxy\s*\{([^}]+)\}/', $content, $reverseMatches)) {
		preg_match('/to\s+(.+?)\s*$/m', $reverseMatches[1], $toMatch);
		preg_match('/fail_duration\s+(\S+)/', $reverseMatches[1], $failMatch);
		preg_match('/max_fails\s+(\S+)/', $reverseMatches[1], $maxfailMatch);

		if (preg_match('/lb_policy\s+(\S+)\s/m', $reverseMatches[1], $matchLoadBalance)) {
			$loadbalance = true;
			$lb_policy = $matchLoadBalance[1];
		}
		if ( preg_match('/\btls\s+([^\s]+\.crt)\s+([^\s]+\.key)\b/',$content, $matches) ) {
			$customCert = [
				'tls' => true,
				'crt' => $matches[1],
				'key' => $matches[2],
			];
		}
		preg_match('/tls\s+([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/', $content, $tlsMatch);

		if ( preg_match('/transport\s+http\s*\{/', $reverseMatches[1], $loadbalanceMatch) ) {
			preg_match('/tls_insecure_skip_verify\s*(?=\s*[^a-zA-Z]|$)/', $reverseMatches[1], $tlsInsecureVerifyMatch);
		}
		if (!empty($toMatch[1])) {
				$reverseProxyIps = preg_split('/\s+/', trim($toMatch[1]));
				if ( count($reverseProxyIps) > 1 ) $failOver = true;
			}
		}
		$failDuration = $failMatch[1];
		$maxfails = $maxfailMatch[1];
		$letsencrypt = $tlsMatch[0];
		$tlsInsecureVerify = $tlsInsecureVerifyMatch[0];
		$config[] = [
			'filename' => basename($path),
			'domain' => $domains,
			'customCert' => $customCert['tls'],
			'customCertCrt' => $customCert['crt'],
			'customCertKey' => $customCert['key'],
			'upstreams' => $reverseProxyIps,
			'iprestriction' => $iprestriction,
			'allowedips' => $allowedIps,
			'failDuration' => $failDuration,
			'maxFails' => $maxfails,
			'loadbalance' => $loadbalance,
			'loadbalancemethod' => $lb_policy,
			'letsEncrypt' => $letsencrypt,
			'tlsInsecureVerify' => $tlsInsecureVerify,
			'httpOnly' => $httpOnly,
			'failover' => $failOver
		];
		//print_r($config);
		return $config;
	}
}


class CaddyfileWriter {
    private $filePath;
    private $configurations = [];

    public function __construct($filePath) {
        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {

        }
    }

    public function addConfiguration(
		$servername,
        $domain,
        $upstreams = [],
        $letsEncrypt = false,
		$letsEncryptmail,
		$customCert,
		$certFile,
        $insecureSkipVerify = false,
        $loadBalancing = [false, ''], 
        $failDuration,
        $maxFails,
        $httpOnly = false,
        $allowSpecificIPs = false,  
        $allowedIPs = [] 
    ) 
	{

		  if ( $httpOnly ) {
			  $domains = str_replace(',',' http://',$domain);
			  $domains = 'http://' . $domains;
		  }
		  else {
			  $domains = str_replace(',',' ',$domain);
		  }

		$upstreams = str_replace(',',' ',$upstreams);
		
		$config = $domains . " {\n";
			if ( $allowSpecificIPs ) {
				$config .= "	@allowed_ip {\n";
					foreach ( $allowedIPs as $ipaddr ) {
						$config .= "		remote_ip $ipaddr\n";
					}
				$config .= "	}\n";
			}
			if ( $letsEncrypt ) $config .= "tls $letsEncryptmail\n";
			if ( $customCert ) $config .= "	tls " . $certFile . " " . str_replace('.crt','.key',$certFile) . "\n";
			$config .= "	reverse_proxy {
		to " . implode(' ', $upstreams) . "
		fail_duration $failDuration
		max_fails $maxFails
	";
		if ($loadBalancing[0]) {
		$config .= "	lb_policy $loadBalancing[1]\n";
		}
		if ( $customCert) {
	$config .= "	transport http {\n";
		if ( $insecureSkipVerify ) $config .= "			tls_insecure_skip_verify\n";

		$config .= " 		}\n";
	}
	if ( $allowSpecificIPs ) $config .= "		@allowed_ip\n";
	$config .= " 	}\n";
	$config .= "}\n";
				
			
			file_put_contents('/etc/caddy/caddy.conf.d/' . $servername,$config);
	}

}


?>