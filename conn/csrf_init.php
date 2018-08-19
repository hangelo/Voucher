<?php

/*
Cria  o token e declara o vetor de parâmetros.
*/

$csrf = new csrf();
$token_id = $csrf->get_token_id();
$token_id__md5 = md5( $token_id );
$token_value = $csrf->get_token( $token_id );

$vet_POST_params = array(
	'a0',
	'b0',
	'c0',
	'e0',
	'd0',
	'f0',
	'a1',
	'b1',
  'c1',
  'd1',
  'e1',
  'f1'
);

// carrega parâmetros sem renovar identificadores, pois foram renovados quando iniciou a sessão
$POST_params = $csrf->form_names( $vet_POST_params, false );
