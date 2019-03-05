$.widget('seriel.relatedwordArticleSemantiqueNav', $.seriel.navigator, {
	_create : function() {
		this._super();
		
		$('.article_similarity_container .list_content', this.element).ser_list();
		
	},
	openModal : function(title, url, options) {
		this.openModalInsideGrandParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideGrandParentNavigator(title, content, options);
	},
	options: {
		
	}
});