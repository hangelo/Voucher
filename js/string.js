/* alterações no objeto STRING */

String.prototype.trim = func_str_trim;
function func_str_trim () {
	return this.replace(/^\s+|\s+$/g,"");
}

String.prototype.ltrim = func_str_ltrim;
function func_str_ltrim () {
	return this.replace(/^\s+/,"");
}

String.prototype.rtrim = func_str_rtrim;
function func_str_rtrim () {
	return this.replace(/\s+$/,"");
}

String.prototype.replaceAll = func_str_replaceAll;
function func_str_replaceAll(de, para){
	var str = this;
	var pos = str.indexOf(de);
	while (pos > -1){
		str = str.replace(de, para);
		pos = str.indexOf(de);
	}
	return (str);
}
