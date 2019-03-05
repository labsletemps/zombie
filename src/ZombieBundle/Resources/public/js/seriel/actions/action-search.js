
$.widget('seriel.saveSearch', $.seriel.modal_navigator, {
	_create : function() {
		this._super();
		
		this.options.form = $('form', this.element);
		this.options.form.bind('submit', $.proxy(this.submit, this));
		
		$('.metier_widget', this.element).multiChoiceWidget();
		$('.activite_widget', this.element).multiChoiceWidget();
		$('.avancement_widget', this.element).multiChoiceWidget();
		$('.statut_widget', this.element).multiChoiceWidget();
		$('.categorie_widget', this.element).multiChoiceWidget();
		$('.delai_widget', this.element).multiChoiceWidget();
		$('.provenance_widget', this.element).multiChoiceWidget();
		$('.gestionnaire_widget', this.element).multiChoiceWidget();
		$('.type_entite_widget', this.element).multiChoiceWidget();
		$('.type_materiel_widget', this.element).multiChoiceWidget();
		$('.structure_widget', this.element).multiChoiceWidget();
		$('.region_widget', this.element).multiChoiceWidget();
		$('.type_division_widget', this.element).multiChoiceWidget();
		$('.section_widget', this.element).multiChoiceWidget();
		$('.trend_widget', this.element).multiChoiceWidget();
		$('.orderby_widget', this.element).multiChoiceWidget();
		$('.min_max_widget', this.element).minmaxWidget();
		
		$('.widget:not(.widget_initialized)', this.element).ser_widget({ /*	 */});
		
		this.options.search_str = this.getSearchStr();
		
		//  build serhash with string.
		var serhash = parseHash('save_search['+this.options.search_str+']');
		
		var params = serhash[0].getParams();
		
		var container = $('.save_search_widgets_container', this.element);
		loadFieldsSearchFromParams(params, container);
		
		// bind click on old elements.
		$('.list_recherches > ul > li', this.element).bind('click', $.proxy(this.otherSearchClicked, this));
		
		$('#form_nom', this.element).focus().select();
		
	},
	initAttachableFilters: function() {
		var ul = $('.save_search_widgets_editable_container > .cnt > ul', this.element);
		var attachables = $('.save_search_widgets_container .inp.attachable', this.element);
		
		var uid = time()+'_'+rand(10000, 99999);
		
		for (var i = 0; i < attachables.size(); i++) {
			var attachable = $(attachables.get(i));
			var name = attachable.attr('attachable-name');
			
			//  get élement.
			var widget = $('.widget', attachable);
			var varName = null;
			if (widget.size() == 1) {
				var varName = widget.attr('name');
			} else {
				var inp = $('input', attachable);
				if (inp.size() == 1) {
					var varName = inp.attr('name');
				}
			}
			
			var var_id = varName+'_'+uid;
			
			var li = $('<li name="'+varName+'"><input id="'+var_id+'" type="checkbox" name="'+varName+'" /><label for="'+var_id+'"> '+name+'</label></li>');
			li.appendTo(ul);
		}
	},
	getAttachableFiltersStr: function() {
		
		// get checkbox checked
		var ul = $('.save_search_widgets_editable_container > .cnt > ul', this.element);
		var checked= $(':checked', ul);
		
		if (checked) {
			var values = [];
			
			for (var i = 0; i < checked.size(); i++) {
				var checkbox = $(checked.get(i));
				var name = checkbox.attr('name');
				
				values.push(name);
			}
			
			return implode(',', values);
		}
		
		return '';
	},
	otherSearchClicked: function(event) {
		var target = $(event.currentTarget);
		var text = target.html();
		
		$('#form_nom', this.element).val(text);
	},
	koClicked : function() {
		this.element.dialog('close');
	},
	okClicked : function() {
		$('button[type=submit], input[type=submit]', this.element).trigger('click');
		return false;
	},
	submit: function(event) {
		event.stopPropagation();
		
		//get search string
		var checked = $('.save_search_cols_container input:checked', this.element);
		var mode_colonnes = checked.val();
		var colonnes_container = $('.save_search_cols_container .colonnes', this.element);
		var colonnes = colonnes_container.size() ? colonnes_container.html() : null;
		var editables = this.getAttachableFiltersStr();
		
		search_str = this.buildSearchStr();

		this.setLoading();
		
		var datas = getFormattedFormValues(this.options.form);
		
		datas['search'] = search_str;
		datas['mode_colonnes'] = mode_colonnes;
		if (colonnes && mode_colonnes == 1) datas['colonnes'] = colonnes;
		
		datas['editables'] = editables;
		
		$.post(getUrlPrefix() + '/recherche/save/'+ this.getType(), datas, $.proxy(this.saved, this));
		
		return false;
	},
	saved: function(result) {
		// TODO : contrôle le résultat.
		this.close();
	},
	getType: function() {
		return $('.type', this.element).html();
	},
	getSearchStr: function() {
		return $('.search', this.element).html();
	},
	buildSearchStr: function() {
		var container = $('.save_search_widgets_container', this.element);
		
		return buildSearchStr(container);
	},
	options: {
		defaultLayout : false,
		form: null,
		search_str: null
	}
});

$.widget('seriel.loadSearch', $.seriel.modal_navigator, {
	_create : function() {
		this._super();
		
		$('.menu_left > ul > li', this.element).bind('click', $.proxy(this.menuElemClicked, this));
		
		$('.content li[rs_id]', this.element).bind('click', $.proxy(this.rsClicked, this));
		$('.content li[rs_id]', this.element).bind('dblclick', $.proxy(this.okClicked, this));
	},
	menuElemClicked: function(event) {
		var target = $(event.currentTarget);
		var type = target.attr('type');
		$('.menu_left > ul > li.selected', this.element).removeClass('selected');
		target.addClass('selected');
		
		$('.cnt', this.element).removeClass('visible');
		$('.cnt[type='+type+']').addClass('visible');
	},
	rsClicked: function(event) {
		var target = $(event.currentTarget);
		$('.content li[rs_id].selected', this.element).removeClass('selected');
		target.addClass('selected');
	},
	koClicked: function() {
		this.close();
	},
	okClicked: function() {
		// Let's get the current selection.
		var elem = $('.content li[rs_id].selected', this.element);
		if (elem.size() == 0) {
			var title = "Aucune recherche sélectionnée";
			var content = "Vous devez sélectionner une recherche.";
			this.showAlert(title, content);
			return false;
		}
		
		var rs_id = elem.attr('rs_id');
		var type = elem.attr('rs_type');
		var search = $('.search', elem).html();
		
		var columns = null;
		var columns_container = $('.columns', elem);
		if (columns_container.size() == 1) columns = columns_container.html();
		
		this.getRechercheNavigator().loadSearchFromSauvegarde(rs_id, type, search, columns);
		this.close();
	},
	getRechercheNavigator: function() {
		return this.getParentNavigator();
	},
	options: {
		defaultLayout : false,
	}
});

$.widget('seriel.configSearch', $.seriel.modal_navigator, {
	_create : function() {
		this._super();
		$('.menu_left > ul > li', this.element).bind('click', $.proxy(this.menuElemClicked, this));
		$('.perso .delete',this.element).bind('click',$.proxy(this.delete_item,this));
		$('.perso .share',this.element).bind('click',$.proxy(this.share_rs,this));
		
		$('.shared .delete',this.element).bind('click',$.proxy(this.delete_shared_item,this));
	},
	menuElemClicked: function(event) {
		var target = $(event.currentTarget);
		var type = target.attr('type');
		$('.menu_left > ul > li.selected', this.element).removeClass('selected');
		target.addClass('selected');
		
		$('.cnt', this.element).removeClass('visible');
		$('.cnt[type='+type+']').addClass('visible');
	},
	share_rs: function(event){
		var target = $(event.currentTarget);
		
		var li = target.closest('li');
		var id = li.attr('rs_id');
		var nom = $('.nom', li).html();
		
		this.openModal("Partager la recherche : "+nom, getUrlPrefix() + '/recherche/share/'+id, { 'width': 850, 'height': 420 });
	},
	delete_item:function(event){
		var target = $(event.currentTarget);
		var res_id = target.attr('res_id');
		this.options.res_id = res_id;
		
		var li = target.closest('li');
		var nom = $('.nom', li).html();
		
		this.showConfirm('Confirmation de suppression', 'Etes vous sûr de vouloir supprimer définitivement la recherche "'+nom+'" ?', $.proxy(this.confirmDeleteRech, this), 'Annuler', 'Supprimer');
		
	},
	confirmDeleteRech: function(confirm) {
		if (confirm) {
			this.lancerDelete();
		}
	},
	delete_shared_item: function(event) {
		var target = $(event.currentTarget);
		var res_id = target.attr('res_id');
		this.options.res_id = res_id;
		
		var li = target.closest('li');
		var nom = $('.nom', li).html();
		
		this.showConfirm('Confirmation de suppression', 'Etes vous sûr de vouloir enlever la recherche fournie "'+nom+'" ?', $.proxy(this.confirmDeleteRechShared, this), 'Annuler', 'Supprimer');
	},
	confirmDeleteRechShared: function(confirm) {
		if (confirm) {
			this.lancerDeleteShared();
		}
	},
	lancerDelete: function(){
		var id = this.options.res_id;
		$.post(getUrlPrefix() + '/recherche/delete/'+id, $.proxy(this.deleted, this));

	},
	lancerDeleteShared: function(){
		var id = this.options.res_id;
		$.post(getUrlPrefix() + '/recherche/delete_shared/'+id, $.proxy(this.deleted, this));
	},
	deleted: function(result) {
		var res = $(result);
		var rs_id = $('.rs_id', res).html();
		
		$(".cont > ul > li[rs_id="+rs_id+"]",this.element).remove();
	},
	getRechercheNavigator: function() {
		return this.getParentNavigator();
	},
	options: {
		defaultLayout : false,
	}
});

$.widget('seriel.shareSearch', $.seriel.modal_navigator, {
	_create : function() {
		this._super();
		
		this.options.search_form = $('.search_form', this.element);
		this.options.search_form.bind('submit', $.proxy(this.search, this));
		
		this.options.typeEntiteSelect = $('.type_entite_select', this.element);
		this.options.typeEntiteSelect.bind('change', $.proxy(this.entiteSelectChanged, this));
		
		this.options.compteWidget = $('.select_compte_widget', this.element);
		this.options.compteWidget.multiChoiceWidget();
		
		$('.list_to li:not(.group) .remove', this.element).bind('click', $.proxy(this.remove, this));
	},
	search: function() {
		var type = this.options.typeEntiteSelect.val();
		var nom = $('input.nom', this.element).val();
		
		var datas = { 'nom': nom };
		
		if (type == 'cli') {
			var compte = this.options.compteWidget.multiChoiceWidget('getVal');
			datas['compte'] = compte;
		}
		
		if (type == 'cli' && (!compte) && (!nom)) {
			var title = "Recherche invalide";
			var content = "Vous devez sélectionner une recherche.";
			this.showAlert(title, content);
			return false
		}
		
		this.showSearchLoading();
		
		$.post(getUrlPrefix() + '/recherche/search_indiv/'+type, datas, $.proxy(this.searched, this));
	},
	searched: function(result) {
		this.hideSearchLoading();
		
		var res = $(result);
		if (res.hasClass('success')) {
			var current_individu_id = current_user_id();
			if (current_individu_id) {
				var current_individu_li = $('li[individu_id='+current_individu_id+']', res);
				if (current_individu_li.size() > 0) {
					current_individu_li.remove();
				}
			}
			
			var new_lis = $('li', res);
			for (var i = 0; i < new_lis.size(); i++) {
				var li = $(new_lis.get(i));
				var individu_id = li.attr('individu_id');
				// Search selected element in list.
				var indiv_to = $('.list_to li[individu_id='+individu_id+']', this.element);
				if (indiv_to.size() > 0) li.addClass('selected');
			}
			
			$('.list_from .cnt ul', this.element).html(res.html());
			$('.list_from .cnt ul > li .add', this.element).bind('click', $.proxy(this.add, this));
			$('.list_from .cnt ul > li .remove', this.element).bind('click', $.proxy(this.remove, this));
		}
	},
	add: function(event) {
		var target = $(event.target).closest('li');
		var individu_id = target.attr('individu_id');
		
		// Do we have individu in result ?
		var indiv_to = $('.list_to li[individu_id='+individu_id+']', this.element);
		if (indiv_to.size() > 0) return;
		
		var entite_id = target.attr('entite_id');
		// Do we have entite ?
		var entite_to = $('.list_to li.group[entite_id='+entite_id+']', this.element);
		if (entite_to.size() == 0) {
			// add entite.
			var entite_name = target.attr('entite_name');
			entite_to = $('<li class="group" entite_id="'+entite_id+'"><span>'+entite_name+'</span></li>');
			
			$('.list_to ul', this.element).append(entite_to);
		}
		
		// Add individu.
		indiv_to = target.clone();
		$('.add', indiv_to).remove();
		$('.remove', indiv_to).bind('click', $.proxy(this.remove, this));
		
		// insert individu.
		var lastElem = entite_to;
		var next = entite_to.next();
		while (next.size() == 1) {
			if (next.hasClass('group')) {
				break;
			}
			lastElem = next;
			next = next.next();
		}
				
		indiv_to.insertAfter(lastElem);
		target.addClass('selected');
		
	},
	remove: function(event) {
		var target = $(event.target).closest('li');
		var individu_id = target.attr('individu_id');
		
		var indiv_to = $('.list_to li[individu_id='+individu_id+']', this.element);
		if (indiv_to.size() > 0) {
			var entite_id = indiv_to.attr('entite_id');
			var entite_group = $('.list_to li.group[entite_id='+entite_id+']', this.element);
			var indiv_from = $('.list_from li[individu_id='+individu_id+']', this.element);
			
			indiv_to.remove();
			if (indiv_from.size() > 0) indiv_from.removeClass('selected');
			
			var individus_restants = $('.list_to li[entite_id='+entite_id+']:not(.group)', this.element);
			if (individus_restants.size() == 0) entite_group.remove();
		}
	},
	showSearchLoading: function() {
		var loader = $('.list_from .cnt > .loading', this.element);
		if (loader.size() == 0) {
			$('<div class="loading"></div>').appendTo($('.list_from .cnt', this.element));
		}
	},
	hideSearchLoading: function() {
		$('.list_from .cnt > .loading', this.element).remove();
	},
	entiteSelectChanged: function() {
		var val = this.options.typeEntiteSelect.val();
		if (val == 'soc') {
			$('.cli_select', this.element).addClass('hidden');
			$('.soc_select', this.element).removeClass('hidden');
		} else if (val == 'cli') {
			$('.soc_select', this.element).addClass('hidden');
			$('.cli_select', this.element).removeClass('hidden');
		}
	},
	koClicked: function() {
		this.close();
	},
	okClicked: function() {
		this.showLoading();
		
		// OK, get all individu
		var individus_lis = $('.list_to li:not(.group)', this.element);
		var datas = { 'submit': true };
		
		for (var i = 0; i < individus_lis.size(); i++) {
			var individu_li = $(individus_lis.get(i));
			var individu_id = individu_li.attr('individu_id');
			datas['individus['+i+']'] = individu_id;
		}
		
		$.post(getUrlPrefix() + '/recherche/share/'+this.getRechercheId(), datas, $.proxy(this.saved, this));
	},
	saved: function(result) {
		this.close();
	},
	getRechercheId: function() {
		return $('.rs_id', this.element).html();
	},
	options: {
		search_form: null,
		compteWidget: null,
		defaultLayout : false,
	}
});

