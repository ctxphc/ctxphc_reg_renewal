(function() {  
        tinymce.create('tinymce.plugins.emaillink', {  
            init : function(ed, url) {  
                ed.addButton('emaillink', {  
                    title : 'Add a mailto link',
                    image : url+'/email.png',  
                    onclick : function() {
						ed.selection.setContent('[email]' + ed.selection.getContent() + '[/email]');
						ed.undoManager.add();
                    }  
                });  
            },  
            createControl : function(n, cm) {  
                return null;  
            },  
        });  
        tinymce.PluginManager.add('emaillink', tinymce.plugins.emaillink);  
    })();  
