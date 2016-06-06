# api-bgp
An API to expose BGP information given an IP address

## How It Works

This API uses PHP to make DNS requests to the [IP To ASN Mapping](http://www.team-cymru.org/IP-ASN-mapping.html) service that [Team Cymru](http://www.team-cymru.com/) operates.

## Usage

```
/
  returns HTML usage documentation
/api/v1/bgp/
  returns $REMOTE_ADDR prefix and source asn in format "prefix $prefix advertised by $asn"
/api/v1/bgp/<IP>
  returns $IP prefix and source asn in format "prefix $prefix advertised by $asn"
/api/v1/bgp/asn/
  returns $REMOTE_ADDR source asn in format "$asn"
/api/v1/bgp/asn/<IP>
  returns $IP source asn in format "$asn"
/api/v1/bgp/prefix/
  returns $REMOTE_ADDR prefix in format "$prefix"
/api/v1/bgp/prefix/<IP>
  returns $IP prefix in format "$prefix"
```

## Requirements

*	nginx
*	php

## Server Configuration

```

# Redirect everything to the main site.
server {
    listen 192.241.199.179:80; 
    listen [2604:a880:1:20::8a:b001]:80;

    server_name api.hextet.net;
    root /var/www/api-hextet-net;

    charset utf-8;

    access_log /var/log/nginx/hextet_net_access.log;
    error_log /var/log/nginx/hextet_net_error.log;

    return         301 https://$server_name$request_uri;

}

server {
    listen 192.241.199.179:443 ssl http2;
    listen [2604:a880:1:20::8a:b001]:443 ssl http2;

    add_header Strict-Transport-Security "max-age=31536000";
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;

    server_name api.hextet.net;

    root /var/www/api-hextet-net;

    charset utf-8;

#   error_page    404 = /index.html;

    access_log /var/log/nginx/api_hextet_net_access.log;
    error_log /var/log/nginx/api_hextet_net_error.log;

    ssl on;
        ssl_dhparam /etc/nginx/ssl/dhparam.pem;

    ssl_certificate /etc/letsencrypt/live/api.hextet.net/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.hextet.net/privkey.pem;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers 'ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:DHE-RSA-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:kEDH+AESGCM:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DHE-RSA-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA256:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA:DHE-RSA-AES256-SHA:ECDHE-RSA-DES-CBC3-SHA:ECDHE-ECDSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:AES128-GCM-SHA256:AES256-GCM-SHA384:AES128-SHA256:AES256-SHA256:AES128-SHA:AES256-SHA:AES:CAMELLIA:DES-CBC3-SHA:!aNULL:!eNULL:!EXPORT:!DES:!RC4:!MD5:!PSK:!aECDH:!EDH-DSS-DES-CBC3-SHA:!KRB5-DES-CBC3-SHA';

    ssl_prefer_server_ciphers   on;

    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.4.4 8.8.8.8 valid=300s;
    resolver_timeout 10s;

    # nginx 1.5.9+ ONLY
    ssl_buffer_size 1400; # 1400 bytes to fit in one MTU

    # SPDY header compression (0 for none, 1 for fast/less compression, 9 for slow/heavy compression)
    #spdy_headers_comp 6;



    ssl_session_timeout 10m;
    ssl_session_cache  builtin:1000  shared:SSL:10m;


    location = / {
        index index.php index.html;
        try_files $uri $uri/ /index.php /index.html;
    }

    location / {
        try_files $uri $uri/ =404;
    }


    location = /api/v1/bgp/asn/ {
        rewrite ^/api/v1/bgp/asn/?$ /cymru.php?l=asn break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location /api/v1/bgp/asn/ {
        rewrite ^/api/v1/bgp/asn/([a-z0-9A-Z\.\:]+)/?$ /cymru.php?l=asn&ip=$1 break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location = /api/v1/bgp/prefix/ {
        rewrite ^/api/v1/bgp/prefix/?$ /cymru.php?l=prefix break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location /api/v1/bgp/prefix/ {
        rewrite ^/api/v1/bgp/prefix/([a-z0-9A-Z\.\:]+)/?$ /cymru.php?l=prefix&ip=$1 break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location = /api/v1/bgp/ {
        rewrite ^/api/v1/bgp/?$ /cymru.php?ip=$1 break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location /api/v1/bgp/ {
        rewrite ^/api/v1/bgp/([a-z0-9A-Z\.\:]+)/?$ /cymru.php?ip=$1 break;
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  /var/www/api-hextet-net/$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_pass php;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
#       fastcgi_intercept_errors on;
        fastcgi_pass php;
    }

}

```
