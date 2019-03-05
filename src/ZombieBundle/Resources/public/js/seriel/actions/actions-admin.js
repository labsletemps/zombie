$.widget('seriel.crea_gestionnaire', $.seriel.modal_navigator, {
    _create: function () {
        this._super();
        this.options.form = $('form', this.element);
        this.options.form.bind('submit', $.proxy(this.submitForm, this));

        this.options.submit_button = $('[type=submit]', this.options.form);

        this.options.login = $('[name=login]', this.element);
        this.options.pwd = $('[name=pwd]', this.element);
        this.options.pwd.bind('click', $.proxy(this.viderPwd, this));

    },
    viderPwd: function () {
        this.options.pwd.unbind();
        this.options.pwd.val('');
    },
    koClicked: function () {
        var parentNav = this.getParentNavigator();
        if (parentNav) {

        }
        this.element.dialog('close');
    },
    okClicked: function () {
        this.options.submit_button.trigger('click');
    },
    submitForm: function (event) {
        event.stopPropagation();
        this.save();
        return false;
    },
    save: function () {
        this.setLoading();
        
        var datas = getFormattedFormValues(this.options.form);

        var login = $('[name=login]', this.element).val();
        datas['login'] = login;

        var pwd = $('[name=pwd]', this.element).val();
        datas['pwd'] = pwd;

        var actif = $('[name=actif]', this.element).val();
        datas['actif'] = actif;

        var admin = $('[name=admin]', this.element).val();
        datas['admin'] = admin;
        
        var profil_select = $('[name=profil]', this.element);
        var profil_id = profil_select.val();
        
        datas['profil_id'] = profil_id;

        //get id
        var id = $('.select_id').html();
        // test and send 
        if (id) {
            $.post(getUrlPrefix() + '/admin/societe/utilisateurs/edited/' + id, datas, $.proxy(this.saved, this));
        }
        else {
            $.post(getUrlPrefix() + '/admin/societe/utilisateurs/edited', datas, $.proxy(this.saved, this));
        }
        return;
    },
    saved: function (response) {
        var res = $(response);
        if (res.hasClass('success')) {
            //add row
            var tva = $('tr', res);
            var mode = $('.mode', res).html();
            if (mode == 'add') {
                this.getParentNavigator().addLigne(tva);

            }
            else {

                this.getParentNavigator().updateLigne(tva);
            }
            //close modal
            this.close();
            return;
        }
        var form = $('form', res);
        if (form) {
            this.options.form.html(form.html());
            this.options.submit_button = $('[type=submit]', this.options.form);
            this.hideLoading();
        }
    },
    options: {
        defaultLayout: false,
        login: null,
        form: null,
        submit_button: null
    }
});



$.widget('seriel.attribution_gc', $.seriel.modal_navigator, {
    _create: function() {
        this._super();
    
        this.options.list = $('.div_gauche', this.element);
        this.options.list.ser_list();
        
        
        
        this.options.list.bind('select_changed', $.proxy(this.listSelectChanged, this));
        $('tr',this.element).bind('dblclick', function(event) { event.stopPropagation; return false; });
        
        $('ul.metiers_select', this.element).selectable();
        
        $('.metierForStructure',this.element).each(function(){
            $(this).hide();
        })
    },
    listSelectChanged: function(event){
        var selected_elems = this.options.list.ser_list('getElemsSelected');
        
        this.setLoading();
        if (selected_elems.size() != 1) return;
        var tr = selected_elems;
        
        
        
        var id = $(tr).attr('uid');
        
        $('.metierForStructure',this.element).each(function(){
            if(id == $(this).attr('parent')){
                $(this).show();
            }else{
                $(this).hide();
            }
            
        })
        this.hideLoading();
    },
    koClicked: function() {
        var parentNav = this.getParentNavigator();
        if (parentNav) {
            
        }
        this.element.dialog('close');
    },
    okClicked: function() {
        
        this.save();
    },
    save: function() {
        this.setLoading();
        var datas = {};
        var structure = {}
        var gest_id = $('.gest_id',this.element).html();
        var cpt = 0;
        $('.metierForStructure',this.element).each(function(){
            var elems = new Array();
            $('ul.metiers_select > li.ui-selected', $(this)).each(function(){
                elems.push($(this).attr('metier_id'));
            });
            
            if (elems.length != 0) {
                var structure_id = $(this).attr('parent');
                
                var tab = {};
                tab['structure'] = structure_id;
                var metier = {};
                for(var i = 0 ; i < elems.length; i++){
                    metier[i]=elems[i];
                }    
                tab['metiers'] = metier;
                structure[cpt] = tab;
                cpt++;
            }            
        });
        datas['GCS'] = structure;
        console.log(datas);
        $.post(getUrlPrefix()+'/admin/societe/utilisateurs/gc/save/'+gest_id, datas, $.proxy(this.saved, this));
        return;
    },
    saved: function(response) {    
        this.close();
        return;
    },
    options: {
        defaultLayout: false
    }
});

$.widget('seriel.edit_infoComplementaire', $.seriel.modal_navigator, {
    _create: function () {
        this._super();

        $(' > .modal-helper', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            north: {}
        });

        $(' > .modal-center', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            west: {
                size: 35
            }
        });
        
        this.options.form = $('form', this.element);
        this.options.form.bind('submit', $.proxy(this.submitForm, this));

        this.options.submit_button = $('[type=submit]', this.options.form);
        this.options.crea = $('.crea',this.element).html();
       
    },
    koClicked: function () {
        var parentNav = this.getParentNavigator();
        if (parentNav) {

        }
        this.element.dialog('close');
    },
    okClicked: function () {
    	this.options.submit_button.trigger('click');
    	return
    },
    submitForm: function(event) {
    	 event.stopPropagation();
    	 
   		 this.save(); 
        
         return false;
    },
    save: function () {
    	
    	this.setLoading();
    	var datas = getFormattedFormValues(this.options.form);
    
     	var structure = this.getParentNavigator().getStructure();
     	datas['structure'] = structure;
     	var crea = this.options.crea; 
     	 if(crea == 0){
     		$.post(getUrlPrefix() + '/admin/clients/infos_complementaire/edit/'+this.getInfoId(), datas, $.proxy(this.saved, this));
     	 }
     	 else {
     		$.post(getUrlPrefix() + '/admin/clients/infos_complementaire/edit', datas, $.proxy(this.saved, this));
     	 }
        
        return;
    },
    saved: function (response) {
        var res = $(response);
        if (res.hasClass('success')) {
            // add row
            var metier = $('tr', res);
            var crea = $('.crea',this.element).html();
            if(crea == 1){
            	this.getParentNavigator().addLigne(metier);
            }else {
            	this.getParentNavigator().updateLigne(metier);
            }
            
            //close modal
            this.close();
        }
        else {
            alert('error');
        }

    },
    getInfoId: function() {
    	return $('.info_id',this.element).html();
    },
    options: {}
});


$.widget('seriel.edit_signatureEmail', $.seriel.modal_navigator, {
    _create: function () {
        this._super();

        $(' > .modal-helper', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            north: {}
        });

        $(' > .modal-center', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            west: {
                size: 35
            }
        });
        
        var editor = $('textarea.signature_textarea', this.element);
        this.options.editorUid = editor.attr('id');
        
        // init cke editor
        this.options.cke = CKEDITOR.replace(this.options.editorUid, {
            height: 280
         });
        this.options.cke.on('loaded', $.proxy(this.editorLoaded, this));
    },
    editorLoaded : function() {
		$('.cke iframe', this.element).focus();
		this.options.cke.focus();
	},
    koClicked: function () {
        this.element.dialog('close');
    },
    okClicked: function () {
    	this.save();
    },
    save: function () {
    	
    	var text = this.options.cke.getData();
		if (editorTextEmpty(text)) {
			text = '';
		}
    	
    	this.setLoading();
    	
    	var data = { 'submit': 1, 'signature' : text };

  		$.post(getUrlPrefix() + '/societe/signature_email/'+this.getSocieteId(), data, $.proxy(this.saved, this));
        
        return;
    },
    saved: function (response) {
        var res = $(response);
        if (res.hasClass('success')) {
            // close modal
            this.close();
        }
        else {
            alert('erreur lors de la sauvegarde');
            this.hideLoading();
        }
    },
    getSocieteId: function() {
    	return $('.societe_id',this.element).html();
    },
    options: {
    	editorUid: null,
    	cke: null
    }
});

$.widget('seriel.edit_profil_accueil', $.seriel.modal_navigator, {
    _create: function () {
        this._super();

        // On bind tous les boutons ajouter gauche puis droite.
        $('.list_saved_searches li .addleft', this.element).bind('click', $.proxy(this.addLeftClicked, this));
        $('.list_saved_searches li .addright', this.element).bind('click', $.proxy(this.addRightClicked, this));

        $('.list_left ul, .list_right ul', this.element).sortable({'axis': 'y'});
        $('.list_left .add_button, .list_right .add_button', this.element).bind('click', $.proxy(this.addGroupClicked, this));

        // On récupère les groupes existants et le initialise.
        var groups = $('.list_left li.group, .list_right li.group', this.element);
        this.initGroups(groups);

        // On récupère les recherches existantes et le initialise.
        var searches = $('.list_left li[rs_id], .list_right li[rs_id]', this.element);
        $('.remove', searches).bind('click', $.proxy(this.removeClicked, this));
    },
    initGroups: function (groups) {
        $('.edit', groups).bind('click', $.proxy(this.editGroupClicked, this));
        $('.delete', groups).bind('click', $.proxy(this.deleteGroupClicked, this));
    },
    addLeftClicked: function (event) {
        var target = $(event.currentTarget);
        var li = target.closest('li');
        var rs_id = li.attr('rs_id');
        var rs_type = li.attr('rs_type');

        this.addLeft(rs_id, rs_type);
    },
    addLeft: function (rs_id, rs_type) {
        var li = $('.list_saved_searches li[rs_id=' + rs_id + '][rs_type=' + rs_type + ']', this.element);
        var clone = li.clone();
        li.addClass('selected');

        // On bind le bouton remove de clone.
        $('.remove', clone).bind('click', $.proxy(this.removeClicked, this));

        $('.list_left .cnt > ul', this.element).append(clone);
    },
    addRightClicked: function (event) {
        var target = $(event.currentTarget);
        var li = target.closest('li');
        var rs_id = li.attr('rs_id');
        var rs_type = li.attr('rs_type');

        this.addRight(rs_id, rs_type);
    },
    addRight: function (rs_id, rs_type) {
        var li = $('.list_saved_searches li[rs_id=' + rs_id + '][rs_type=' + rs_type + ']', this.element);
        var clone = li.clone();
        li.addClass('selected');

        // On bind le bouton remove de clone.
        $('.remove', clone).bind('click', $.proxy(this.removeClicked, this));

        $('.list_right .cnt > ul', this.element).append(clone);
    },
    removeClicked: function (event) {
        var target = $(event.currentTarget);
        var li = target.closest('li');

        var rs_id = li.attr('rs_id');
        var rs_type = li.attr('rs_type');

        // On récupère l'élément dans la liste centrale.
        var li_orig = $('.list_saved_searches li[rs_id=' + rs_id + '][rs_type=' + rs_type + ']', this.element);

        li.remove();
        li_orig.removeClass('selected');
    },
    addGroupClicked: function (event) {
        var target = $(event.currentTarget);
        var container = target.parent().parent();

        var pos = 'gauche';
        if (container.hasClass('list_right')) pos = 'droite';

        var content = $('.add_groupe_container', this.element).clone().removeClass('hidden');
        $('input.pos', content).attr('value', pos);

        this.openModalWithContent('Nouveau groupe / menu de ' + pos, content.html(), {'width': 400, 'height': 180});
    },
    addGroup: function (nom, pos, icon) {
        var newGroup = $('<li class="group" num="new_' + this.options.group_souche + '"><span>' + nom + '<span class="edit"></span><span class="delete"></span></span></li>');
        if (icon) {
        	newGroup.attr('icon', icon);
        } else {
        	newGroup.removeAttr('icon');
        }
        this.options.group_souche++;

        this.initGroups(newGroup);

        if (pos == 'gauche') {
            $('.list_left .cnt > ul', this.element).append(newGroup);
        } else {
            $('.list_right .cnt > ul', this.element).append(newGroup);
        }
    },
    updateGroup: function (nom, num, icon) {
        // On récupère le group.
        var group = $('li.group[num=' + num + ']', this.element);
        if (group.size() == 1) {
        	if (icon) {
        		group.attr('icon', icon);
            } else {
            	group.removeAttr('icon');
            }
            group.html('<span>' + nom + '<span class="edit"></span><span class="delete"></span></span>');
            this.initGroups(group);
        }
    },
    editGroupClicked: function (event) {
        var target = $(event.currentTarget);
        var li = target.closest('li');

        var num = li.attr('num');
        var nom = strip_tags(li.html());

        var cnt = li.closest('.cnt');
        var container = cnt.parent();
        
        var icon = li.attr('icon');

        var pos = 'gauche';
        if (container.hasClass('list_right')) pos = 'droite';

        var content = $('.add_groupe_container', this.element).clone().removeClass('hidden');
        $('input.nom', content).attr('value', nom);
        $('input.pos', content).attr('value', pos);
        $('input.num', content).attr('value', num);
        if (icon) $('select.icon_select > option[value='+icon+']', content).attr('selected', 'selected');

        this.openModalWithContent('Modification du groupe', content.html(), {'width': 400, 'height': 180});
    },
    deleteGroupClicked: function (event) {
        var target = $(event.currentTarget);
        var li = target.closest('li');

        li.remove();
    },
    koClicked: function () {
        this.close();
    },
    okClicked: function () {
        this.setLoading();

        var datas = {'submit': '1'};

        // On récupère toutes les variables.
        var leftLis = $('.list_left .cnt > ul > li', this.element);
        for (var i = 0; i < leftLis.size(); i++) {
            li = $(leftLis.get(i));
            if (li.hasClass('group')) {
                // Il s'agit d'un group
                datas['left[' + i + '][type]'] = 'group';
                datas['left[' + i + '][nom]'] = strip_tags(li.html());
                var icon = li.attr('icon');
                if (icon) datas['left[' + i + '][icon]'] = icon;
            } else {
            	if ($(' > span', li).hasClass('list')) {
                    // Il s'agit d'une liste.
                    datas['left[' + i + '][type]'] = 'list';
                    datas['left[' + i + '][rs_id]'] = li.attr('rs_id');            		
            	} else if ($(' > span', li).hasClass('report')) {
                    // Il s'agit d'un reporting.
                    datas['left[' + i + '][type]'] = 'report';
                    datas['left[' + i + '][rs_id]'] = li.attr('rs_id');
            	}
            }
        }

        // On fait pareil pour la droite.
        var rightLis = $('.list_right .cnt > ul > li', this.element);
        for (var i = 0; i < rightLis.size(); i++) {
            li = $(rightLis.get(i));
            if (li.hasClass('group')) {
                // Il s'agit d'un group
                datas['right[' + i + '][type]'] = 'group';
                datas['right[' + i + '][nom]'] = strip_tags(li.html());
                var icon = li.attr('icon');
                if (icon) datas['right[' + i + '][icon]'] = icon;
            } else {
            	if ($(' > span', li).hasClass('list')) {
            		// Il s'agit d'une liste.
            		datas['right[' + i + '][type]'] = 'list';
            		datas['right[' + i + '][rs_id]'] = li.attr('rs_id');
            	} else if ($(' > span', li).hasClass('report')) {
            		// Il s'agit d'un reporting.
            		datas['right[' + i + '][type]'] = 'report';
            		datas['right[' + i + '][rs_id]'] = li.attr('rs_id');
            	}
            }
        }

        $.post(getUrlPrefix() + '/admin/profils/accueil/' + this.getProfilId(), datas, $.proxy(this.saved, this));
    },
    saved: function (result) {
        //this.hideLoading();
        this.close();
    },
    getProfilId: function () {
        return $('.profil_id', this.element).html();
    },
    options: {
        defaultLayout: false,
        group_souche: 0
    }
});

$.widget('seriel.edit_profil_accueil_add_group', $.seriel.modal_navigator, {
    _create: function () {
        this._super();
        this.options.form = $('form', this.element);
        this.options.form.bind('submit', $.proxy(this.submit, this));

        $('input.nom', this.element).focus().select();
    },
    koClicked: function () {
        this.close();
    },
    okClicked: function () {
        $('input[type=submit]', this.options.form).trigger('click');
    },
    submit: function (event) {
        event.stopPropagation();
        this.save();
        return false;
    },
    save: function () {
        var nom = $('input.nom', this.element).val();
        var icon = $('select.icon_select', this.element).val();
        var pos = this.getPos();
        var num = this.getNum();
        
        if (num) {
            this.getParentNavigator().updateGroup(nom, num, icon);
        } else {
            this.getParentNavigator().addGroup(nom, pos, icon);
        }


        this.close();
    },
    getPos: function () {
        return $('input.pos', this.element).val();
    },
    getNum: function () {
        return $('input.num', this.element).val();
    },
    options: {
        defaultLayout: false,
        form: null
    }
});
