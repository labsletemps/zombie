
function openArticle(target, event, backward) {
	var article_id = target.attr('uid');
	var hash = 'article[' + article_id+ ']';
	
	if (!backward) hc().setHash(hash);
    else dockNav().openBackward(hash);
}

function initSsdDock() {
	var dn = dockNav();
	
	dockNav().addElemDockable('article');
}

function initAEListActions() {
	initLists();
	
	$('body').listActions('addActionForType', 'ZombieBundle\\Entity\\News\\Article', openArticle);
}

function openBackwardListElem(elem) {
	if (!elem) return;
	var type = elem.attr('type')
	if (!type) return;
	
	if (type == 'ZombieBundle\\Entity\\News\\Article') openArticle(elem, null, true);
}

function getAccueilNav() {
	var accueil_div = $('.accueil_div');
	
	if (accueil_div.size() == 1) {
		var par = accueil_div.parent();
		if (par.size() == 1 && par.hasClass('seriel_navigator')) {
			var navClass = par.attr('navigator');
			if (navClass && navClass == 'serielAccueilNav') {
				var navObj = par.data(navClass);
				return navObj;
			}
		}
	}
	
	return null;
}

function getRechercheNav() {
	var menu_rech = $('.menu_rech');
	
	if (menu_rech.size() == 1) {
		var par = menu_rech.parent();
		if (par.size() == 1 && par.hasClass('seriel_navigator')) {
			var navClass = par.attr('navigator');
			if (navClass && navClass == 'serielRechercheNav') {
				var navObj = par.data(navClass);
				return navObj;
			}
		}
	}
	
	return null;
}

function current_user_id() {
	var current_user = $('#header > .current_user');
	if (current_user.size() == 1) {
		var individu_id = current_user.attr('individu_id');
		if (individu_id) return individu_id;
	}
	
	return null;
}