<?php

function app() {

	return ox\Facades\Facade::getApplication();
}

function url() {
	return app()->config['url'];
}
