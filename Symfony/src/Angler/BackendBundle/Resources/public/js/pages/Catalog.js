Catalog = function(options){
	Page.call(this, options);

	this.controller = 'catalog';
	this.action = 'list';
};
extend(Catalog, Page);