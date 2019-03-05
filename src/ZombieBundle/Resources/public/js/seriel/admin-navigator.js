$.widget('seriel.adminNav', $.seriel.navigator, {
    _create: function () {
        this._super();

        var westBlock = $(' > .menu_left', this.element);
        var westWidth = westBlock.attr('layout-size');
        if (!westWidth)
            westWidth = westBlock.width();

        var eastBlock = $(' > .menu_right', this.element);
        var eastWidth = eastBlock.attr('layout-size');
        if (!eastWidth)
            eastWidth = eastBlock.width();
        
        this.element.serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            west: {
                size: westWidth ? westWidth : 'auto'
            },
            east: {
                size: eastWidth ? eastWidth : 'auto'
            }
        });
		
        this.options.destDiv = $(' > .admin_container', this.element);
        this.initHasDestDivContainer(this.options.destDiv);

        
        $('#admin_accordion', this.element).serielAccordion({heightStyle: 'fill', animate: 200});

        $(".button", this.element).bind('click', $.proxy(this.select, this));
    },
    getDestDiv: function () {
        return this.options.destDiv;
    },
    load: function (dest) {
        if ($.type(dest) === 'string') dest = parseHash(dest);

        for (var i = 0; i < count(dest); i++) {
            var elem = dest[i];
            var label = elem.getLabel();

            alert(elem);
        }

        this.loadContent('./' + dest);
        return true;
    },
    checkContent: function (url, key, serhash) {
        if (!serhash) serhash = parseHash(getCleanHash());
        this.verifUrl(serhash);
    },
    verifUrl: function (dest) {
        //get URL
        if (!dest) {
            dest = parseHash(getCleanHash());
        }

        if (count(dest) > 1) {
            var accordion = dest[1].getLabel();

            $('h3.' + accordion, $('#admin_accordion', this.element)).trigger('click');

            if (accordion == 'societe') {
                if (count(dest) > 2) {
                    var subMenu = dest[2].getLabel();

                    if (subMenu == 'informations') {
                        this.loadContent(getUrlPrefix() + '/admin/societe/informations');
                    }
                    if (subMenu == 'utilisateurs') {
                        this.loadContent(getUrlPrefix() + '/admin/societe/utilisateurs');
                    }
                    if (subMenu == 'profils') {
                        this.loadContent(getUrlPrefix() + '/admin/societe/profils');
                    }                    
                    if (subMenu == 'donnees') {
                        this.loadContent(getUrlPrefix() + '/admin/societe/donnees');
                    }
                }
            }
        }
    },
    updateSocieteInfos: function(content) {
		this.refreshElementWithContent('/admin/societe/informations', content.html());
	},
    select: function (event) {
        var target = $(event.currentTarget).parent();
        $('.active').removeClass('active');
        target.addClass('active');
    },

    infosStructure: function(){
    	
    },
    options: {
        destDiv: null
    }
});



$.widget('seriel.gestionnaireNav', $.seriel.navigator, {
    _create: function () {
        this._super();

        var northBlock = $(' > .admin_list_container > .gestionnaires_head', this.element);
        var northWidth = northBlock.attr('layout-size');
        if (!northWidth)
            northWidth = northBlock.width();

        var centerBlock = $(' > .admin_list_container > .liste_container', this.element);
        var centerWidth = centerBlock.attr('layout-size');
        if (!centerWidth)
            centerWidth = centerBlock.width();

        $('.admin_list_container', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            north: {
                size: northWidth ? northWidth : 'auto'
            },
            center: {
                size: centerWidth ? centerWidth : 'auto'
            }
        });
        this.options.actionMenu = $('.actions', this.element);
        this.options.actionMenu.actionMenu();

        this.options.actionMenu.css('z-index', 100);
        $('.list_content table > tbody > tr', this.element).bind('dblclick', $.proxy(this.dblClicked, this));
        $('.list_content', this.element).ser_list();
        $('.list_content', this.element).ser_list('setBuildContextMenuCallback', $.proxy(this.buildContextMenu, this));
    },
    backInDom: function() {
		// Must be overwritted.
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
    openModal: function (title, url, options) {
        this.openModalInsideParentNavigator(title, url, options);
    },
    dblClicked: function () {
        var elems = $('.list_content', this.element).ser_list('getElemsSelected');
        if (elems.size() == 0) return;

 
        if (elems.size() > 1) return;
        for (var i = 0; i < elems.size(); i++) {
           var elem = $(elems.get(i));
        }
        if (elem) {
           var uid = elem.attr('uid');
        }
        this.runModifierMetier(uid);
    },
    openModalWithContent: function (title, content, options) {
        this.openModalWithContentInsideParentNavigator(title, content, options);
    },
    onAction: function (action, title) {
        console.log("GestionnaireNAV_ACTION[" + action + "]");

        if (action == 'ajouter_gestionnaire') {
            this.runAjouterGestionnaire();
            return;
        }
    },
    runAjouterGestionnaire: function () {
        this.openModal('Créer un nouvel utilisateur', getUrlPrefix() + '/admin/societe/utilisateurs/edit');
    },
    addLigne: function (gestionnaire) {
        gestionnaire.bind('dblclick', $.proxy(this.dblClicked, this));
        $('.list_content', this.element).ser_list('addLine', gestionnaire);
    },
    buildContextMenu: function (trigger, e) {
        var numElems = $('.list_content', this.element).ser_list('getNumElemsSelected');

        if (numElems > 1) {
        	return false;
        } else {
            return {
                callback: $.proxy(this.contextMenuAction, this),
                items: {
                    "edit": {name: "Informations", icon: "edit"}
                }
            };
        }

    },
    contextMenuAction: function (key, options) {
        var elems = $('.list_content', this.element).ser_list('getElemsSelected');
        if (elems.size() == 0) return;

        if (key == 'edit') {
            if (elems.size() > 1) return;
            for (var i = 0; i < elems.size(); i++) {
                var elem = $(elems.get(i));
            }
            if (elem) {
                var uid = elem.attr('uid');
            }
            this.runModifierMetier(uid);
        }
	else if (key == 'gc'){
			
		if (elems.size() > 1) return;
			
		for (var i = 0; i < elems.size(); i++) {
			var elem = $(elems.get(i));
		}
		if(elem){
			var uid = elem.attr('uid');
		}
			
		this.runAttributionGC(uid);
	}
        else if (key == 'delete') {
            var uids = [];
            for (var i = 0; i < elems.size(); i++) {
                var elem = $(elems.get(i));
                elem.addClass('removing');
                var uid = elem.attr('uid');

                if (uid) {
                    uids.push(uid);
                }
            }

            if (count(uids) > 0)
                this.remove(uids);
        }
    },
    runAttributionGC: function(uid){
    	this.openModal('Attribution : Clients / Métiers',getUrlPrefix()+'/admin/societe/utilisateurs/gc/'+uid)
    },
    runModifierMetier: function (uid) {
        this.openModal('Modifier un utilisateur', getUrlPrefix() + '/admin/societe/utilisateurs/edit/' + uid);
    },
    updateLigne: function (gestionnaire) {
        gestionnaire.bind('dblclick', $.proxy(this.dblClicked, this));
        $('.list_content', this.element).ser_list('updateLine', gestionnaire);
    },
    remove: function (uids) {
        if (count(uids) == 0) return;

        var datas = {};
        for (var i = 0; i < count(uids); i++) {
            datas['uids[' + i + ']'] = uids[i];
        }
        $.post(getUrlPrefix() + '/admin/societe/utilisateurs/delete', datas, $.proxy(this.removed, this));
    },
    removed: function (result) {
        var res = $(result);
        if (res.hasClass('success')) {
            var uidContainers = $('.uid', res);

            for (var i = 0; i < uidContainers.size(); i++) {
                var cont = $(uidContainers.get(i));
                var uid = cont.html();
                $('.list_content', this.element).ser_list('removeLine', uid);
            }
        }
        else {
            var uidContainers = $('.uid', res);

            for (var i = 0; i < uidContainers.size(); i++) {
                var cont = $(uidContainers.get(i));
                var uid = cont.html();
                if (uid) {
                    elem = $('.list_content', this.element).ser_list('getElemWithUid', uid);
                    if (elem) {
                        elem.removeClass('removing');
                    }
                }
            }
        }
    },
    options: {
        actionMenu: null
    }
});

$.widget('seriel.listsNav', $.seriel.navigator, {
	_create: function(){
        this._super();	
		
		var northBlock = $(' > .admin_list_container > .emails_head', this.element);
        var northWidth = northBlock.attr('layout-size');
        if (!northWidth)
        	northWidth = northBlock.width();

        var centerBlock = $(' > .admin_list_container > .liste_container', this.element);
        var centerWidth = centerBlock.attr('layout-size');
        if (!centerWidth)
        	centerWidth = centerBlock.width();

        $('.admin_list_container', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            north: {
                size: northWidth ? northWidth : 'auto'
            },
            center: {
                size: centerWidth ? centerWidth : 'auto'
            }
        });
        
		this.options.actionMenu = $('.actions', this.element);
		this.options.actionMenu.actionMenu();
		
		this.options.actionMenu.css('z-index', 100);
	},
	backInDom: function() {
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
	openModal : function(title, url, options) {
		this.openModalInsideParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideParentNavigator(title, content, options);
	},
	onAction: function(action, title) {
	
		if (action == 'edit_actionAuto') {
			this.runAjouterActionAuto();
			return;
		}
	},
	runAjouterActionAuto: function(){
		this.openModal('Ajouter une action automatique ',getUrlPrefix()+'/admin/actionsAuto/edit');
	},
	addLigne: function(typeMat){
		typeMat.bind('dblclick', $.proxy(this.dblClicked, this));
		$('.list_content', this.element).ser_list('addLine', typeMat);
	},
	buildContextMenu: function(trigger, e) {
		var numElems = $('.list_content', this.element).ser_list('getNumElemsSelected');
		
		if (numElems > 1) {
			return {
	            callback: $.proxy(this.contextMenuAction, this), 
	            items: {
					"delete": {name: "Supprimer", icon: "delete"}
	            }
	        };
		} else {
			return {
	            callback: $.proxy(this.contextMenuAction, this), 
	            items: {
	            	"edit": {name: "Modifier", icon: "edit"},
					"delete": {name: "Supprimer", icon: "delete"}
	            }
	        };
		}
		
	},
	contextMenuAction: function(key, options) {
		var elems = $('.list_content', this.element).ser_list('getElemsSelected');
		if (elems.size() == 0) return;
		
		if (key == 'edit') {
			if (elems.size() > 1) return;
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
			}
			if(elem){
				var uid = elem.attr('uid');
			}
			this.runModifierModele(uid);
		} 
		else if (key == 'delete'){
			var uids = [];
            for (var i = 0; i < elems.size(); i++) {
               var elem = $(elems.get(i));
               elem.addClass('removing');
               var uid = elem.attr('uid');
           
               if(uid) {
                  uids.push(uid);
               }
            }
           
            if (count(uids) > 0) 
            	this.remove(uids);
		}
	},
	dblClicked: function(){
		var target = $(event.currentTarget);
		var id = target.attr('uid');
		
		this.runModifierActionAuto(id);
	},
	runModifierActionAuto: function(uid){
		this.openModal('Modifier une action automatique',getUrlPrefix()+'/admin/actionsAuto/edit/'+uid);
	},
	updateLigne: function(typeMat){
		typeMat.bind('dblclick', $.proxy(this.dblClicked, this));
		$('.list_content', this.element).ser_list('updateLine',typeMat);
	},
	remove: function(uids) {
          if (count(uids) == 0) return;
         
          var datas = {};
          for (var i = 0; i < count(uids); i++) {
             datas['uids['+i+']'] = uids[i];
          }
		this.options.datasDelete = datas;
          $.post(getUrlPrefix()+'/admin/actionsAuto/delete', datas, $.proxy(this.removed, this));
	},
	removed: function(result) {
        var res = $(result);

		if (res.hasClass('success')) {
           var uidContainers = $('.uid', res);
                       
           for (var i = 0; i < uidContainers.size(); i++) {
             var cont = $(uidContainers.get(i));
             var uid = cont.html();
             $('.list_content', this.element).ser_list('removeLine', uid);
           }
        } 
        else {
	        var uidContainers = $('.uid', res);
	       
	        for (var i = 0; i < uidContainers.size(); i++) {
	        	var cont = $(uidContainers.get(i));
	            var uid = cont.html();
	            if (uid) {
	            	elem = $('.list_content', this.element).ser_list('getElemWithUid', uid);
	            	if (elem) {
	                   elem.removeClass('removing');
	            	}
	            }
	        }
        }
	},
	options:{
		actionMenu: null,
		datasDelete: {}
	}
});

$.widget('seriel.infosComplementaireNav', $.seriel.navigator, {
	_create: function(){
        this._super();	
		
		var northBlock = $(' > .admin_list_container > .metier_head', this.element);
        var northWidth = northBlock.attr('layout-size');
        if (!northWidth)
        	northWidth = northBlock.width();

        var centerBlock = $(' > .admin_list_container > .liste_container', this.element);
        var centerWidth = centerBlock.attr('layout-size');
        if (!centerWidth)
        	centerWidth = centerBlock.width();

        $('.admin_list_container', this.element).serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            north: {
                size: northWidth ? northWidth : 'auto'
            },
            center: {
                size: centerWidth ? centerWidth : 'auto'
            }
        });
        
		this.options.actionMenu = $('.actions', this.element);
		this.options.actionMenu.actionMenu();
		
		this.options.actionMenu.css('z-index', 100);
		
		$('.list_content table > tbody > tr', this.element).bind('dblclick', $.proxy(this.dblClicked, this));
		
		$('.list_content', this.element).ser_list();
		$('.list_content', this.element).ser_list('setBuildContextMenuCallback', $.proxy(this.buildContextMenu, this));
	},
	backInDom: function() {
		try {
			this.options.actionMenu.css('z-index', 100);			
		} catch (e) {
			
		}
	},
	dblClicked: function(event) {
		var target = $(event.currentTarget);
		var id = target.attr('uid');
		
		this.runModifierInfo(id);
	},
	openModal : function(title, url, options) {
		this.openModalInsideParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideParentNavigator(title, content, options);
	},
	onAction: function(action, title) {
		console.log("infosComplementairesNAV_ACTION["+action+"]");
		
		if (action == 'ajouter_info') {
			this.runAjouterInfo();
			return;
		}
	},
	runAjouterInfo: function(){
		this.openModal('Ajouter une information',getUrlPrefix()+'/admin/clients/infos_complementaire/edit');
	},
	addLigne: function(metier){
		metier.bind('dblclick', $.proxy(this.dblClicked, this));
		$('.list_content', this.element).ser_list('addLine', metier);
	},
	getStructure: function(){
		return $('.structure',this.element).html();
	},
	buildContextMenu: function(trigger, e) {
		var numElems = $('.list_content', this.element).ser_list('getNumElemsSelected');
		
		if (numElems > 1) {
			return {
	            callback: $.proxy(this.contextMenuAction, this), 
	            items: {
					"delete": {name: "Supprimer", icon: "delete"}
	            }
	        };
		} else {
			return {
	            callback: $.proxy(this.contextMenuAction, this), 
	            items: {
	            	"edit": {name: "Modifier", icon: "edit"},
					"delete": {name: "Supprimer", icon: "delete"}
	            }
	        };
		}
		
	},
	contextMenuAction: function(key, options) {
		var elems = $('.list_content', this.element).ser_list('getElemsSelected');
		if (elems.size() == 0) return;
		
		if (key == 'edit') {
			if (elems.size() > 1) return;
			for (var i = 0; i < elems.size(); i++) {
				var elem = $(elems.get(i));
			}
			if(elem){
				var uid = elem.attr('uid');
			}
			this.runModifierInfo(uid);
		} 
		else if (key == 'delete'){
			var uids = [];
            for (var i = 0; i < elems.size(); i++) {
               var elem = $(elems.get(i));
               elem.addClass('removing');
               var uid = elem.attr('uid');
           
               if(uid) {
                  uids.push(uid);
               }
            }
           
            if (count(uids) > 0) 
            	this.remove(uids);
		}
	},
	runModifierInfo: function(uid){
		this.openModal('Modifier une information',getUrlPrefix()+'/admin/clients/infos_complementaire/edit/'+uid);
	},
	updateLigne: function(metier){
		metier.bind('dblclick', $.proxy(this.dblClicked, this));
		$('.list_content', this.element).ser_list('updateLine',metier);
		
	},
	remove: function(uids) {
          if (count(uids) == 0) return;
         
          var datas = {};
          for (var i = 0; i < count(uids); i++) {
             datas['uids['+i+']'] = uids[i];
          }	
          $.post(getUrlPrefix()+'/admin/clients/infos_complementaire/delete', datas, $.proxy(this.removed, this));
	},
	removed: function(result) {
        var res = $(result);
        if (res.hasClass('success')) {
           var uidContainers = $('.uid', res);
                       
           for (var i = 0; i < uidContainers.size(); i++) {
             var cont = $(uidContainers.get(i));
             var uid = cont.html();
             $('.list_content', this.element).ser_list('removeLine', uid);
           }
        } 
        else {
	        var uidContainers = $('.uid', res);
	       
	        for (var i = 0; i < uidContainers.size(); i++) {
	        	var cont = $(uidContainers.get(i));
	            var uid = cont.html();
	            if (uid) {
	            	elem = $('.list_content', this.element).ser_list('getElemWithUid', uid);
	            	if (elem) {
	                   elem.removeClass('removing');
	            	}
	            }
	        }
        }
	},
	openModal: function (title, url, options) {
        var dest = this.element;
        var parent = this.getParentNavigator();
        if (parent) {
            dest = parent.element;
            var grand_pa = parent.getParentNavigator();
            if (grand_pa) {
                dest = grand_pa.element;
            }
        }

        this.openModalInside(title, url, dest, options);
    },
    openModalWithContent: function(title, content, options) {
    	var dest = this.element;
        var parent = this.getParentNavigator();
        if (parent) {
            dest = parent.element;
            var grand_pa = parent.getParentNavigator();
            if (grand_pa) {
                dest = grand_pa.element;
            }
        }
        
        this.openModalWithContentInside(title, content, dest, options);
    },
	options:{
		actionMenu: null
	}
});


$.widget('seriel.profilsSocieteListNav', $.seriel.navigator, {
    _create: function () {
        this._super();

        this.options.actionMenu = $('.actions', this.element);
        this.options.actionMenu.actionMenu();

        this.options.actionMenu.css('z-index', 100);

        this.options.list = $('.list_content', this.element);
        this.options.list.bind('refreshed', $.proxy(this.listRefreshed, this));

        this.options.list.ser_list();
        this.options.list.ser_list('setBuildContextMenuCallback', $.proxy(this.buildContextMenu, this));

        // rebind elements of the list.
        this.initLines($('.list_content table > tbody > tr', this.element));
    },
    listRefreshed: function() {
    	this.initLines($('.list_content table > tbody > tr', this.element));
    },
    initLines: function(lines) {
    	lines.unbind('dblclick');
    	lines.bind('dblclick', $.proxy(this.profilDblClicked, this));
    },
    backInDom: function() {
		try {
			this.options.actionMenu.css('z-index', 100);
		} catch (e) {
			
		}
	},
    buildContextMenu: function (trigger, e) {
        var numElems = $('.list_content', this.element).ser_list('getNumElemsSelected');

        if (numElems > 1) {
            return {
                callback: $.proxy(this.contextMenuAction, this),
                items: {
                    "delete": {name: "Supprimer", icon: "delete"}
                }
            };
        } else {
            return {
                callback: $.proxy(this.contextMenuAction, this),
                items: {
                    "edit": {name: "Modifier les droits", icon: "edit"},
                    "accueil": {name: "Editer la page d'accueil", icon: "home"},
                    "delete": {name: "Supprimer", icon: "delete"}
                }
            };
        }

    },
    contextMenuAction: function (key, options) {
        var elems = $('.list_content', this.element).ser_list('getElemsSelected');
        if (elems.size() == 0) return;

        if (key == 'edit') {
            if (elems.size() > 1) return;
            for (var i = 0; i < elems.size(); i++) {
                var elem = $(elems.get(i));
            }
            if (elem) {
                var uid = elem.attr('uid');
            }
            this.runModifierProfil(uid); 
        }
        else if (key == 'accueil') {
            if (elems.size() > 1) return;
            for (var i = 0; i < elems.size(); i++) {
                var elem = $(elems.get(i));
            }
            if (elem) {
                var uid = elem.attr('uid');
            }
            this.runModifierAccueil(uid);
        }
        else if (key == 'delete') {
            alert('TODO');
            return;
            var uids = [];
            for (var i = 0; i < elems.size(); i++) {
                var elem = $(elems.get(i));
                elem.addClass('removing');
                var uid = elem.attr('uid');

                if (uid) {
                    uids.push(uid);
                }
            }

            if (count(uids) > 0)
                this.remove(uids);
        }
    },
    onAction: function (action, title) {
        if (action == 'ajout_profil') {
            this.ajoutProfil();
        }
    },
    ajoutProfil: function () {
        this.openModal('Nouveau profil utilisateur', getUrlPrefix() + '/admin/societe/profils/nouveau', {'height': 600});
    },
    profilDblClicked: function (event) {
        var target = $(event.currentTarget);
        var id = target.attr('uid');

        this.runModifierProfil(id);
    },
    runModifierProfil: function (id) {
        var tr = this.options.list.ser_list('getElemWithUid', id);
        var nom = null;
        if (tr) {
            // get profil.
            nom = tr.attr('nom');
        }
        this.openModal('Modification du profil' + (nom ? ' "' + nom + '"' : ''), getUrlPrefix() + '/admin/societe/profils/edit/' + id, {'height': 600});
    },
    runModifierAccueil: function (id) {
        var tr = this.options.list.ser_list('getElemWithUid', id);
        var nom = null;
        if (tr) {
            // On récupère le nom du profil.
            nom = tr.attr('nom');
        }
        this.openModal('Personnalisation de la page d\'accueil' + (nom ? ' "' + nom + '"' : ''), getUrlPrefix() + '/admin/profils/accueil/' + id, {
            'width': 1300,
            'height': 550
        });
    },
    addLigne: function (profil) {
        this.options.list.ser_list('addLine', profil);
        this.initLines(profil);
    },
    updateLigne: function (profil) {
    	this.options.list.ser_list('updateLine', profil);
    	this.initLines(profil);
    },
    openModal: function (title, url, options) {
        var dest = this.element;
        var parent = this.getParentNavigator();
        if (parent) {
            dest = parent.element;
        }

        this.openModalInside(title, url, dest, options);
    },
    options: {
        list: null
    }
});

$.widget('seriel.edit_profil_societe', $.seriel.modal_navigator, {
    _create: function () {
        this._super();

        this.options.form = $('form', this.element);
        this.options.form.bind('submit', $.proxy(this.submit, this));

        this.options.profil_type_select = $('#form_profil_type', this.element);
        this.options.profil_type_select.bind('change', $.proxy(this.profilTypeChanged, this));

        // bind checkboxs.
        $('input[type=checkbox]', this.element).bind('change', $.proxy(this.checkboxChanged, this));

        this.profilTypeChanged();
        $('#form_nom', this.element).focus().select();
    },
    profilTypeChanged: function () {
        var val = this.options.profil_type_select.val();

        if (val == 3) {
            // INTERVENANT
            var lis_OK = $('li[cred_id].compat_soc_intervenant', this.element);
            var lis_KO = $('li[cred_id]:not(.compat_soc_intervenant)', this.element);

            lis_KO.addClass('hidden');
            lis_OK.removeClass('hidden');
        } else if (val == 4) {
            // RESPONSABLE
            var lis_OK = $('li[cred_id].compat_soc_responsable', this.element);
            var lis_KO = $('li[cred_id]:not(.compat_soc_responsable)', this.element);

            lis_KO.addClass('hidden');
            lis_OK.removeClass('hidden');
        }

        // Let's check all groups of creds and show/hide in consequence.
        var roots = $('ul.profils_credentials_ul > li', this.element);
        for (var i = 0; i < roots.size(); i++) {
            var root = $(roots.get(i));
            // get all visible li.
            var visibles = $(' > ul > li:not(.hidden)', root);
            if (visibles.size() == 0) {
                root.addClass('hidden');
            } else {
                root.removeClass('hidden');
            }
        }
    },
    checkboxChanged: function (event) {
        var target = $(event.currentTarget);
        var checked = target.is(':checked');
        var li = target.closest('li');

        if (checked) li.addClass('checked');
        else li.removeClass('checked');
    },
    koClicked: function () {
        this.element.dialog('close');
    },
    okClicked: function () {
        $('button[type=submit], input[type=submit]', this.element).trigger('click');
    },
    submit: function (event) {
        event.stopPropagation();
        this.save();
        return false;
    },
    save: function () {
        // get all checked li.
        this.setLoading();

        var lis = $('li.checked:not(.hidden)', this.element);
        
        var datas = getFormattedFormValues(this.options.form);

        for (var i = 0; i < lis.size(); i++) {
            var li = $(lis.get(i));

            // It is in hidden li ?
            var parent_hidden = li.closest('li.hidden');
            if (parent_hidden.size() > 0) continue;

            var id = li.attr('cred_id');
            var level = null;
            var level_select = $(' > select.cred_level', li);
            if (level_select.size() == 1) {
                level = level_select.val();
            }
            var choice = null;
            var choice_select = $(' > select.cred_choice', li);
            if (choice_select.size() == 1) {
            	choice = choice_select.val();
            }

            datas['creds[' + i + '][id]'] = id;
            if (level) datas['creds[' + i + '][level]'] = level;
            if (choice) datas['creds[' + i + '][choice]'] = choice;
        }

        var profil_id = this.getProfilId();

        if (profil_id) $.post(getUrlPrefix() + '/admin/societe/profils/edit/' + profil_id, datas, $.proxy(this.saved, this));
        else $.post(getUrlPrefix() + '/admin/societe/profils/nouveau', datas, $.proxy(this.saved, this));
    },
    saved: function (result) {

        var res = $(result);
        
        if (res.hasClass('success')) {
            //add row
            var profil = $('tr', res);
            
            if (!this.getProfilId()) {
                this.getParentNavigator().addLigne(profil);
            }
            else {
                this.getParentNavigator().updateLigne(profil);
            }
            //close modal

            this.close();
            this.hideLoading();
            return;
        }
        var form = $('form', res);
        if (form) {
            this.options.form.html(form.html());
            this.options.submit_button = $('[type=submit]', this.options.form);
        }
    },
    getProfilId: function () {
        return $('.profil_id', this.element).html();
    },
    options: {
        defaultLayout: false,
        form: null,
        profil_type_select: null
    }
});

$.widget('seriel.donneesInfosNav', $.seriel.navigator, {
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
	options : {
		actionMenu : null,
		societeId : null
	}
});

