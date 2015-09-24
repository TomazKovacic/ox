<?php

	return array(

		/*
		|-----------------------
		|Application path
		|-----------------------
		*/

		'app' => realpath( ROOT_DIR .'/app'), 

		'controllers' => realpath(ROOT_DIR .'/app/controllers'),
		'models' => realpath(ROOT_DIR .'/app/models'),
		'views' => realpath(ROOT_DIR .'/app/views'),

		/*
		|-----------------------
		|Public path
		|-----------------------
		*/

		'public' => realpath(ROOT_DIR .'/public')



	);

