function Page(options) {
	if (options == undefined) return null;
	var context = $('#pages');

	this.container = $('#page-' + options.id, context);
	this.visible = false;
	this.controller = null;
	this.action = null;
	this.params = null;
	this.request = null;
}

Page.prototype._init = function (object) {
	var len = arguments.length;
	if (len < 2) {
		throw new Error("Function Interface.implements called with " + arguments.length + "arguments, but expected at least 2.");
	}

	for (var i = 1; i < len; i++) {
		var iface = arguments[i];

		if (Interface !== iface.constructor) {
			throw new Error("Function Interface.implements expects arguments two and above to be instances of Interface.");
		}
		var methodsLen = iface.methods.length;
		for (var j = 0; j < methodsLen; j++) {
			var method = iface.methods[j];

			if (!object[method] || typeof object[method] !== 'function') {
				throw new Error("Function Interface.implements: object does not implement the " + iface.name + "ifacee. Method " + method + " was not found.");
			}
		}
	}
};

Page.prototype.init = function () {
	return this;
};

Page.prototype.parse_params = function () {
};

Page.prototype.load = function () {
	var url = '/' + this.controller + ((this.action != null) ? '/' + this.action : '') + ((this.params != null) ? '?' + this.params : '');

	this.request = $.ajax({
		type: 'GET',
		url: url,
		success: this.show,
		dataType: 'html',
		context: this
	});
};

Page.prototype.show = function (data) {

	this.container.html(data).removeClass('hidden');
	this.visible = true;
};

Page.prototype.render = function () {
	this.load();
};

Page.prototype.clean = function () {
	this.container.empty();
};

Page.prototype.hide = function () {
	this.container.addClass('hidden');
	this.visible = false;
};
