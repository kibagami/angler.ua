Wishlist = function(options){
	Page.call(this, options);

	this.controller = 'wishlist';
	this.action = 'list';
};
extend(Wishlist, Page)