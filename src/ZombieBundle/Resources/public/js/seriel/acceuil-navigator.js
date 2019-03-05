

var accueilNav = null;

$.widget('seriel.accueilNav', $.seriel.navigator, {
    _create: function () {
        this._super();
        accueilNav = this;
        
        this.options.actionMenu = $('.actions', this.element);
		this.options.actionMenu.actionMenu();
		
		$(' > .accueil_div', this.element).serielLayout({
			defaults: {
				spacing_open: 0,
				spacing_closed: 0
			}
		});
		
		var accueilHead = $('.accueil_head', this.element);
		var accueilHeadHeight = accueilHead.attr('layout-size');
		
		var panelLeft = $('.left_block', this.element);
		var panelLeftWidth = panelLeft.attr('layout-size');
		
		var panelRight = $('.right_block', this.element);
		var panelRightWidth = 0;
		if (panelRight.size() == 1) {
			var panelRightWidth = panelRight.attr('layout-size');
		}
		
		$('> .accueil_div .accueil_div_listes', this.element).serielLayout({
			defaults: {
				spacing_open: 0,
				spacing_closed: 0
			}
		});
		
		panelLeft.perfectScrollbar();
		panelLeft.css('overflow', 'hidden');
		
		panelRight.perfectScrollbar();
		panelRight.css('overflow', 'hidden');
		
		var lists = $('.accueil_list_block', this.element);
		this.initLists(lists);
		
		var reports = $('.accueil_report', this.element);
		this.initReports(reports);
		
		this.options.shortcutListsElems = $('.left_block > ul > li, .right_block > ul > li', this.element);
		this.options.shortcutListsElems.bind('click', $.proxy(this.shortListClicked, this));
		
		this.options.actionMenu.css('z-index', 100);
    },
    backInDom: function() {
		// Must be overwritted.
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
    initLists: function(lists) {
    	var l = $('.list_content', lists);
    	l.ser_list();
		l.bind('refreshed', $.proxy(this.listRefreshed, this));
		l.ser_list('setBuildContextMenuCallback', $.proxy(this.buildContextMenu, this));
		
		lists.accueil_list();
    },
    initReports: function(reports) {
    	reports.accueilReports();
    },
    buildContextMenu: function(trigger, event) {
    	var target = $(event.currentTarget);
    	
    	// get list.
    	var list = target.closest('.ser_list');
    	
    	var elems = list.ser_list('getElemsSelected');
    	var numElems = elems.size();
    	
    	var items = {
           	"open": {name: "Ouvrir"+(numElems > 1 ? ' ('+numElems+' &eacute;l&eacute;ments)' : ''), icon: "open"}
        };
    	// if all element may be validate, It is proportioned to the user.
    	var canValid = true;
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
            'callback': $.proxy(this.contextMenuAction, this), 
            'items': items 
        };
	},
	contextMenuAction: function(key, options) {
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
			
			$.post(getUrlPrefix() + '/ticket/group_accept', datas, $.proxy(this.disAccepted, this));
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
			
			$.post(getUrlPrefix() + '/ticket/group_refus', datas, $.proxy(this.disRefused, this));
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
			
			$.post(getUrlPrefix() + '/ticket/group_cloture', datas, $.proxy(this.disClotured, this));
		}
	},
	disAccepted: function(result) {
		var res = $(result);
		var list_id = $('.list_id', res).html();
		var list = $('#'+list_id);
		if (list.size() == 1) {
			var ids_spans = $('.elem_id', res);
			for (var i = 0; i < ids_spans.size(); i++) {
				var id_span = $(ids_spans.get(i));
				var id = id_span.html();
				list.ser_list('removeLine', id, true);
			}
		}
	},
	disRefused: function(result) {
		var res = $(result);
		var list_id = $('.list_id', res).html();
		var list = $('#'+list_id);
		if (list.size() == 1) {
			var ids_spans = $('.elem_id', res);
			for (var i = 0; i < ids_spans.size(); i++) {
				var id_span = $(ids_spans.get(i));
				var id = id_span.html();
				list.ser_list('removeLine', id, true);
			}
		}
	},
	disClotured: function(result) {
		var res = $(result);
		var list_id = $('.list_id', res).html();
		var list = $('#'+list_id);
		if (list.size() == 1) {
			var ids_spans = $('.elem_id', res);
			for (var i = 0; i < ids_spans.size(); i++) {
				var id_span = $(ids_spans.get(i));
				var id = id_span.html();
				list.ser_list('removeLine', id, true);
			}
		}
	},
	elemsRefreshed: function(result) {
		var res = $(result);
		var groups = $('> .group', res);
		for (var i = 0; i < groups.size(); i++) {
			var group = $(groups.get(i));
			var uid = group.attr('uid');
			
			// get list.
			var list = $('#'+uid, this.element);
			list.ser_list('elemsRefreshed', group);
		}
	},
    shortListClicked: function(event) {
    	var target = $(event.currentTarget);
    	
    	if (target.hasClass('report')) {
    		var reportToShow = target.attr('report');
    		this.showReport(reportToShow);
    	} else {
    		var listToShow = target.attr('list');
        	this.showList(listToShow);
    	}
    },
    forceRefreshCurrentList: function() {
    	try {
    		var currentDiv = $('.accueil_list > div.visible', this.element);
        	var currentList = $(' > .ser_list', currentDiv);
        	
        	if (currentList.size() == 1) {
        		currentList.ser_list('refresh');
        	}
    	} catch (e) {
    	}
    },
    showList: function(listName) {
    	this.options.shortcutListsElems.filter('.selected').removeClass('selected');
    	this.options.shortcutListsElems.filter('[list='+listName+']').addClass('selected');
    	
    	$('.accueil_list > div', this.element).removeClass('visible');
    	var newVisible = $('.accueil_list > div.accueil_list_'+listName, this.element);
    	var list = $('.ser_list', newVisible);
    	newVisible.accueil_list('loadIfNeverLoaded');
    	newVisible.addClass('visible');
    	
    },
    showReport: function(reportName) {
    	this.options.shortcutListsElems.filter('.selected').removeClass('selected');
    	this.options.shortcutListsElems.filter('[report='+reportName+']').addClass('selected');
    	
    	$('.accueil_list > div', this.element).removeClass('visible');
    	var newVisible = $('.accueil_list > div.accueil_list_'+reportName, this.element);
    	newVisible.addClass('visible');
    	
    	newVisible.accueilReports('runIfNeverRunned');
    	
    },
    getListNameFromContainerClass: function(className) {
    	var classes = explode(' ', className);
    	var listName = null;
    	
    	for (var i = 0; i < count(classes); i++) {
    		var className = classes[i];
    		if (strpos(className, 'accueil_list_') === 0) {
    			listName = substr(className, 13);
    		}
    	}
    	
    	return listName;
    }, 
    listRefreshed: function(event) {
    	var list = $(event.currentTarget);
    	var parent = list.parent();
    	
    	var listName = this.getListNameFromContainerClass(parent.attr('class'));
    	var qte = list.ser_list('getNumElems');
    	
    	this.updateQteList(listName, qte);
    },
    loadListContent: function(listName) {
    	var div = $('.accueil_list > div.accueil_list_'+listName, this.element);
    	
    	var loader = $('.loading', div);
    	if (loader.size() > 0) return;
    	
    	div.html('<div class="loading"></div>');
    	
    	$.post(getUrlPrefix() + '/accueil/ajax_list/' + listName, $.proxy(this.listLoaded, this));
    },
    listLoaded: function(result) {
    	var res = $(result);
    	
    	var listName = res.attr('list_name');
    	var div = $('.accueil_list > div.accueil_list_'+listName, this.element);
    	
    	if (res.hasClass('success')) {
    		div.html(res.html());
    		
    		var list = $('.list_content', div);
    		this.initLists(list);
    		
    		var qte = res.attr('qte');
    		
    		console.log('test 1 updateQteList : '+qte);
    		this.updateQteList(listName, qte);
    		
    		return;
    	} else {
    		// TODO : error
    		div.html('');
    	}
    },
    updateQteList: function(listName, qte) {
    	$('.left_block ul li[list='+listName+'] .qte, .right_block ul li[list='+listName+'] .qte', this.element).html(qte);
    },
    dealWithKeyPressed : function(event) {
		var res = this.options.actionMenu.actionMenu('keyPressed', event);
		return res;
	},
	onAction : function(action, title) {
		// Let's deal with this.
		console.log("ACCUEIL ACTION[" + action + "]");

		if (action == 'new_di') {
			this.newDI();
			return;
		}
		if (action == 'new_dd') {
			this.newDD();
			return;
		}
		if (action == 'new_presta') {
			this.newPresta();
			return;
		}
	},
	prestaAddResult: function(elem) {
		var prestataire_id = elem.attr('prestataire');
		hc().setHash('prestataire['+prestataire_id+']');
	},
	newDI: function() {
		this.openModal('Nouvelle demande d\'intervention', getUrlPrefix() + '/accueil/creaDI');
	},
	newDD: function() {
		this.openModal('Nouvelle demande de devis', getUrlPrefix() + '/accueil/creaDD');
	},
	newPresta: function() {
		this.openModal('Nouveau prestataire', getUrlPrefix() + '/accueil/creaPrestataire');
	},
    options: {
    	actionMenu: null,
    	shortcutListsElems: null
    }
});

$.widget('seriel.accueil_list', {
	_create: function() {
		this.options.list = $('.list_content', this.element);
		this.options.list.bind('refreshed', $.proxy(this.listRefreshed, this));
		
		if (this.element.hasClass('visible')) this.loadIfNeverLoaded();
	},
	loadIfNeverLoaded: function() {
		parent = this.element.parent();
		if (!this.options.first_loaded) {
			this.options.list.ser_list('refresh');
			this.options.sent_first_loading = true;
		}
	},
	listRefreshed: function() {
		if (this.options.sent_first_loading) this.options.first_loaded = true;
	},
	options: {
		list: null,
		
		sent_first_loading: false,
		first_loaded: false
	}
});