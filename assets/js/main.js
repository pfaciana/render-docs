(function ($, window, document) {
	$(function () {
		var sectionId = window.sectionId, query = null;

		function getUrl(path, refs) {
			var nameIndex = path.lastIndexOf('\\');
			var ns = path.substr(0, nameIndex);
			ns = (ns = ns.split('\\').filter(function (x) {
				return x;
			}).join('-')) ? ns : 'global';
			var parts = path.substr(nameIndex + 1).split('::');
			var name = parts[0];
			var attr = parts.length > 1 ? parts[1] : null;
			var url = window.docUrl + '/' + ns + '/' + refs[0] + '/' + name;
			attr && refs.length > 1 && (url += '/' + refs[1] + '/' + attr);
			return url;
		}

		function getWrappingText(text) {
			return text.replace(/\\/g, '&ZeroWidthSpace;\\').replace(/::/g, '&ZeroWidthSpace;::');
		}

		$('.select2-ajax-data').select2({
			width: '100%',
			ajax: {
				delay: 100,
				url: window.relPath + '/ajax',
				dataType: 'json',
				data: function (params) {
					query = params.term;
					return {q: query, sectionId: sectionId};
				}, processResults: function (data) {
					data.results.unshift({id: 'search', text: 'View all results &raquo;'});
					return data;
				},
			},
			minimumInputLength: 3,
			templateResult: function (data) {
				if (typeof data == 'object' && data.loading) {
					return data.text;
				}
				return $('<div>' +
					getWrappingText(data.text) +
					(data.desc ? '<br><small>' + data.desc + '</small>' : '') +
					'</div>');
			},
		}).on('select2:select', function (e) {
			var url, data = e.params.data;
			if (data.id === 'search') {
				url = window.relPath + '/search' + (sectionId ? '/' + sectionId : '') + '/?q=' + query;
			} else {
				url = sectionId ? getUrl(data.id, data.refs) : window.docsUrl + '/' + data.id;
			}
			window.location.replace(url);
		});

		$('[data-editor-url]').on('click', function () {
			var w = window.open($(this).attr('href'));
			setTimeout(function () {
				w.close();
			}, 1);
			return false;
		});

		var openState = false;

		$('.show-hide-labels .controls a').on('click', function () {
			var $this = $(this);
			openState = !openState;
			$this.text(openState ? '[x]' : '[?]');
			$('.show-hide-labels ul').toggle(openState);
			return false;
		});

		$('.show-hide-labels ul a').on('click', function () {
			var $this = $(this);
			var showState = $this.hasClass('strikethrough');
			$this.toggleClass('strikethrough');
			var key = $this.data('key');
			var value = $this.data('value');

			if (value) {
				$('.' + key + ':contains("' + value + '")', '.methodsynopsis.dc-description, .classsynopsis').parent().toggle();
			} else {
				$('.' + key, '.methodsynopsis.dc-description, .classsynopsis').toggle();
			}
		});

		var synopsisState = true;
		var $synopsis = $('.methodsynopsis.dc-description, .classsynopsis');

		if ($synopsis.length) {
			$('.show-hide-labels').toggle();
			$synopsis.on('click', function (e) {
				if (e.ctrlKey) {
					synopsisState = !synopsisState;
					$.each(['modifier', 'type', 'initializer'], function (i, item) {
						var $item = $('[data-key="' + item + '"]:not([data-value])');
						if ((synopsisState && $item.hasClass('strikethrough')) //
							|| (!synopsisState && !$item.hasClass('strikethrough'))) {
							$item.click();
						}
					});
				}
			});
		}

		if ($('.classsynopsis').length) {
			$('.has-classsynopsis').toggle();
			$('[data-key="modifier"][data-value="protected"], [data-key="modifier"][data-value="private"]').click();
		}

	});
}(jQuery, window, document));