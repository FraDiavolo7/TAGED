<?php

include '../src/define.php';

WebServices::handle ( Form::getData ( TagedWS::WS_SELECTOR, TagedWS::WS_DEFAULT ), $_REQUEST );

