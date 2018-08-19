<?PHP

/*
Usado para gerar as variáveis em javascript para o Front End
*/

$POST_init_param = $token_id.'='.$token_value;

$js_POST_params = '';
$js_POST_params .= '<script>';
$js_POST_params .= 'var token_id__md5 = \''.$token_id__md5.'\';';
$js_POST_params .= 'var POSTinitP = \''.$POST_init_param	.'\';';
foreach ( $vet_POST_params as $index => $val ) {
	$js_POST_params .= 'var jsP_'.$val.' = \'&'.$POST_params[ $val ].'='.'\';';
}
$js_POST_params .= 'var jsP = new Array(jsP_'.implode(', jsP_', $vet_POST_params).')';
$js_POST_params .= '</script>';

echo $js_POST_params;
