<?php

header('Content-type: application/json; charset=utf-8');
echo json_encode(apache_request_headers());
