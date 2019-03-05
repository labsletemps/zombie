

$.widget('seriel.saveReport', $.seriel.modal_navigator, {
	_create : function() {
		this._super();
		

		this.element.addClass('is_gestionnaire');
		
		this.options.form = $('form', this.element);
		this.options.form.bind('submit', $.proxy(this.submit, this));

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
		
		this.options.datas_selector = $('.select_datas_report_widget', this.element);
		this.options.datas_selector.multiChoiceWidget();
		
		this.options.ligne_select = $('.ligne_select', this.element);
		this.options.col_select = $('.col_select', this.element);
		
		this.options.ligne_options_block = $('.options_line', this.element);
		this.options.col_options_block = $('.options_col', this.element);
		
		this.options.ligne_select.bind('change', $.proxy(this.ligneChanged, this));
		this.options.col_select.bind('change', $.proxy(this.colChanged, this));
		
		$('.save_report_widgets_container .widget:not(.widget_initialized)', this.element).ser_widget();
		
		this.ligneChanged();
		this.colChanged();
		
		this.options.report_str = this.getReportStr();
		
		//  build serhash with string.
		var serhash = parseHash('save_report['+this.options.report_str+']');
		
		var params = serhash[0].getParams();
		
		this.loadFieldsFromParams(params);
		
		// bind click on old elements.
		$('.list_reportings > ul > li', this.element).bind('click', $.proxy(this.otherReportClicked, this));
		
		$('#form_nom', this.element).focus().select();
		
		this.initAttachableFilters();
	},
	initAttachableFilters: function() {
		var ul = $('.save_report_widgets_editable_container > .cnt > ul', this.element);
		var attachables = $('.save_report_widgets_container .inp.attachable', this.element);
		
		var uid = time()+'_'+rand(10000, 99999);
		
		for (var i = 0; i < attachables.size(); i++) {
			var attachable = $(attachables.get(i));
			var name = attachable.attr('attachable-name');
			
			// get element.
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
		var ul = $('.save_report_widgets_editable_container > .cnt > ul', this.element);
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
	loadFieldsSearchFromParams: function(params) {
		var container = $('.save_report_widgets_container', this.element);
		
		// Remove all params that not concerned search.
		var searchParams = {};
		for (var key in params) {
			if (key == 'row' || key == 'col' || key == 'datas') continue;
			searchParams[key] = params[key];
		}
		
		loadFieldsSearchFromParams(searchParams, container);
	},
	loadFieldsFromParams: function(params) {
		var row = params['row'];
		var col = params['col'];
		var datas = params['datas'];
		
		this.loadFieldsSearchFromParams(params);
		
		this._realLoadFieldsFromParams(row, col, datas);
	},
	_realLoadFieldsFromParams: function(row, col, datas) {
		var row_sub = null;
		var col_sub = null;
		
		if (row) {
			var index = strpos(row, '(');
			if (index) {
				row_sub = substr(row, index+1, strlen(row) - (index+1) - 1);
				row = substr(row, 0, index);
			}			
		}

		if (col) {
			var index = strpos(col, '(');
			if (index) {
				col_sub = substr(col, index+1, strlen(col) - (index+1) - 1);
				col = substr(col, 0, index);
			}			
		}
		
		this.options.ligne_select.val(row);
		this.options.ligne_select.trigger('change');
		this.options.col_select.val(col);
		this.options.col_select.trigger('change');
		
		if (row_sub) {
			this.setLigneOptVal(row_sub);
		}
		if (col_sub) {
			this.setColOptVal(col_sub);
		}
		
		this.options.datas_selector.multiChoiceWidget('setVal', datas);
	},
	otherReportClicked: function(event) {
		var target = $(event.currentTarget);
		var text = target.html();
		
		$('#form_nom', this.element).val(text);
	},
	ligneChanged: function() {
		$(' > .option_block.visible', this.options.ligne_options_block).removeClass('visible');
		
		var newLineOptionBlock = $(' > .option_block[code="'+this.options.ligne_select.val()+'"]', this.options.ligne_options_block);
		newLineOptionBlock.addClass('visible');
		
		// Do we have a widget to activate ?
		var widget_name = $('.render_option_widget', newLineOptionBlock).html();
		if (widget_name) {
			// it is initialize ?		
			if (!newLineOptionBlock.hasClass('render_option_widget')) {
				eval('newLineOptionBlock.' + widget_name + '();');
			}
		}
	},
	colChanged: function() {
		$(' > .option_block.visible', this.options.col_options_block).removeClass('visible');
		
		var newColOptionBlock = $(' > .option_block[code="'+this.options.col_select.val()+'"]', this.options.col_options_block);
		newColOptionBlock.addClass('visible');
		
		// Do we have a widget to activate ?
		var widget_name = $('.render_option_widget', newColOptionBlock).html();
		if (widget_name) {
			// it is initialize ?	
			if (!newColOptionBlock.hasClass('render_option_widget')) {
				eval('newColOptionBlock.' + widget_name + '();');
			}
		}
	},
	getLigneOptVal: function() {
		//get block option selectionned.
		var block = $(' > .option_block.visible', this.options.ligne_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.getVal();
			}
		}
		
		return null;
	},
	setLigneOptVal: function(val) {
		//get block option selectionned.
		var block = $(' > .option_block.visible', this.options.ligne_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.setVal(val);
			}
		}
		
	},
	getColOptVal: function() {
		//get block option selectionned.
		var block = $(' > .option_block.visible', this.options.col_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.getVal();
			}
		}
		
		return null;
	},
	setColOptVal: function(val) {
		//get block option selectionned.
		var block = $(' > .option_block.visible', this.options.col_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				widget.setVal(val);
			}
		}
	},
	koClicked : function() {
		this.element.dialog('close');
	},
	okClicked : function() {
		$('button[type=submit], input[type=submit]', this.element).trigger('click');
		return false;
	},
	buildReportStr: function() {
		var container = $('.save_report_widgets_container', this.element);
		var search_str = buildSearchStr(container);
		
		var ligne = this.options.ligne_select.val();
		var ligneOptVal = this.getLigneOptVal();
		
		var col = this.options.col_select.val();
		var colOptVal = this.getColOptVal();
		
		var datas = this.options.datas_selector.multiChoiceWidget('getVal');
		
		var report_str = search_str;
		if (report_str) report_str += ',';
		report_str += this.options.ligne_p_name+'='+ligne+(ligneOptVal?'('+ligneOptVal+')':'')+','+this.options.col_p_name+'='+col+(colOptVal?'('+colOptVal+')':'')+','+this.options.data_p_name+'='+datas;
		
		return report_str;
	},
	submit: function(event) {
		event.stopPropagation();
		
		var editables = this.getAttachableFiltersStr();
		
		// get search string.
		report_str = this.buildReportStr();

		this.setLoading();
		
		var datas = getFormattedFormValues(this.options.form);
		
		datas['report'] = report_str;
		
		datas['editables'] = editables;
		
		$.post(getUrlPrefix() + '/reporting/save/'+ this.getType(), datas, $.proxy(this.saved, this));
		
		return false;
	},
	saved: function(result) {
		// TODO : contrôle le résultat.
		this.close();
	},
	getType: function() {
		return $('.type', this.element).html();
	},
	getReportStr: function() {
		return $('.report', this.element).html();
	},
	buildSearchStr: function() {
		var container = $('.save_search_widgets_container', this.element);
		
		return buildSearchStr(container);
	},
	options: {
		defaultLayout : false,
		form: null,
		report_str: null,
		
		ligne_p_name: 'row',
		col_p_name: 'col',
		data_p_name: 'datas',
		
		ligne_options_block: null,
		col_options_block: null,
		
		datas_selector: null,
	}
});

$.widget('seriel.loadReport', $.seriel.modal_navigator, {
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
			var title = "Aucun reporting sélectionné";
			var content = "Vous devez sélectionner un reporting.";
			this.showAlert(title, content);
			return false
		}
		
		var rs_id = elem.attr('rs_id');
		var type = elem.attr('rs_type');
		var report = $('.report', elem).html();
		this.getReportingNavigator().loadReportingFromSauvegarde(rs_id, type, report);
		this.close();
	},
	getReportingNavigator: function() {
		return this.getParentNavigator();
	},
	options: {
		defaultLayout : false,
	}
});

$.widget('seriel.configReport', $.seriel.modal_navigator, {
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
		
		this.openModal("Partager le reporting : "+nom, getUrlPrefix() + '/reporting/share/'+id, { 'width': 850, 'height': 420 });
	},
	delete_item:function(event){
		var target = $(event.currentTarget);
		var res_id = target.attr('res_id');
		this.options.res_id = res_id;
		
		var li = target.closest('li');
		var nom = $('.nom', li).html();
		
		this.showConfirm('Confirmation de suppression', 'Etes vous sûr de vouloir supprimer définitivement le reporting "'+nom+'" ?', $.proxy(this.confirmDeleteReport, this), 'Annuler', 'Supprimer');
	},
	confirmDeleteReport: function(confirm) {
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
		
		this.showConfirm('Confirmation de suppression', 'Etes vous sûr de vouloir enlever le reporting fourni "'+nom+'" ?', $.proxy(this.confirmDeleteReportShared, this), 'Annuler', 'Supprimer');
	},
	confirmDeleteReportShared: function(confirm) {
		if (confirm) {
			this.lancerDeleteShared();
		}
	},
	lancerDelete: function() {
		var id = this.options.res_id;
		$.post(getUrlPrefix() + '/reporting/delete/'+id, $.proxy(this.deleted, this));
	},
	lancerDeleteShared: function() {
		var id = this.options.res_id;
		$.post(getUrlPrefix() + '/reporting/delete_shared/'+id, $.proxy(this.deleted, this));
	},
	deleted: function(result) {
		var res = $(result);
		var rs_id = $('.rs_id', res).html();
		
		$(".cont > ul > li[rs_id="+rs_id+"]",this.element).remove();
	},
	getReportingNavigator: function() {
		return this.getParentNavigator();
	},
	options: {
		defaultLayout : false,
	}
});

$.widget('seriel.shareReport', $.seriel.modal_navigator, {
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
		
		$.post(getUrlPrefix() + '/reporting/search_indiv/'+type, datas, $.proxy(this.searched, this));
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
				// search elements selectionned in list.
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
		
		$.post(getUrlPrefix() + '/reporting/share/'+this.getReportingId(), datas, $.proxy(this.saved, this));
	},
	saved: function(result) {
		this.close();
	},
	getReportingId: function() {
		return $('.rs_id', this.element).html();
	},
	options: {
		search_form: null,
		compteWidget: null,
		defaultLayout : false,
	}
});