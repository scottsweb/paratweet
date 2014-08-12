(function() {
	tinymce.PluginManager.add('paratweet', function( editor, url ) {
		editor.addButton( 'paratweet', {
			icon: false,
			type: 'button',
			title: 'Tweetable Paragraph',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Tweetable Paragraph',
					body: [
						{
							type: 'textbox',
							name: 'hashtag',
							label: 'Hashtag (optional)',
							value: ''
						},
						{
							type: 'textbox',
							name: 'custom',
							label: 'Custom Tweet Text (optional)',
							value: '',
							multiline: true,
							minWidth: 300,
							minHeight: 100
						}
					],
					onsubmit: function( e ) {

						var content = tinyMCE.activeEditor.selection.getContent( { format : "text" } );
						var shortcode = '[tweetable'

						// custom tweet text?
						if (e.data.custom != '') {
							shortcode += ' alt="' + e.data.custom + '"';
						}

						// hashtag?
						if (e.data.hashtag != '') {
							shortcode += ' hashtag="' + e.data.hashtag + '"';
						}

						shortcode += ']';
						var closeshortcode = '[/tweetable]';
						editor.insertContent( shortcode + content + closeshortcode);
					}
				});
			}
		});
	});
})();