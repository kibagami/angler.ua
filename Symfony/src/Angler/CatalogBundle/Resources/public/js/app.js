window.onload = function () {
//    window.panel = new PanelView();
    window.menu = new MenuViewModel();
//    ko.applyBindings(panel, document.getElementById('panel'));
	ko.applyBindings(menu);
};

