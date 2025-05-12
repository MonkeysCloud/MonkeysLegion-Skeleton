<?php

return [
    // Which origins to allow; use "*" or an array of domains.
    'allow_origin'  => '*',

    // Which methods to permit.
    'allow_methods' => ['GET','POST','PUT','DELETE','OPTIONS'],

    // Which request headers browsers may send.
    'allow_headers' => ['Content-Type','Authorization'],
];