function PanelViewModel () {
    this.visible = ko.observable(false);

    this.firstName = ko.observable("Stephen");
    this.lastName = ko.observable("King");
}

PanelViewModel.prototype.setVisible = function (state) {
    this.visible(state);
};

PanelViewModel.prototype.toggle = function () {
    if(!this.visible()) {
        this.visible(true);
    } else {
        this.visible(false);
    }
};

window.onload = function () {
    window.panel = new PanelViewModel();
    ko.applyBindings(panel, document.getElementById('panel'));
};

