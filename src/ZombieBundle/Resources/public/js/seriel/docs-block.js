$.widget('seriel.docsBlock', {
	_create: function() {
		this.options.docs = $('.doc', this.element);
		
		if (this.getObjDOI()) {
			this.element.contextMenu({
				selector: ' > .doc',
				build: $.proxy(this.buildContextMenuCallback, this)
			});
		}
		
		$('.visibilite_profil_widget', this.element).visibilityProfilWidget();
	},
	buildContextMenuCallback: function(trigger, e) {
		var target = $(e.currentTarget);
		this.options.currentContextElem = target;
		
		return {
            callback: $.proxy(this.contextMenuAction, this), 
            items: {
            	"delete": {name: "Supprimer le fichier", icon: "delete"}
            }
		};
	},
	contextMenuAction: function(key, options) {
		if (this.options.currentContextElem) {
			var file_id = this.options.currentContextElem.attr('file_id');
			this.remove(file_id);
			this.options.currentContextElem = null;
		}
	},
	remove: function(file_id) {
		// Ok: let's remove
		this.setLoading();
		$.post(getUrlPrefix() + '/documents/supprimer/'+this.getObjDOI()+'/'+file_id, $.proxy(this.removed, this));
	},
	removed: function(result) {
		this.hideLoading();
		var res = $(result);
		if (res.hasClass('success')) {
			var file_id = res.html();
			$(' > .doc[file_id='+file_id+']', this.element).remove();
		}
	},
	setLoading: function() {
		// TODO
	},
	hideLoading: function() {
		// TODO
	},
	getObjDOI: function() {
		return this.element.attr('obj_doi');
	},
	options: {
		docs: null,
		currentContextElem: null
	}
});