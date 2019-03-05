
$.widget('seriel.rechercheNav', $.seriel.navigator, {
	_create : function() {
		this._super();
		
		$('.line.inp', this.element).bind('mousedown', $.proxy(this.lineMouseDown, this));

		$('.region_widget', this.element).multiChoiceWidget();
		$('.type_division_widget', this.element).multiChoiceWidget();
		$('.select_fournisseur_widget', this.element).multiChoiceWidget();
		$('.marque_vehicule_widget', this.element).multiChoiceWidget();
		$('.modele_vehicule_widget', this.element).multiChoiceWidget();
		$('.statut_widget', this.element).multiChoiceWidget();
		$('.services_widget', this.element).multiChoiceWidget();
		$('.section_widget', this.element).multiChoiceWidget();
		$('.trend_widget', this.element).multiChoiceWidget();
		$('.orderby_widget', this.element).multiChoiceWidget();
		$('.min_max_widget', this.element).minmaxWidget();

		$('.select_fournisseur_widget', this.element).bind('change', $.proxy(this.fournisseurIdChanged, this));
		
		$('.empty_button', this.element).bind('click', $.proxy(this.emptySearch, this));

		var westBlock = $(' > .menu_left', this.element);
		var westWidth = westBlock.attr('layout-size');
		if (!westWidth)
			westWidth = westBlock.width();

		var eastBlock = $(' > .menu_right', this.element);
		var eastWidth = eastBlock.attr('layout-size');
		if (!eastWidth)
			eastWidth = eastBlock.width();

		this.element.serielLayout({
			defaults : {
				spacing_open : 0,
				spacing_closed : 0
			},
			west : {
				size : westWidth ? westWidth : 'auto'
			},
			east : {
				size : eastWidth ? eastWidth : 'auto'
			}
		});

		
		this.options.menu = $('#search_accordion', this.element);
		this.options.menu.menuMulti();
		
		initUncheckableRadio(this.element);

		this.options.destDiv = $(' > .cont > .search_list_container', this.element);

		$('.search_button', this.element).bind('click', $.proxy(this.runSearch, this));
		
		$('.save_button', this.element).bind('click', $.proxy(this.saveSearch, this));
		
		$('.widget:not(.widget_initialized)', this.element).ser_widget({});
		
		$('.widget.widget_initialized', this.element).bind('submit', $.proxy(this.serWidgetSubmit, this));
		
		$('.action', this.element).bind('click', $.proxy(this.actionClicked, this));
		this.initResults();

		// Initialize parrams buttons of the search.
		$('.context_menu_widget', this.element).contextMenuWidget();
		$('.context_menu_widget', this.element).bind('action', $.proxy(this.contextMenuAction, this));
		this.firstLoadCheck();
	},
	serWidgetSubmit: function(event) {
		var widget = $(event.currentTarget);
		var container = widget.closest('.scrollable');
		$('.search_button', container).trigger('click');
	},
	fournisseurIdChanged: function(event) {
		var target = $(event.currentTarget);
		var value = target.multiChoiceWidget('getVal');
		
		var container = target.closest('.content');
		
		if (value) {
			$('.line.inp:not(.line_prestataire_select)', container).addClass('disabled');
			
			var inputs = $('input', container);
			inputs.attr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_prestataire_select) > .inp_container > .ser_multichoice_widget', container);
			multiChoiceWidgets.multiChoiceWidget('setDisable', true);
			multiChoiceWidgets.trigger('change');
		} else {
			$('.line.inp', container).removeClass('disabled');
			
			var inputs = $('input', container);
			inputs.removeAttr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_prestataire_select) > .inp_container > .ser_multichoice_widget', container);
			
			multiChoiceWidgets.multiChoiceWidget('setDisable', false);
			multiChoiceWidgets.trigger('change');
		}
	},
	lineMouseDown: function(event) {
		var target = $(event.currentTarget);
		if (target.hasClass('disabled')) {
			event.stopPropagation();
			return false;
		}
		return true;
	},
	load : function(dest) {
		this.loadContent('./' + dest);
		return true;
	},
	contentLoaded : function(res) {
	},
	getDestDiv : function() {
		return this.options.destDiv;
	},
	contextMenuAction: function(event, action) {
		var target = $(event.currentTarget);
		var type = target.attr('rel');
		
		if (action == 'save') {
			this.saveSearch(type);
		} else if (action == 'load') {
			this.loadSearch(type);
		} else if (action == 'config') {
			this.configSearch(type);
		}
	},
	transformObjectToType: function(object) {
		var index = strrpos(object, '\\');
		if (index) object = substr(object, index+1);
		
		object = strtoupper(object);
		
		if (object == 'ARTICLE') return 'article';
		if (object == 'CONNEXION') return 'connexion';
		
		return null;
	},
	saveSearch: function(type) {
		var search_str = this.buildSearchStr(type);
		var type_str = type;
		
		// get columns.
		var list = $('.list_content', this.element);
		var list_type = list.ser_list('getType');
		
		colonnes_str = null;
		var obj_type = this.transformObjectToType(list_type);
		if (this.transformObjectToType(list_type) == type) {
			// get columns.
			colonnes = list.ser_list('getColumns');
			colonnes_str = implode(',', colonnes);
		}
		
		var datas = { 'search': search_str };
		if (colonnes_str) datas['colonnes'] = colonnes_str;
		
		var options = { 'post': datas, 'height': 600 };
		
		this.openModal('Enregistrer une recherche '+type_str, getUrlPrefix()+'/recherche/save/'+type, options);
	},
	loadSearch: function(type) {
		this.openModal('Charger une recherche', getUrlPrefix()+'/recherche/load/'+type);
	},
	loadSearchFromSauvegarde: function(rs_id, type, search, columns) {
		search_str = search;
		if (search_str) search_str += ',';
		search_str += 't='+type;
		
		if (columns) {
			str_cols = str_replace(',', ';', columns);
			search_str += ',cols='+str_cols;
		}
		
		hc().setHash('rechercher[' + search_str + ']');
	},
	configSearch: function(type) {
		this.openModal('Gérer mes recherches', getUrlPrefix()+'/recherche/config/'+type);
	},

	loadFieldsSearchFromParams: function(params) {
		var type = null;
		type = params['t'];
		if (params) {
		}
		var container = $('.search_button[rel='+type+']', this.element).parent();
		loadFieldsSearchFromParams(params, container);
	},
	emptySearch: function(event) {
		var target = $(event.currentTarget);
		var container = target.parent();
		
		loadFieldsSearchFromParams({}, container);
	},
	buildSearchStr: function(search_type) {
		var container = $('.search_button[rel='+search_type+']', this.element).parent();
		return buildSearchStr(container);
	},
	runSearch : function(event) {

		var target = $(event.currentTarget);
		var search_type = target.attr("rel"); 			
	
		var search_str = this.buildSearchStr(search_type);
		if (search_str == '') {

		}

		if (search_str != "")
			search_str += ","
		search_str += "t=" + search_type;
		var origHash = getCleanHash();
		var newHash = 'rechercher[' + search_str + ']';
		
		if (newHash == origHash) {
			
			this.checkContent(newHash, newHash, null, true);
			
			return;
		}
		hc().setHash(newHash);
	},
	firstLoadCheck: function() {
		var hash = getCleanHash();
		if (hash == 'rechercher') {
			var now = new Date();
			var datedujour  = now.getFullYear() +'-'+ (('0'+(now.getMonth()+1)).slice(-2))+'-'+('0'+now.getDate()   ).slice(-2);
		
			hash = 'rechercher[date_parution=' +  datedujour + ',t=article]';

		}
		var serhash = parseHash(hash);
		
		if (serhash && count(serhash) > 0) {
			var elem = serhash[0];
			var label = elem.getLabel();
			if (label != 'rechercher') return;
			
			var params = elem.getParams();
			if (count(params) > 0) {
				
				this.checkContent(hash, hash, serhash);
			}
		}
		
	},
	checkContent : function(url, key, serhash, force) {
		var hash = getCleanHash();
		
		if (this.options.lastCheckedHash && this.options.lastCheckedHash == hash && (!force)) return;

		$('ul#main_menu > li.rechercher > a').attr('href', '#' + hash);
		
		if (!serhash) serhash = parseHash(hash);

		if (serhash && count(serhash) > 0) {
			var elem = serhash[0];
			var params = elem.getParams();
			
			this.loadFieldsSearchFromParams(params);
			
			if (count(params) > 0) {
				var type = params['t'];
				
				this.showMenuForType(type);
				
				if (type && type == 'article') {
					delete params['t'];
					this.ajaxSearchArticle(params);
				} else if (type && type == 'connexion') {
					delete params['t'];
					this.ajaxSearchConnexion(params);
				} else {
				}
			}
		}
		
		this.options.lastCheckedHash = hash;
	},
	showMenuForType: function(type) {
		// click on H3.
		this.options.menu.menuMulti('setSelected', type);
	},
	clearCurrentList: function() {
		var list = $('.ser_list', this.element);
		if (list.size() > 0) {
			list.ser_list('selfDestruct');
		}
	},
	ajaxSearchArticle: function(params) {
		this.clearCurrentList();
		this.options.destDiv.html("");
		this.options.destDiv.addClass('loading');
		this.options.destDiv.load(getUrlPrefix() + "/recherche/ajax_search_article", params, $.proxy(this.gotResult, this));
	},
	ajaxSearchConnexion: function(params) {
		this.clearCurrentList();
		this.options.destDiv.html("");
		this.options.destDiv.addClass('loading');
		this.options.destDiv.load(getUrlPrefix() + "/recherche/ajax_search_connexion", params, $.proxy(this.gotResult, this));
	},
	gotResult : function(response, status, xhr) {
		// Do we have an error ?
		var inError = false;
		if (status == "error") {
			var code = xhr.status;
			var res = getBodyContent(response);
			this.options.destDiv.html('<div class="error_container"><div>'
					+ res + '</div></div>');

			inError = true;
		}

		this.options.destDiv.removeClass('loading');
		if (inError) {
			this.options.destDiv.errorNav();
			return;
		}

		this.initResults();
	},
	forceRefreshTicketsList: function() {
		try {
			var list = $('.list_content', this.element);
			if (list.size() == 1) {
				var list_type = list.ser_list('getType');
				var index = strrpos(list_type, "\\");
				if (index !== false) list_type = substr(list_type, index+1);
			}	
		} catch (e) {
			
		}
	},
	actionClicked : function(event) {
		var target = $(event.currentTarget);
		this.onAction(target.attr("action"));
	},
	onAction : function(action, title) {
		// Let's deal with this.
		console.log("RECHERCHENAV_ACTION[" + action + "]");

		if (action == 'settings') {
			this.settings();
			return;
		}
	},
	settings : function() {

	},
	initResults : function() {
		var list = $('.list_content', this.element);
		list.ser_list();
		var list_type = list.ser_list('getType');
		var index = strrpos(list_type, "\\");
		if (index !== false) list_type = substr(list_type, index+1);
	},
	buildContextMenuList: function(trigger, event) {

    	var target = $(event.currentTarget);
    	
    	// get list
    	var list = target.closest('.ser_list');
    	
    	var elems = list.ser_list('getElemsSelected');
    	var numElems = elems.size();
    	
    	var items = {      	
            	"open": {name: "Ouvrir"+(numElems > 1 ? ' ('+numElems+' &eacute;l&eacute;ments)' : ''), icon: "open"}
        };
    	
    	// if all elements may be to validate, request user
    	var canValid = true;
    	var canRefus = true;
    	var canCloture = true;
    	
    	for (var i = 0; i < elems.size(); i++) {
    		var elem = $(elems.get(i));
    		if (!elem.attr('canValid')) canValid = false;
    		if (!elem.attr('canRefus')) canRefus = false;
    		if (!elem.attr('canCloture')) canCloture = false;
    		
    		if ((!canValid) && (!canRefus) && (!canCloture)) {
    			break;
    		}
    	}
    	
    	if (canValid) {
    		items["valid"] = {name: "Valider"+(numElems > 1 ? ' ('+numElems+' &eacute;l&eacute;ments)' : ''), icon: "valid"};
    	}
    	if (canRefus) {
    		items["refus"] = {name: "Refuser"+(numElems > 1 ? ' ('+numElems+' &eacute;l&eacute;ments)' : ''), icon: "refus"};
    	}
    	if (canCloture) {
    		items["cloture"] = {name: "Clôturer"+(numElems > 1 ? ' ('+numElems+' &eacute;l&eacute;ments)' : ''), icon: "cloture"};
    	}
    	
		return {
            'callback': $.proxy(this.contextMenuActionList, this), 
            'items': items 
        };
	},
	contextMenuActionList: function(key, options) {
		var context = $(options.context);
		var list = context.closest('.ser_list');

		var elems = list.ser_list('getElemsSelected');
		if (elems.size() == 0) return;
		
		var type = list.ser_list('getType');
		
		if (key == 'open') {
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
				
				openBackwardListElem(elem);
			}
		} else if (key == 'valid') {
			var ids = [];
			var datas = {};
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
				
				ids.push(elem.attr('uid'));
			}
			
			if (count(ids) == 0) return; // Should never happen
			
			var list_id = list.attr('id');
			datas['list_id'] = list_id;
			
			datas['type'] = type;
			datas['ids'] = ids;
			
			$.post(getUrlPrefix() + '/ticket/group_accept', datas, $.proxy(this.elemAccepted, this));
		} else if (key == 'refus') {
			var ids = [];
			var datas = {};
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
				
				ids.push(elem.attr('uid'));
			}
			
			if (count(ids) == 0) return; // Should never happen
			
			var list_id = list.attr('id');
			datas['list_id'] = list_id;
			
			datas['type'] = type;
			datas['ids'] = ids;
			
			$.post(getUrlPrefix() + '/ticket/group_refus', datas, $.proxy(this.elemRefused, this));
		} else if (key == 'cloture') {
			var ids = [];
			var datas = {};
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
				
				ids.push(elem.attr('uid'));
			}
			
			if (count(ids) == 0) return; // Should never happen
			
			var list_id = list.attr('id');
			datas['list_id'] = list_id;
			
			datas['type'] = type;
			datas['ids'] = ids;
			
			$.post(getUrlPrefix() + '/ticket/group_cloture', datas, $.proxy(this.elemClotured, this));
		}
	},
	elemAccepted: function(result) {
		
	},
	elemRefused: function(result) {
		
	},
	elemClotured: function(result) {
		
	},
	options : {
		destDiv : null,
		resList : null,
		
		menu: null,
		
		
		lastCheckedHash: null
	}
});