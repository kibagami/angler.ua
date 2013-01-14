function PanelView () {
	this.visible = ko.observable(false);

	this.firstName = ko.observable("Stephen");
	this.lastName = ko.observable("King");
}

PanelView.prototype.setVisible = function (state) {
	this.visible(state);
};

PanelView.prototype.toggle = function () {
	if(!this.visible()) {
		this.visible(true);
	} else {
		this.visible(false);
	}
};

