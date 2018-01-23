<?php
use NoahBuscher\Macaw\Macaw;

Macaw::get('/', 'App\Controllers\demo@index');

Macaw::error(function() {
    echo '404 :: Not Found';
    //throw_exception(Errors::BAD_REQUEST);
});

Macaw::dispatch();
