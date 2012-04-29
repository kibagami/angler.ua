Profile = function(options){
	Page.call(this, options);

	this.controller = 'profile';
	this.action = 'list';
};
extend(Profile, Page);