$.widget('seriel.articleInfosNav', $.seriel.navigator, {
	_create : function() {
		this._super();
		
		$(' > .presta_info_content', this.element).serielLayout({
			defaults : {
				spacing_open : 0,
				spacing_closed : 0
			},
			north : {}
		});

		$('.presta_info_content_2', this.element).serielLayout({
			defaults : {
				spacing_open : 0,
				spacing_closed : 0
			},
			west : {
				size : 35
			}
		});
		
		var img = $('.presta_info_informations_infos_2 .block_left > img', this.element);
		if (img.size() == 1) {
			var imgsrc = img.attr('src');
			if (imgsrc) {
				img.parent().css('background-image', 'url('+imgsrc+')');
			}
		}
		
		this.options.articleId = $('.article_id', this.element).html();

		this.options.actionMenu = $('.actions', this.element);
		this.options.actionMenu.actionMenu();

		transformAncres($('.ancre', this.element));
		
		this.options.actionMenu.css('z-index', 100);
		
		$('.presta_liste_contacts_container > .list_content', this.element).ser_list();
		
		$('.presta_info_content_2', this.element).ancrage({
			'scrollblock' : $('.presta_info_content_3', this.element)
		});
		
		this.updateLinks();
		
		
		// Let's bind buttons.
		$('.action_buttons .cancel_button', this.element).bind('click', $.proxy(this.cancelModifClicked, this));
		$('.action_buttons .valid_button', this.element).bind('click', $.proxy(this.saveModifClicked, this));
	},
	updateLinks: function() {
		var links = $('.article_art a[href]', this.element);
		
		for (var i = 0; i < links.size(); i++) {
			var link = $(links.get(i));
			var href = link.attr('href');
			var orig_href = href;
			
			link.attr('target', '_blank');
			if (substr(href, 0, 2) == "\\\"" && substr(href, -2) == "\\\"") href = substr(href, 2, -2);
			
			if (orig_href != href) link.attr('href', href);
		}
	},
	backInDom: function() {
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
	afficherAgence: function(event){
		var target = $(event.currentTarget);
		var name = $('.nom',target).html();
		var id = target.attr('agence_id');
		this.options.name = name
		$.post(getUrlPrefix() + '/agence/infos/'+id, $.proxy(this.agenceCharged, this));
	},
	agenceCharged: function(result) {
		var res = $(result);
		res.css({
			'top':'30px',
			'left': '0px',
			'bottom':'0px',
			'right' : '0px'
		});
		this.openModalWithContent(this.options.name,res);
		
	},
	checkChange: function() {
		var changed = this.anythingChanged();
		this.setHasModif(changed);
	},
	anythingChanged: function() {
		return false;
	},
	cancelModifClicked: function() {
		// TODO
		this.checkChange();
	},
	saveModifClicked: function() {
		if (this.anythingChanged() == false) return;
		
		var ticketNav = this.getTicketNavigator();
   		var ticketNum = ticketNav.getTicketNum();
		
   		ticketNav.showLoading();
		
		var metier = this.options.metierWidget.multiChoiceWidget('getVal');
		var delay = this.options.delayWidget.multiChoiceWidget('getVal');
		
		var compteId = this.getCompteId();
		
		var datas = { 'di_id': compteId, 'metier': metier, 'delai': delay };
		
		var newRep = trim(this.options.nouvelleReponse.val());
		if (!editorTextEmpty(newRep)) {
			datas['new_reponse'] = newRep;
		}
   		
    	//  refresh all open element.
    	var openElements = ticketNav.getAllOpenElems();
    	for (var i = 0; i < count(openElements); i++) {
    		var elem = openElements[i];
    		datas['elems['+i+']'] = elem;
    	}
    	
    	$.post(getUrlPrefix()+'/ticket/editDI/'+di_id, datas, $.proxy(this.savedModif, this));
	},
	savedModif: function(result) {
		var res = $(result);
		
		var ticketNav = this.getTicketNavigator();
		ticketNav.hideLoading();
		
		if (!res.hasClass('nav_refresh_list')) {
			// We have an unknown problem
			// TODO
			
			ticketNav.hideLoading();
			return;
		}

		this.getTicketNavigator().updateElems(res);
	},
	dealWithKeyPressed : function(event) {
		var res = this.options.actionMenu.actionMenu('keyPressed', event);
		return res;
	},
	onAction : function(action, title) {
		// Let's deal with this.
		console.log("PRESTA_INFO_ACTION[" + action + "]");
		
		if (action == 'infos') {
			this.modifInfos();
		} else if (action == 'bon_essence') {
			this.bonEssence();
		} else if (action == 'bon_lavage') {
			this.bonLavage();
		} else if (action == 'revision') {
			this.revision();
		} else if (action == 'entretien') {
			this.entretien();
		} else if (action == 'sinistre') {
			this.sinistre();
		} else if (action == 'lier_doc') {
			this.lierDoc();
		} else if (action == 'traces') {
			this.traces();
		} else if (action == 'email') {
			// get mail.
			this.openEmailModel(0);
		} else if (action == 'imprimer'){
			this.print();
		}
	},
	openEmailModel: function(model_id, files_ids) {
		parentHeight = 600;
		var parent = this.getParentNavigator();
		if (parent) {
			parentHeight =  parent.element.height();
		}
		
		var datas = { 'objet_doi': 'Article-'+this.getArticleId(), 'model_id': model_id };
		if (files_ids) datas['attachments'] = files_ids;
		
		this.openModal('Envoyer un email', getUrlPrefix() + '/email/nouveau', { 'post': datas, 'height': parentHeight - 50 });
	},
	lierDoc: function() {
		var doi = 'Article-'+this.getArticleId();
		this.openModal('Lier un document', getUrlPrefix() + '/documents/lier/'+doi);
	},
	traces: function() {
		var doi = 'Article-'+this.getArticleId();
		this.openModal('Historique des actions', getUrlPrefix() + '/traces/'+doi, {'width': 1000, 'height': 550});
	},
	print: function() {
		var clone = this.element.clone();
		clone.css('height', 'auto');

		var what_elems = $('.di_historique .what', clone);
		
		for (var i = 0; i < what_elems.size(); i++) {
			var what = $(what_elems.get(i));
			var container = what.parent();	
			what.css('max-height', 'none');
		}
		$('.left_helper',clone).css('display','none');
		$('.actions',clone).css('display','none');
		
		$('.reponse',clone).css('display','none');

		
		
		$('.presta_info_content',clone).css('border-width','0px');
		
		$('.widget > span',clone).css({
			'border-width':'0px'
			
		});

		$('.show_histo',clone).css('display','none');
		clone.printArea();
		setTimeout($.proxy(this.hideLoading, this), 2000);
	},
	modifInfos: function() {
		this.openModal('Informations de l\'article', getUrlPrefix() + '/article/infos/edit/' + this.getArticleId());
	},
	openModal : function(title, url, options) {
		this.openModalInsideParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		console.log('PRESTA MODAL TEST : '+options+' > '+(options ? options['width'] : 'NULL'));
		this.openModalWithContentInsideParentNavigator(title, content, options);
	},
	getArticleId : function() {
		return this.options.articleId;
	},
	updateInfos : function(content) {
		console.log('update info : article-info-navigator');
		this.getParentNavigator().updateInfos(content);
	},
	updateDocs: function(block_docs) {
		$('.block_documents', this.element).replaceWith(block_docs);
	},
	options : {
		actionMenu : null,
		articleId : null
	}
});