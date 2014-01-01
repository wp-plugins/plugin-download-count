(function() {
	tinymce.create('tinymce.plugins.PDC', {
		// Plugin initialisation
		init: function(ed, url) {
			// Add command to be fired by button
			ed.addCommand('tinyPDC', function() {
				tinymce.execCommand('mceReplaceContent', false, '[PDC]');
			});
			
			// Add button, hooking to command above
			ed.addButton('pdc', {
				title: 'wordpress.org Plugin Download Count Shortcode', 
				cmd: 'tinyPDC',
				image: url + '/../images/icons/small.png'
			});
		},
		
		// Plugin info
		getInfo: function() {
			return {
				longname: 'wordpress.org Plugin Download Count Shortcode',
				author: 'WP Cube',
				authorurl: 'http://www.wpcube.co.uk',
				infourl: 'http://www.wpcube.co.uk/plugins/plugin-download-count',
				version: '1.0'
			};
		}
	});
	
	// Add plugin created above
	tinymce.PluginManager.add('pdc', tinymce.plugins.PDC);
})();