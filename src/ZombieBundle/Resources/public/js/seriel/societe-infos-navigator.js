$.widget('seriel.societeInfosNav', $.seriel.navigator, {
	_create : function() {
		this._super();
		
		$(' > .compte_info_content', this.element).serielLayout({
			defaults : {
				spacing_open : 0,
				spacing_closed : 0
			},
			north : {}
		});

		$('.compte_info_content_2', this.element).serielLayout({
			defaults : {
				spacing_open : 0,
				spacing_closed : 0
			},
			west : {
				size : 35
			}
		});
		
		var img = $('.compte_info_informations_infos_2 .block_left > img', this.element);
		if (img.size() == 1) {
			var imgsrc = img.attr('src');
			if (imgsrc) {
				img.parent().css('background-image', 'url('+imgsrc+')');
			}
		}
		
		this.options.societeId = $('.societe_id', this.element).html();

		this.options.actionMenu = $('.actions', this.element);
		this.options.actionMenu.actionMenu();

		transformAncres($('.ancre', this.element));
		
		this.options.actionMenu.css('z-index', 100);

		$('.compte_info_content_2', this.element).ancrage({
			'scrollblock' : $('.compte_info_content_3', this.element)
		});
		
		$('.compte_info_horaires', this.element).bind('click', $.proxy(this.horaires, this));
		
		// Let's bind buttons.
		$('.action_buttons .cancel_button', this.element).bind('click', $.proxy(this.cancelModifClicked, this));
		$('.action_buttons .valid_button', this.element).bind('click', $.proxy(this.saveModifClicked, this));
	},
	backInDom: function() {
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
	dealWithKeyPressed : function(event) {
		var res = this.options.actionMenu.actionMenu('keyPressed', event);
		return res;
	},
	onAction : function(action, title) {
		// Let's deal with this.
		console.log("COMPTE_INFO_ACTION[" + action + "]");
		
		if (action == 'edit') {
			this.edit();
		} else if (action == 'horaires') {
			this.horaires();
		} else if (action == 'lier_doc') {
			this.lierDoc();
		} else if (action == 'signature_email') {
			this.signatureEmail();
		} else if (action == 'traces') {
			this.traces();
			return;
		} else if (action == 'imprimer'){
			this.print();
			return;
		} else if (action == 'changer_photo'){
			this.changerPhoto();
		}
	},
	signatureEmail: function() {
		this.openModal('Signature Email pour la société', getUrlPrefix() + '/societe/signature_email/'+this.getSocieteId());
	},
	changerPhoto: function(){
		this.openModal('Gestion des images', getUrlPrefix() + '/societe/photo/'+this.getSocieteId());
		return;
	},
	openEmailModel: function(model_id, files_ids) {
		
		parentHeight = 600;
		var parent = this.getParentNavigator();
		if (parent) {
			parentHeight =  parent.element.height();
		}
		
		var datas = { 'objet_doi': 'Societe-'+this.getSocieteId(), 'model_id': model_id };
		if (files_ids) datas['attachments'] = files_ids;
		
		this.openModal('Envoyer un email', getUrlPrefix() + '/email/nouveau', { 'post': datas, 'height': parentHeight - 50 });
	},
	print: function() {
		
		var clone = this.element.clone();
		clone.css('height', 'auto');

		
		var what_elems = $('.di_historique .what', clone);
		
		clone.css('-webkit-print-color-adjust', 'exact');

		$('.compte_info_informations_infos_2 .client_block .block_left',clone).css({
			'height':'50px',
			'min-height':'0px'
		});
		
		$('.compte_info_informations_infos_2 .client_block .block_right, .di_informations_client .client_block .block_right',clone).css('min-height','none');
		$('.compte_info_informations_infos_2 .client_block, .di_informations_client .client_block',clone).css('padding-top','3px');
		$('.list_elem',clone).css({
			'width':'330px'
		});
		
		$('.hierarchy_children_list_container',clone).remove();

		
		
		$('.selected',clone).removeClass('selected');
		
		$('.pager',clone).css('display','none');
		
		
		
		$('.list_head',clone).remove();
		$('.hierarchy_children_list_container .list_content',clone).css('top','-35px')
		$('.list_elems',clone).css({
			'overflow':'hidden',
			'display' : 'block'
		});
		
		$('.list_elems',clone).css('height','auto');
		
		$('.compte_info_informations_infos_1',clone).css('width','21%');
		$('.compte_info_informations_infos_2',clone).css({
			'float':'right',
			'width':'70%'
		});
		
		$('.nbSousEntite',clone).show();
		$('.nbSousEntite',clone).css('padding','10px');
		
		$('.compte_info_horaires',clone).css('margin','0px');
		
		$('.compte_info_memo',clone).css({
			'margin-top':'0px',
			'margin-left':'20px',
			'width' : '300px',
			'min-height': '80px'
		});
		
		for (var i = 0; i < what_elems.size(); i++) {
			var what = $(what_elems.get(i));
			var container = what.parent();	
			what.css('max-height', 'none');
		}
		$('.left_helper',clone).css('display','none');
		$('.actions',clone).css('display','none');
		
		$('.compte_info_content',clone).css('border-width','0px');
		
		$('.widget > span',clone).css({
			'border-width':'0px'
			
		});
		
		$('.thumb',clone).css({
			'height':'100px',
			'width': '78px'
			
		});
		
		$('.doc',clone).css('width','80px');
		
		clone.printArea();
		setTimeout($.proxy(this.hideLoading, this), 2000);
	},
	edit: function() {
		this.openModal('Edition des données de la société', getUrlPrefix() + '/societe/infos/edit/' + this.getSocieteId());
	},
	horaires: function() {
		this.openModal('Gestion des horaires de la société', getUrlPrefix() + '/societe/horaires/' + this.getSocieteId(), { 'width': 1010, 'height': 590 });
	},
	lierDoc: function() {
		var doi = 'Compte-'+this.getCompteId();
		this.openModal('Lier un document', getUrlPrefix() + '/documents/lier/'+doi);
	},
	traces: function() {
		var doi = 'Compte-'+this.getCompteId();
		this.openModal('Historique des actions', getUrlPrefix() + '/traces/'+doi, {'width': 1000, 'height': 550});
	},
	openModal : function(title, url, options) {
		this.openModalInsideParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideParentNavigator(title, content, options);
	},
	getSocieteId: function() {
		return this.options.societeId;
	},
	updateInfos : function(content) {
		this.getParentNavigator().updateSocieteInfos(content);
	},
	updateDocs: function(block_docs) {
		$('.block_documents', this.element).replaceWith(block_docs);
	},
	options : {
		actionMenu : null,
		societeId : null
	}
});