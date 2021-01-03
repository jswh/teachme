<?php
$private_key = getenv('OAUTH_PRIVATE_KEY');
$public_key = getenv('OAUTH_PUBLIC_KEY');
$dir = __DIR__;
file_put_contents($dir . '/storage/oauth-private.key', $private_key);
file_put_contents($dir . '/storage/oauth-public.key', $public_key);
