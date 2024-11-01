/**
 * WordPress plugin.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @version 1.3
 * @package ycwp-qr-me
 */

/**
 * tinyMCE plugin
 * Adds YCWP QR Me buttons to the editor.
 *
 * @author Nicola Mustone <mail@nicolamustone.it>
 * @copyright Copyright (c) 2012 Nicola Mustone
 * @version 1.3
 * @package ycwp-qr-me
 * @subpackage mce
 */
(function(tinymce) {
    tinymce.create( 'tinymce.plugins.YCWP_QR_Me', {
        init: function( ed, url ) {
            tinymce.plugins.YCWP_QR_Me.theurl = url;
        },        
        createControl: function( n, cm ) {
            switch (n) {
                case 'YCWP_QR_Me':
                    var c = cm.createSplitButton('YCWP_QR_Me', {
                        title   : 'YCWP QR Me',
                        image   : tinymce.plugins.YCWP_QR_Me.theurl + '/ycwp-qr-me.png',
                        onclick : function () {
                            var s = tinyMCE.activeEditor.selection;
                            
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme]' + s.getContent() + '[/qrme]' );
                            } else {
                                s.setContent( '[qrme content="" /]' );
                            }
                            
                            tinyMCE.activeEditor.undoManager.add();
                        }
                    });

                    c.onRenderMenu.add( function( c, m ) {
                        var ed = tinyMCE.activeEditor;
                        var s = tinyMCE.activeEditor.selection;
                        
                        m.add( { title : 'General types', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        m.add( { title : 'Simple', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme]' + s.getContent() + '[/qrme]' );
                            } else {
                                s.setContent( '[qrme content="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'URL', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_url]' + s.getContent() + '[/qrme_url]' );
                            } else {
                                s.setContent( '[qrme_url url="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'SMS', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_sms tel=""]' + s.getContent() + '[/qrme_sms]' );
                            } else {
                                s.setContent( '[qrme_sms tel="" message="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'Telephone', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_tel]' + s.getContent() + '[/qrme_tel]' );
                            } else {
                                s.setContent( '[qrme_tel tel="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'Geo Location', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_geoloc lat="" lon="" alt="" u="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'Email', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_email email="" subject=""]' + s.getContent() + '[/qrme_email]' );
                            } else {
                                s.setContent( '[qrme_email email="" subject="" body="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'Contact', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_contact name="" tel="" memo="" address="" url="" /]' );
                        } } );
                        m.add( { title : 'Android market', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_androidmarket]' + s.getContent() + '[/qrme_androidmarket]' );
                            } else {
                                s.setContent( '[qrme_androidmarket package="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'GitHub', onclick : function() {
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_github]' + s.getContent() + '[/qrme_github]' );
                            } else {
                                s.setContent( '[qrme_github path="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'View source', onclick : function() {
                            if( s.getContent != '' ) {
                                s.setContent( '[qrme_viewsource]' + s.getContent() + '[/qrme_viewsource]' );
                            } else {
                                s.setContent( '[qrme_viewsource uri="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'WiFi', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_wifi authtype="" ssid="" password="" /]' );
                            ed.undoManager.add();
                        } } );
                    } );

                    return c;
                    break;
                case 'YCWP_QR_Me_Twitter':
                    var c = cm.createSplitButton('YCWP_QR_Me_Twitter', {
                        title   : 'YCWP QR Me Twitter',
                        image   : tinymce.plugins.YCWP_QR_Me.theurl + '/ycwp-qr-me-twitter.png',
                        onclick : function () {}
                    });
                    
                    c.onRenderMenu.add( function( c, m ) {
                        var ed = tinyMCE.activeEditor;
                        var s = tinyMCE.activeEditor.selection;
                        
                        m.add( { title : 'Twitter types', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        
                        m.add( { title: 'User', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="user" screen_name="" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'Status', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="status" id="" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'Timeline', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="timeline" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'Mentions', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="mentions" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'Messages', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="messages" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'List', onclick : function() { 
                            s.setContent( s.getContent() + '[qrme_twitter type="list" screen_name="" slug="" /]' );
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'Post', onclick : function() { 
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_twitter type="post" replyto=""]' + s.getContent() + '[/qrme_twitter]' );
                            } else {
                                s.setContent( '[qrme_twitter type="post" replyto="" message="" /]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                        
                        m.add( { title: 'URL shortener', onclick : function() { 
                            if( s.getContent() != '' ) {
                                s.setContent( '[qrme_twitter type="urlshortener"]' + s.getContent() + '[/qrme_twitter]' );
                            } else {
                                s.setContent( '[qrme_twitter type="urlshortener"][/qrme_twitter]' );
                            }
                            
                            ed.undoManager.add();
                        } } );
                    } );
                    
                    return c;
                    break;
                case 'YCWP_QR_Me_Steam':
                    var c = cm.createSplitButton('YCWP_QR_Me_Steam', {
                        title   : 'YCWP QR Me Steam',
                        image   : tinymce.plugins.YCWP_QR_Me.theurl + '/ycwp-qr-me-steam.png',
                        onclick : function () {}
                    });
                    
                    c.onRenderMenu.add( function( c, m ) {
                        var ed = tinyMCE.activeEditor;
                        var s = tinyMCE.activeEditor.selection;
                        m.add( { title : 'Commands', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        m.add( { title : 'AddNonSteamGame', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="AddNonSteamGame" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'advertise', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="advertise" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'appnews', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="appnews" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'browsemedia', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="browsemedia" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'publisher', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="publisher" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'purchase', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="purchase" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'store', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="store" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'updatenews', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="updatenews" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'friends', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        m.add( { title : 'add', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="friends" subcommand="add" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'friends', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="friends" subcommand="friends" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'players', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="friends" subcommand="players" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'hardwarepromo', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        m.add( { title : 'ATi', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="hardwarepromo" subcommand="ATi" value="305" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'nVidia', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="hardwarepromo" subcommand="nVidia" value="609" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'url', 'class' : 'mceMenuItemTitle' } ).setDisabled( 1 );
                        m.add( { title : 'CommunitySearch', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="CommunitySearch" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'CommunityGroupSearch', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="CommunityGroupSearch" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'GroupEventsPage', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="GroupEventsPage" value="" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'LegalInformation', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="LegalInformation" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'PrivacyPolicy', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="PrivacyPolicy" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'Store', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="Store" /]' );
                            ed.undoManager.add();
                        } } );
                        m.add( { title : 'StoreFrontPage', onclick : function() {
                            s.setContent( s.getContent() + '[qrme_steam command="url" subcommand="StoreFrontPage" /]' );
                            ed.undoManager.add();
                        } } );
                    } );
                    
                    return c;
                    break;
            }

            return null;
        },
        getInfo : function() {
            return {
                longname    : 'YCWP QR Me buttons',
                author      : 'Nicola Mustone',
                authorurl   : 'http://www.nicolamustone.it',
                infourl     : 'http://wordpress.org/extend/plugins/ycwp-qr-me/',
                version     : '1.3'
            };
        }
    } );
    tinymce.PluginManager.add( 'YCWP_QR_Me', tinymce.plugins.YCWP_QR_Me );
} ) (tinymce);