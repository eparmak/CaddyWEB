
<?php
$content = 'http://example.maiden2.local http://google.gr {
        @allowed_ip {
                remote_ip 1.1.1.1
                remote_ip 2.2.2.2
        }
        reverse_proxy {
                to 192.168.130.252:8096 http://1.1.1.1
                fail_duration 30s
                max_fails 3
                @allowed_ip
        }
}';

// Regex pattern to match multiple domains/subdomains with optional http://
$pattern = '/(?:http:\/\/)?([a-zA-Z0-9-]+\.[a-zA-Z0-9-]+\.[a-zA-Z]{2,}|[a-zA-Z0-9-]+\.[a-zA-Z]{2,})\b/';

if (preg_match_all($pattern, $content, $matches)) {
    // Extract the matched domains from the first capturing group
    $domains = array_map('trim', $matches[0]);
    
    // Output the matched domains
    print_r($domains);
} else {
    echo "No domains found.";
}
?>