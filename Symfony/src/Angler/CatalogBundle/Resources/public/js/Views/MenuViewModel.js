function MenuViewModel() {
//	this.tabs = [
//			'Buttons',
//			'Dialogs',
//			'Date widgets',
//			'Tab manager',
//			'Lists'
//	];
	this.currentTab = ko.observable();
	this.tabs = [
		{
			id: '#buttons',
			view: "ButtonsView",
			title: "Buttons"
		},
		{
			id: '#dialogs',
			view: "DialogsView",
			title: "Dialogs"
		},
		{
			id: '#dates',
			view: "DatesView",
			title: "Date widgets"
		},
		{
			id: '#tabs',
			view: "TabsView",
			title: "Tab manager"
		},
		{
			id: '#lists',
			view: "ListsView",
			title: "List manager"
		}
	];

	var mi = this;
	this.select = function(data, event) {
		var element = $(event.currentTarget);
		var current = mi.currentTab();
		(current && current.element) && current.element.removeClass('b-sidebar_item__current');


		element.addClass('b-sidebar_item__current');
		data.element = element;
		mi.currentTab(data);
	};

	mi.currentTab(mi.tabs[0]);
}
