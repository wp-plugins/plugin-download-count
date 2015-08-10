(function() {
	tinymce.PluginManager.add('pdc', function(editor,url) {
		// Add Command
		editor.addCommand('tinyPDC', function() {
			console.log('CMD');
		}); 
			
		// Add Button to Visual Editor Toolbar
		editor.addButton('pdc', {
			title: 'wordpress.org Plugin and Theme Download Count Shortcode',
			icon: 'icon dashicons-marker',
			onclick: function() {
				editor.insertContent('[PDC]');	
			}
		});
	});
})();