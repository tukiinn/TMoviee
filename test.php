<?php
$data = '554|250529_184|299000|-49';
$key2 = 'uUfsWgfLkRLzq6W2uNXTCxrfxs51auny';
echo hash_hmac('sha256', $data, $key2);