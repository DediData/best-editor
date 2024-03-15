(function () {

var defs = {}; // id -> {dependencies, definition, instance (possibly undefined)}

// Used when there is no 'main' module.
// The name is probably (hopefully) unique so minification removes for releases.
var register_3795 = function (id) {
  var module = dem(id);
  var fragments = id.split('.');
  var target = Function('return this;')();
  for (var i = 0; i < fragments.length - 1; ++i) {
    if (target[fragments[i]] === undefined)
      target[fragments[i]] = {};
    target = target[fragments[i]];
  }
  target[fragments[fragments.length - 1]] = module;
};

var instantiate = function (id) {
  var actual = defs[id];
  var dependencies = actual.deps;
  var definition = actual.defn;
  var len = dependencies.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances[i] = dem(dependencies[i]);
  var defResult = definition.apply(null, instances);
  if (defResult === undefined)
     throw 'module [' + id + '] returned undefined';
  actual.instance = defResult;
};

var def = function (id, dependencies, definition) {
  if (typeof id !== 'string')
    throw 'module id must be a string';
  else if (dependencies === undefined)
    throw 'no dependencies for ' + id;
  else if (definition === undefined)
    throw 'no definition function for ' + id;
  defs[id] = {
    deps: dependencies,
    defn: definition,
    instance: undefined
  };
};

var dem = function (id) {
  var actual = defs[id];
  if (actual === undefined)
    throw 'module [' + id + '] was undefined';
  else if (actual.instance === undefined)
    instantiate(id);
  return actual.instance;
};

var req = function (ids, callback) {
  var len = ids.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances.push(dem(ids[i]));
  callback.apply(null, callback);
};

var ephox = {};

ephox.bolt = {
  module: {
    api: {
      define: def,
      require: req,
      demand: dem
    }
  }
};

var define = def;
var require = req;
var demand = dem;
// this helps with minificiation when using a lot of global references
var defineGlobal = function (id, ref) {
  define(id, [], function () { return ref; });
};
/*jsc
["tinymce.plugins.bootstrap.Plugin","tinymce.core.PluginManager","tinymce.core.util.Tools","global!tinymce.util.Tools.resolve"]
jsc*/
defineGlobal("global!tinymce.util.Tools.resolve", tinymce.util.Tools.resolve);
/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.PluginManager',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.PluginManager');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.Tools',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Tools');
  }
);

/**************************************************************************/

/**
 * Plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains all core logic for the bootstrap plugin.
 *
 * @class tinymce.bootstrap.Plugin
 * @private
 */
define(
	'tinymce.plugins.bootstrap.Plugin',
	[
		'tinymce.core.PluginManager',
		'tinymce.core.util.Tools'
	],
	function (PluginManager, Tools) {
		PluginManager.add('bootstrap', function (editor) {
			var menuItems = [];

			// editor.getParam("insertdatetime_dateformat", editor.translate("%Y-%m-%d"))
			/*
			editor.addCommand('mceInsertBootstrapGrid', function () {
				function();
			});
			*/

			editor.addMenuItem('bootstrap', {
				icon: 'code',
				text: 'Bootstrap',
				title: 'Bootstrap Elements',
				tooltip: 'Bootstrap Elements',
				menu: menuItems,
				context: 'tools'
			});

			editor.addButton('bootstrap', {
				type: 'menubutton',
				icon: 'code',
				tooltip: 'Bootstrap Elements',
				title: 'Bootstrap Elements',
				text: 'Bootstrap',
				menu: menuItems
			});

			/* PREVENT DELETING ELEMENTS BY BACKSPACE */
			editor.on("keydown",function(e) {
                //prevent empty panels
                if (e.keyCode == 8 ) { //backspace and delete keycodes
                    try {
                        var elem = editor.selection.getNode(); //current caret node
                        //if (elem.classList.contains("panel-body") || elem.classList.contains("panel-heading")) {
							//console.log(elem.textContent.length);
                            if (elem.textContent.length <= 1) {
                                elem.textContent = String.fromCharCode(160);
                                return false;
                            }
                        //}
                    } catch (e) {}
                }
            });
			/* PREVENT DELETING ELEMENTS BY BACKSPACE */

			/* START MAKE GLYPHICONS NON EDITABLE */
			var setContentEditable = function (state) {
				return function (nodes) {
					for (var i = 0; i < nodes.length; i++) {
						var node = nodes[i];
						if (typeof  node.attr('class') !== 'undefined' && node.attr('class').includes('glyphicon')) { /* !node.firstChild */ // important
							node.attr('contenteditable', state);
						}
					}
				};
			};
			editor.on('PreInit', function () {
				editor.parser.addNodeFilter('span', setContentEditable('false'));
				editor.serializer.addNodeFilter('span', setContentEditable(null));
			});
			/* END MAKE GLYPHICONS NON EDITABLE */

			/******** START INSERT GRID **********/ 
			function insertGrid() {
				editor.windowManager.open({
					title: 'Insert Bootstrap Grid',
					body: [
				        {
							type: 'listbox',
							name: 'columnNo',
							label: 'Number of Columns',
							'values': [
								{text: '1', value: '1'},
								{text: '2', value: '2'},
								{text: '3', value: '3'},
								{text: '4', value: '4'},
								{text: '5', value: '5'},
								{text: '6', value: '6'},
								{text: '7', value: '7'},
								{text: '8', value: '8'},
								{text: '9', value: '9'},
								{text: '10', value: '10'},
								{text: '11', value: '11'},
								{text: '12', value: '12'},
							]
						},
				        {
							type: 'listbox',
							name: 'columnWidth',
							label: 'Column Width',
							'values': [
								{text: '1', value: '1'},
								{text: '2', value: '2'},
								{text: '3', value: '3'},
								{text: '4', value: '4'},
								{text: '5', value: '5'},
								{text: '6', value: '6'},
								{text: '7', value: '7'},
								{text: '8', value: '8'},
								{text: '9', value: '9'},
								{text: '10', value: '10'},
								{text: '11', value: '11'},
								{text: '12', value: '12'},
							]
						},
				        {
							type: 'listbox',
							name: 'responsiveMode',
							label: 'Responsive Mode',
							'values': [
								{text: 'Hide in Mobiles', value: ' '},
								{text: 'Each column: Full width for Mobiles (col-xs-12)', value: 'col-xs-12'},
								{text: 'Each column: Half width for Mobiles (col-xs-6)', value: 'col-xs-6'},
							]
						},
				        {
							type: 'listbox',
							name: 'blockAlign',
							label: 'Block Align',
							'values': [
								{text: 'Center', value: 'text-center'},
								{text: 'Left', value: 'text-left'},
								{text: 'Right', value: 'text-right'},
							]
						},
				        {
							type: 'listbox',
							name: 'contentAlign',
							label: 'Content Align',
							'values': [
								{text: 'Center', value: 'text-center'},
								{text: 'Left', value: 'text-left'},
								{text: 'Right', value: 'text-right'},
								{text: 'Justify', value: 'text-justify'},
							]
						},

						/*
						{
							type: 'textbox',
							name: 'id',
							size: 40,
							label: 'Id',
							value: 'a'
						},
						*/
					],
					onsubmit: function (e) {
						var columnNo = e.data.columnNo;
						if (!( columnNo >= 1 && columnNo <= 12 )) {
							e.preventDefault();
							editor.windowManager.alert('Number of columns are invalid');
							return;
						}

						html = '<div class="row ' + e.data.blockAlign + '">';
						for (i = 0; i < columnNo; i++) {
							html += '<div class="col-sm-' + e.data.columnWidth + ' ' + e.data.responsiveMode + ' ' + e.data.contentAlign + '" style="display: inline-block; float: none;">Content ' + (i+1) + '</div>';
						}
						html += '</div>';
						editor.insertContent(html);
					}
				});
			}

			menuItems.push({
				text: 'Insert Grid',
				icon: 'table',
				tooltip: 'Insert Bootstrap Grid',
				title: 'Insert Bootstrap Grid',
				onclick: function () {
					insertGrid();
				}
			});
			/******** END INSERT GRID **********/ 

			
			/******** START INSERT ALERT MESSAGE **********/ 
			function insertAlert() {
				editor.windowManager.open({
					title: 'Insert Alert Message',
					body: [
				        {
							type: 'listbox',
							name: 'alertType',
							label: 'Alert Type (Color)',
							'values': [
								{text: 'Success (Green)', value: 'success'},
								{text: 'Info (Blue)', value: 'info'},
								{text: 'Warning (Yellow)', value: 'warning'},
								{text: 'Danger (Red)', value: 'danger'},
							]
						},
				        {
							type: 'listbox',
							name: 'widthType',
							label: 'Width Type',
							'values': [
								{text: 'Full', value: ' '},
								{text: 'Fit Content', value: ' style="display: inline-block;"'},
							]
						},

						/*
						{
							type: 'textbox',
							name: 'id',
							size: 40,
							label: 'Id',
							value: 'a'
						},
						*/
					],
					onsubmit: function (e) {
						html = '<div class="alert alert-' + e.data.alertType + '" ' + e.data.widthType + ' role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <strong>Attention!</strong>Change This Text !</div>';
						editor.insertContent(html);
					}
				});
			}

			menuItems.push({
				text: 'Insert Alert Message',
				icon: 'template',
				tooltip: 'Insert Alert Message',
				title: 'Insert Alert Message',
				onclick: function () {
					insertAlert();
				}
			});
			/******** END INSERT ALERT MESSAGE **********/ 

			/******** START INSERT LINK BUTTON **********/ 
			function insertLinkButton() {
				editor.windowManager.open({
					title: 'Insert Link Button',
					body: [
				        {
							type: 'listbox',
							name: 'buttonType',
							label: 'Button Type (Color)',
							'values': [
								{text: 'Default (White)', value: 'default'},
								{text: 'Primary (Dark Blue)', value: 'primary'},
								{text: 'Success (Green)', value: 'success'},
								{text: 'Info (Blue)', value: 'info'},
								{text: 'Warning (Yellow)', value: 'warning'},
								{text: 'Danger (Red)', value: 'danger'},
							]
						},
				        {
							type: 'listbox',
							name: 'sizeType',
							label: 'Button Size',
							'values': [
								{text: 'Medium', value: 'md'},
								{text: 'Small', value: 'sm'},
								{text: 'Extra Small', value: 'xs'},
								{text: 'Large', value: 'lg'},
							]
						},
				        {
							type: 'listbox',
							name: 'blockType',
							label: 'Button Width',
							'values': [
								{text: 'Normal', value: ' '},
								{text: 'Block Mode', value: 'btn-block'},
							]
						},
						{
							type: 'textbox',
							name: 'url',
							size: 40,
							label: 'Link Address (URL)',
							value: 'http://esitedesign.com'
						},
						{
							type: 'textbox',
							name: 'linkText',
							size: 40,
							label: 'Link Text',
							value: 'Button Text!'
						},
					],
					onsubmit: function (e) {
						html = '<a class="btn btn-' + e.data.buttonType + ' btn-' + e.data.sizeType + '  ' + e.data.blockType + '" href="' + e.data.url + '">' + e.data.linkText + '</a>';
						editor.insertContent(html);
					}
				});
			}

			menuItems.push({
				text: 'Insert Link Button',
				icon: 'link',
				tooltip: 'Insert Link Button',
				title: 'Insert Link Button',
				onclick: function () {
					insertLinkButton();
				}
			});
			/******** END INSERT LINK BUTTON **********/ 

			/******** START INSERT COLORED TEXT **********/ 
			function insertColoredText() {
				editor.windowManager.open({
					title: 'Insert Colored Text',
					body: [
				        {
							type: 'listbox',
							name: 'colorType',
							label: 'Text Type (Color)',
							'values': [
								{text: 'Primary (Dark Blue)', value: 'primary'},
								{text: 'Success (Green)', value: 'success'},
								{text: 'Info (Blue)', value: 'info'},
								{text: 'Warning (Yellow)', value: 'warning'},
								{text: 'Danger (Red)', value: 'danger'},
								{text: 'Muted (Gray)', value: 'muted'},
							]
						},
					],
					onsubmit: function (e) {
						html = '<p class="text-' + e.data.colorType + '">Here is your text!</p>';
						editor.insertContent(html);
					}
				});
			}

			menuItems.push({
				text: 'Insert Colored Text',
				icon: 'forecolor',
				tooltip: 'Insert Colored Text',
				title: 'Insert Colored Text',
				onclick: function () {
					insertColoredText();
				}
			});
			/******** END INSERT COLORED TEXT **********/

			/******** START INSERT BACKGROUND COLORED TEXT **********/ 
			function insertBgColoredText() {
				editor.windowManager.open({
					title: 'Insert Background Colored Text',
					body: [
				        {
							type: 'listbox',
							name: 'colorType',
							label: 'Text Type (Color)',
							'values': [
								{text: 'Primary (Dark Blue)', value: 'primary'},
								{text: 'Success (Green)', value: 'success'},
								{text: 'Info (Blue)', value: 'info'},
								{text: 'Warning (Yellow)', value: 'warning'},
								{text: 'Danger (Red)', value: 'danger'},
							]
						},
				        {
							type: 'listbox',
							name: 'widthType',
							label: 'Width Type',
							'values': [
								{text: 'Full', value: ' '},
								{text: 'Fit Content', value: ' style="display: inline;"'},
								{text: 'Fit Content - Block', value: ' style="display: inline-block;"'},
							]
						},
					],
					onsubmit: function (e) {
						html = '<p class="bg-' + e.data.colorType + '" ' + e.data.widthType + '> Here is your text! </p>';
						editor.insertContent(html);
					}
				});
			}

			menuItems.push({
				text: 'Insert Background Colored Text',
				icon: 'backcolor',
				tooltip: 'Insert Background Colored Text',
				title: 'Insert Background Colored Text',
				onclick: function () {
					insertBgColoredText();
				}
			});
			/******** END INSERT BACKGROUND COLORED TEXT **********/

		});
		return function () { };
	}
);
dem('tinymce.plugins.bootstrap.Plugin')();
})();
