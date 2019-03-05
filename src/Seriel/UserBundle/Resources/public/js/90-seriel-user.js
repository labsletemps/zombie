
function getCleanHash() {
	var hash = document.location.hash;
	if (hash == null || hash == undefined || strlen(hash) == 0) return '';
	return substr(hash, 1);
}