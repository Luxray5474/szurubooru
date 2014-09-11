var App = App || {};
App.Presenters = App.Presenters || {};

App.Presenters.UserListPresenter = function(
	_,
	jQuery,
	util,
	promise,
	auth,
	router,
	pagedCollectionPresenter,
	topNavigationPresenter,
	messagePresenter) {

	var $el = jQuery('#content');
	var template;
	var userList = [];
	var activeSearchOrder;

	function init(args) {
		topNavigationPresenter.select('users');
		topNavigationPresenter.changeTitle('Users');

		promise.wait(util.promiseTemplate('user-list')).then(function(html) {
			template = _.template(html);
			initPaginator(args);
		});
	}

	function initPaginator(args) {
		var searchArgs = util.parseComplexRouteArgs(args.searchArgs);
		searchArgs.order = searchArgs.order || 'name';
		activeSearchOrder = searchArgs.order;

		pagedCollectionPresenter.init({
			page: searchArgs.page,
			order: searchArgs.order,
			baseUri: '#/users',
			backendUri: '/users',
			renderCallback: function updateCollection(data) {
				userList = data.entities;
				render();
			},
			failCallback: function(response) {
				$el.empty();
				messagePresenter.showError($el, response.json && response.json.error || response);
			}});
	}

	function render() {
		$el.html(template({
			userList: userList,
			formatRelativeTime: util.formatRelativeTime,
		}));
		$el.find('.order a').click(orderLinkClicked);
		$el.find('.order [data-order="' + activeSearchOrder + '"]').parent('li').addClass('active');

		var $pager = $el.find('.pager');
		pagedCollectionPresenter.render($pager);
	}

	function orderLinkClicked(e) {
		e.preventDefault();
		var $orderLink = jQuery(this);
		activeSearchOrder = $orderLink.attr('data-order');
		router.navigate(pagedCollectionPresenter.getSearchOrderChangeLink(activeSearchOrder));
	}

	return {
		init: init,
		reinit: initPaginator,
		render: render
	};

};

App.DI.register('userListPresenter', ['_', 'jQuery', 'util', 'promise', 'auth', 'router', 'pagedCollectionPresenter', 'topNavigationPresenter', 'messagePresenter'], App.Presenters.UserListPresenter);
