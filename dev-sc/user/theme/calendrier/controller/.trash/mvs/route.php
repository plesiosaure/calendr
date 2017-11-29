<?php

respond(function ($request, $response, $router) {
	global $app;
	$router->app = $app;
});

// VILLE ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

respond('GET', '/ville/import', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'villeImport', 'args' => $request->params());
});

respond('GET', '/ville/[id|_id:method]/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'villeGet', 'args' => $request->params());
});

respond('PUT', '/ville', function($request, $response, $router){
	parse_str(file_get_contents("php://input"), $put);
	$args = array_merge($request->params(), $put);

	return array('controller' => 'calendrierMvs', 'action' => 'villeCreation', 'args' => $args);
});

respond('DELETE', '/ville/([id|_id:method])/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'villeDelete', 'args' => $request->params());
});

respond('POST', '/ville/([id|_id:method])/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'villeUpdate', 'args' => $request->params());
});



// ORGANISATEUR/////////////////////////////////////////////////////////////////////////////////////////////////////////

respond('GET', '/organisateur/import', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'organisateurImport', 'args' => $request->params());
});

respond('GET', '/organisateur/[id|_id:method]/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'organisateurGet', 'args' => $request->params());
});

respond('PUT', '/organisateur', function($request, $response, $router){
	parse_str(file_get_contents("php://input"), $put);
	$args = array_merge($request->params(), $put);

	return array('controller' => 'calendrierMvs', 'action' => 'organisateurCreation', 'args' => $args);
});

respond('DELETE', '/organisateur/([id|_id:method])/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'organisateurDelete', 'args' => $request->params());
});

respond('POST', '/organisateur/([id|_id:method])/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'organisateurUpdate', 'args' => $request->params());
});




// MANIFESTATION ///////////////////////////////////////////////////////////////////////////////////////////////////////

respond('GET', '/manifestation/[id|_id:method]/[:id]/type/[:type]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'manifestationGet', 'args' => $request->params());
});

respond('PUT', '/manifestation', function($request, $response, $router){
	parse_str(file_get_contents("php://input"), $put);
	$args = array_merge($request->params(), $put);

	return array('controller' => 'calendrierMvs', 'action' => 'manifestationCreation', 'args' => $args);
});

respond('DELETE', '/manifestation/([id|_id:method])/[:id]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'manifestationDelete', 'args' => $request->params());
});

respond('POST', '/manifestation/([id|_id:method])/[:id]/type/[:type]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'manifestationUpdate', 'args' => $request->params());
});




// MANIFESTATION DATE ///////////////////////////////////////////////////////////////////////////////////////////////////

respond('PUT',    '/manifestation/_id/[:_id]/date', function($request, $response, $router){
	parse_str(file_get_contents("php://input"), $put);
	$args = array_merge($request->params(), $put);

	return array('controller' => 'calendrierMvs', 'action' => 'manifestationDateCreation', 'args' => $args);
});

respond('DELETE', '/manifestation/_id/[:_id]/date/[:timestamp]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'manifestationDateDelete', 'args' => $request->params());
});

respond('POST',   '/manifestation/_id/[:_id]/date/[:timestamp]', function($request, $response, $router){
	return array('controller' => 'calendrierMvs', 'action' => 'manifestationDateUpdate', 'args' => $request->params());
});


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$route = dispatch(substr($_SERVER['REQUEST_URI'], strlen('/mvs')));
