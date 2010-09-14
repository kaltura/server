/*!
 * Ext JS Library 3.1.0
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * @class Ext.layout.ToolbarLayout
 * @extends Ext.layout.ContainerLayout
 * Layout manager implicitly used by Ext.Toolbar.
 */
Ext.layout.ToolbarLayout = Ext.extend(Ext.layout.ContainerLayout, {
    monitorResize : true,
    triggerWidth : 18,
    lastOverflow : false,
    forceLayout: true,

    noItemsMenuText : '<div class="x-toolbar-no-items">(None)</div>',
    // private
    onLayout : function(ct, target){
        if(!this.leftTr){
            var align = ct.buttonAlign == 'center' ? 'center' : 'left';
            target.addClass('x-toolbar-layout-ct');
            target.insertHtml('beforeEnd',
                 '<table cellspacing="0" class="x-toolbar-ct"><tbody><tr><td class="x-toolbar-left" align="' + align + '"><table cellspacing="0"><tbody><tr class="x-toolbar-left-row"></tr></tbody></table></td><td class="x-toolbar-right" align="right"><table cellspacing="0" class="x-toolbar-right-ct"><tbody><tr><td><table cellspacing="0"><tbody><tr class="x-toolbar-right-row"></tr></tbody></table></td><td><table cellspacing="0"><tbody><tr class="x-toolbar-extras-row"></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>');
            this.leftTr = target.child('tr.x-toolbar-left-row', true);
            this.rightTr = target.child('tr.x-toolbar-right-row', true);
            this.extrasTr = target.child('tr.x-toolbar-extras-row', true);
        }
        var side = ct.buttonAlign == 'right' ? this.rightTr : this.leftTr,
            pos = 0,
            items = ct.items.items;

        for(var i = 0, len = items.length, c; i < len; i++, pos++) {
            c = items[i];
            if(c.isFill){
                side = this.rightTr;
                pos = -1;
            }else if(!c.rendered){
                c.render(this.insertCell(c, side, pos));
            }else{
                if(!c.xtbHidden && !this.isValidParent(c, side.childNodes[pos])){
                    var td = this.insertCell(c, side, pos);
                    td.appendChild(c.getPositionEl().dom);
                    c.container = Ext.get(td);
                }
            }
        }
        //strip extra empty cells
        this.cleanup(this.leftTr);
        this.cleanup(this.rightTr);
        this.cleanup(this.extrasTr);
        this.fitToSize(target);
    },

    cleanup : function(row){
        var cn = row.childNodes;
        for(var i = cn.length-1, c; i >= 0 && (c = cn[i]); i--){
            if(!c.firstChild){
                row.removeChild(c);
            }
        }
    },

    insertCell : function(c, side, pos){
        var td = document.createElement('td');
        td.className='x-toolbar-cell';
        side.insertBefore(td, side.childNodes[pos]||null);
        return td;
    },

    hideItem : function(item){
        var h = (this.hiddens = this.hiddens || []);
        h.push(item);
        item.xtbHidden = true;
        item.xtbWidth = item.getPositionEl().dom.parentNode.offsetWidth;
        item.hide();
    },

    unhideItem : function(item){
        item.show();
        item.xtbHidden = false;
        this.hiddens.remove(item);
        if(this.hiddens.length < 1){
            delete this.hiddens;
        }
    },

    getItemWidth : function(c){
        return c.hidden ? (c.xtbWidth || 0) : c.getPositionEl().dom.parentNode.offsetWidth;
    },

    fitToSize : function(t){
        if(this.container.enableOverflow === false){
            return;
        }
        var w = t.dom.clientWidth,
            lw = this.lastWidth || 0,
            iw = t.dom.firstChild.offsetWidth,
            clipWidth = w - this.triggerWidth,
            hideIndex = -1;

        this.lastWidth = w;

        if(iw > w || (this.hiddens && w >= lw)){
            var i, items = this.container.items.items,
                len = items.length, c,
                loopWidth = 0;

            for(i = 0; i < len; i++) {
                c = items[i];
                if(!c.isFill){
                    loopWidth += this.getItemWidth(c);
                    if(loopWidth > clipWidth){
                        if(!(c.hidden || c.xtbHidden)){
                            this.hideItem(c);
                        }
                    }else if(c.xtbHidden){
                        this.unhideItem(c);
                    }
                }
            }
        }
        if(this.hiddens){
            this.initMore();
            if(!this.lastOverflow){
                this.container.fireEvent('overflowchange', this.container, true);
                this.lastOverflow = true;
            }
        }else if(this.more){
            this.clearMenu();
            this.more.destroy();
            delete this.more;
            if(this.lastOverflow){
                this.container.fireEvent('overflowchange', this.container, false);
                this.lastOverflow = false;
            }
        }
    },

    createMenuConfig : function(c, hideOnClick){
        var cfg = Ext.apply({}, c.initialConfig),
            group = c.toggleGroup;

        Ext.apply(cfg, {
            text: c.overflowText || c.text,
            iconCls: c.iconCls,
            icon: c.icon,
            itemId: c.itemId,
            disabled: c.disabled,
            handler: c.handler,
            scope: c.scope,
            menu: c.menu,
            hideOnClick: hideOnClick
        });
        if(group || c.enableToggle){
            Ext.apply(cfg, {
                group: group,
                checked: c.pressed,
                listeners: {
                    checkchange: function(item, checked){
                        c.toggle(checked);
                    }
                }
            });
        }
        delete cfg.ownerCt;
        delete cfg.xtype;
        delete cfg.id;
        return cfg;
    },

    // private
    addComponentToMenu : function(m, c){
        if(c instanceof Ext.Toolbar.Separator){
            m.add('-');
        }else if(Ext.isFunction(c.isXType)){
            if(c.isXType('splitbutton')){
                m.add(this.createMenuConfig(c, true));
            }else if(c.isXType('button')){
                m.add(this.createMenuConfig(c, !c.menu));
            }else if(c.isXType('buttongroup')){
                c.items.each(function(item){
                     this.addComponentToMenu(m, item);
                }, this);
            }
        }
    },

    clearMenu : function(){
        var m = this.moreMenu;
        if(m && m.items){
            m.items.each(function(item){
                delete item.menu;
            });
        }
    },

    // private
    beforeMoreShow : function(m){
        var h = this.container.items.items,
            len = h.length,
            c,
            prev,
            needsSep = function(group, item){
                return group.isXType('buttongroup') && !(item instanceof Ext.Toolbar.Separator);
            };

        this.clearMenu();
        m.removeAll();
        for(var i = 0; i < len; i++){
            c = h[i];
            if(c.xtbHidden){
                if(prev && (needsSep(c, prev) || needsSep(prev, c))){
                    m.add('-');
                }
                this.addComponentToMenu(m, c);
                prev = c;
            }
        }
        // put something so the menu isn't empty
        // if no compatible items found
        if(m.items.length < 1){
            m.add(this.noItemsMenuText);
        }
    },

    initMore : function(){
        if(!this.more){
            this.moreMenu = new Ext.menu.Menu({
                listeners: {
                    beforeshow: this.beforeMoreShow,
                    scope: this
                }
            });
            this.moreMenu.ownerCt = this.container;
            this.more = new Ext.Button({
                iconCls: 'x-toolbar-more-icon',
                cls: 'x-toolbar-more',
                menu: this.moreMenu
            });
            var td = this.insertCell(this.more, this.extrasTr, 100);
            this.more.render(td);
        }
    },

    onRemove : function(c){
        delete this.leftTr;
        delete this.rightTr;
        delete this.extrasTr;
        Ext.layout.ToolbarLayout.superclass.onRemove.call(this, c);
    },

    destroy : function(){
        Ext.destroy(this.more, this.moreMenu);
        delete this.leftTr;
        delete this.rightTr;
        delete this.extrasTr;
        Ext.layout.ToolbarLayout.superclass.destroy.call(this);
    }
    /**
     * @property activeItem
     * @hide
     */
});

Ext.Container.LAYOUTS.toolbar = Ext.layout.ToolbarLayout;

/**
 * @class Ext.Toolbar
 * @extends Ext.Container
 * <p>Basic Toolbar class. Although the <tt>{@link Ext.Container#defaultType defaultType}</tt> for Toolbar
 * is <tt>{@link Ext.Button button}</tt>, Toolbar elements (child items for the Toolbar container) may
 * be virtually any type of Component. Toolbar elements can be created explicitly via their constructors,
 * or implicitly via their xtypes, and can be <tt>{@link #add}</tt>ed dynamically.</p>
 * <p>Some items have shortcut strings for creation:</p>
 * <pre>
<u>Shortcut</u>  <u>xtype</u>          <u>Class</u>                  <u>Description</u>
'->'      'tbfill'       {@link Ext.Toolbar.Fill}       begin using the right-justified button container
'-'       'tbseparator'  {@link Ext.Toolbar.Separator}  add a vertical separator bar between toolbar items
' '       'tbspacer'     {@link Ext.Toolbar.Spacer}     add horiztonal space between elements
 * </pre>
 *
 * Example usage of various elements:
 * <pre><code>
var tb = new Ext.Toolbar({
    renderTo: document.body,
    width: 600,
    height: 100,
    items: [
        {
            // xtype: 'button', // default for Toolbars, same as 'tbbutton'
            text: 'Button'
        },
        {
            xtype: 'splitbutton', // same as 'tbsplitbutton'
            text: 'Split Button'
        },
        // begin using the right-justified button container
        '->', // same as {xtype: 'tbfill'}, // Ext.Toolbar.Fill
        {
            xtype: 'textfield',
            name: 'field1',
            emptyText: 'enter search term'
        },
        // add a vertical separator bar between toolbar items
        '-', // same as {xtype: 'tbseparator'} to create Ext.Toolbar.Separator
        'text 1', // same as {xtype: 'tbtext', text: 'text1'} to create Ext.Toolbar.TextItem
        {xtype: 'tbspacer'},// same as ' ' to create Ext.Toolbar.Spacer
        'text 2',
        {xtype: 'tbspacer', width: 50}, // add a 50px space
        'text 3'
    ]
});
 * </code></pre>
 * Example adding a ComboBox within a menu of a button:
 * <pre><code>
// ComboBox creation
var combo = new Ext.form.ComboBox({
    store: new Ext.data.ArrayStore({
        autoDestroy: true,
        fields: ['initials', 'fullname'],
        data : [
            ['FF', 'Fred Flintstone'],
            ['BR', 'Barney Rubble']
        ]
    }),
    displayField: 'fullname',
    typeAhead: true,
    mode: 'local',
    forceSelection: true,
    triggerAction: 'all',
    emptyText: 'Select a name...',
    selectOnFocus: true,
    width: 135,
    getListParent: function() {
        return this.el.up('.x-menu');
    },
    iconCls: 'no-icon' //use iconCls if placing within menu to shift to right side of menu
});

// put ComboBox in a Menu
var menu = new Ext.menu.Menu({
    id: 'mainMenu',
    items: [
        combo // A Field in a Menu
    ]
});

// add a Button with the menu
tb.add({
        text:'Button w/ Menu',
        menu: menu  // assign menu by instance
    });
tb.doLayout();
 * </code></pre>
 * @constructor
 * Creates a new Toolbar
 * @param {Object/Array} config A config object or an array of buttons to <tt>{@link #add}</tt>
 * @xtype toolbar
 */
Ext.Toolbar = function(config){
    if(Ext.isArray(config)){
        config = {items: config, layout: 'toolbar'};
    } else {
        config = Ext.apply({
            layout: 'toolbar'
        }, config);
        if(config.buttons) {
            config.items = config.buttons;
        }
    }
    Ext.Toolbar.superclass.constructor.call(this, config);
};

(function(){

var T = Ext.Toolbar;

Ext.extend(T, Ext.Container, {

    defaultType: 'button',

    /**
     * @cfg {String/Object} layout
     * This class assigns a default layout (<code>layout:'<b>toolbar</b>'</code>).
     * Developers <i>may</i> override this configuration option if another layout
     * is required (the constructor must be passed a configuration object in this
     * case instead of an array).
     * See {@link Ext.Container#layout} for additional information.
     */
    /**
     * @cfg {Boolean} enableOverflow
     * Defaults to false. Configure <code>true<code> to make the toolbar provide a button
     * which activates a dropdown Menu to show items which overflow the Toolbar's width.
     */

    trackMenus : true,
    internalDefaults: {removeMode: 'container', hideParent: true},
    toolbarCls: 'x-toolbar',

    initComponent : function(){
        T.superclass.initComponent.call(this);

        /**
         * @event overflowchange
         * Fires after the overflow state has changed.
         * @param {Object} c The Container
         * @param {Boolean} lastOverflow overflow state
         */
        this.addEvents('overflowchange');
    },

    // private
    onRender : function(ct, position){
        if(!this.el){
            if(!this.autoCreate){
                this.autoCreate = {
                    cls: this.toolbarCls + ' x-small-editor'
                };
            }
            this.el = ct.createChild(Ext.apply({ id: this.id },this.autoCreate), position);
            Ext.Toolbar.superclass.onRender.apply(this, arguments);
        }
    },

    /**
     * <p>Adds element(s) to the toolbar -- this function takes a variable number of
     * arguments of mixed type and adds them to the toolbar.</p>
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Mixed} arg1 The following types of arguments are all valid:<br />
     * <ul>
     * <li>{@link Ext.Button} config: A valid button config object (equivalent to {@link #addButton})</li>
     * <li>HtmlElement: Any standard HTML element (equivalent to {@link #addElement})</li>
     * <li>Field: Any form field (equivalent to {@link #addField})</li>
     * <li>Item: Any subclass of {@link Ext.Toolbar.Item} (equivalent to {@link #addItem})</li>
     * <li>String: Any generic string (gets wrapped in a {@link Ext.Toolbar.TextItem}, equivalent to {@link #addText}).
     * Note that there are a few special strings that are treated differently as explained next.</li>
     * <li>'-': Creates a separator element (equivalent to {@link #addSeparator})</li>
     * <li>' ': Creates a spacer element (equivalent to {@link #addSpacer})</li>
     * <li>'->': Creates a fill element (equivalent to {@link #addFill})</li>
     * </ul>
     * @param {Mixed} arg2
     * @param {Mixed} etc.
     * @method add
     */

    // private
    lookupComponent : function(c){
        if(Ext.isString(c)){
            if(c == '-'){
                c = new T.Separator();
            }else if(c == ' '){
                c = new T.Spacer();
            }else if(c == '->'){
                c = new T.Fill();
            }else{
                c = new T.TextItem(c);
            }
            this.applyDefaults(c);
        }else{
            if(c.isFormField || c.render){ // some kind of form field, some kind of Toolbar.Item
                c = this.createComponent(c);
            }else if(c.tag){ // DomHelper spec
                c = new T.Item({autoEl: c});
            }else if(c.tagName){ // element
                c = new T.Item({el:c});
            }else if(Ext.isObject(c)){ // must be button config?
                c = c.xtype ? this.createComponent(c) : this.constructButton(c);
            }
        }
        return c;
    },

    // private
    applyDefaults : function(c){
        if(!Ext.isString(c)){
            c = Ext.Toolbar.superclass.applyDefaults.call(this, c);
            var d = this.internalDefaults;
            if(c.events){
                Ext.applyIf(c.initialConfig, d);
                Ext.apply(c, d);
            }else{
                Ext.applyIf(c, d);
            }
        }
        return c;
    },

    /**
     * Adds a separator
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @return {Ext.Toolbar.Item} The separator {@link Ext.Toolbar.Item item}
     */
    addSeparator : function(){
        return this.add(new T.Separator());
    },

    /**
     * Adds a spacer element
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @return {Ext.Toolbar.Spacer} The spacer item
     */
    addSpacer : function(){
        return this.add(new T.Spacer());
    },

    /**
     * Forces subsequent additions into the float:right toolbar
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     */
    addFill : function(){
        this.add(new T.Fill());
    },

    /**
     * Adds any standard HTML element to the toolbar
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Mixed} el The element or id of the element to add
     * @return {Ext.Toolbar.Item} The element's item
     */
    addElement : function(el){
        return this.addItem(new T.Item({el:el}));
    },

    /**
     * Adds any Toolbar.Item or subclass
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Ext.Toolbar.Item} item
     * @return {Ext.Toolbar.Item} The item
     */
    addItem : function(item){
        return this.add.apply(this, arguments);
    },

    /**
     * Adds a button (or buttons). See {@link Ext.Button} for more info on the config.
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Object/Array} config A button config or array of configs
     * @return {Ext.Button/Array}
     */
    addButton : function(config){
        if(Ext.isArray(config)){
            var buttons = [];
            for(var i = 0, len = config.length; i < len; i++) {
                buttons.push(this.addButton(config[i]));
            }
            return buttons;
        }
        return this.add(this.constructButton(config));
    },

    /**
     * Adds text to the toolbar
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {String} text The text to add
     * @return {Ext.Toolbar.Item} The element's item
     */
    addText : function(text){
        return this.addItem(new T.TextItem(text));
    },

    /**
     * Adds a new element to the toolbar from the passed {@link Ext.DomHelper} config
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Object} config
     * @return {Ext.Toolbar.Item} The element's item
     */
    addDom : function(config){
        return this.add(new T.Item({autoEl: config}));
    },

    /**
     * Adds a dynamically rendered Ext.form field (TextField, ComboBox, etc). Note: the field should not have
     * been rendered yet. For a field that has already been rendered, use {@link #addElement}.
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Ext.form.Field} field
     * @return {Ext.Toolbar.Item}
     */
    addField : function(field){
        return this.add(field);
    },

    /**
     * Inserts any {@link Ext.Toolbar.Item}/{@link Ext.Button} at the specified index.
     * <br><p><b>Note</b>: See the notes within {@link Ext.Container#add}.</p>
     * @param {Number} index The index where the item is to be inserted
     * @param {Object/Ext.Toolbar.Item/Ext.Button/Array} item The button, or button config object to be
     * inserted, or an array of buttons/configs.
     * @return {Ext.Button/Item}
     */
    insertButton : function(index, item){
        if(Ext.isArray(item)){
            var buttons = [];
            for(var i = 0, len = item.length; i < len; i++) {
               buttons.push(this.insertButton(index + i, item[i]));
            }
            return buttons;
        }
        return Ext.Toolbar.superclass.insert.call(this, index, item);
    },

    // private
    trackMenu : function(item, remove){
        if(this.trackMenus && item.menu){
            var method = remove ? 'mun' : 'mon';
            this[method](item, 'menutriggerover', this.onButtonTriggerOver, this);
            this[method](item, 'menushow', this.onButtonMenuShow, this);
            this[method](item, 'menuhide', this.onButtonMenuHide, this);
        }
    },

    // private
    constructButton : function(item){
        var b = item.events ? item : this.createComponent(item, item.split ? 'splitbutton' : this.defaultType);
        return b;
    },

    // private
    onAdd : function(c){
        Ext.Toolbar.superclass.onAdd.call(this);
        this.trackMenu(c);
    },

    // private
    onRemove : function(c){
        Ext.Toolbar.superclass.onRemove.call(this);
        this.trackMenu(c, true);
    },

    // private
    onDisable : function(){
        this.items.each(function(item){
             if(item.disable){
                 item.disable();
             }
        });
    },

    // private
    onEnable : function(){
        this.items.each(function(item){
             if(item.enable){
                 item.enable();
             }
        });
    },

    // private
    onButtonTriggerOver : function(btn){
        if(this.activeMenuBtn && this.activeMenuBtn != btn){
            this.activeMenuBtn.hideMenu();
            btn.showMenu();
            this.activeMenuBtn = btn;
        }
    },

    // private
    onButtonMenuShow : function(btn){
        this.activeMenuBtn = btn;
    },

    // private
    onButtonMenuHide : function(btn){
        delete this.activeMenuBtn;
    }
});
Ext.reg('toolbar', Ext.Toolbar);

/**
 * @class Ext.Toolbar.Item
 * @extends Ext.BoxComponent
 * The base class that other non-interacting Toolbar Item classes should extend in order to
 * get some basic common toolbar item functionality.
 * @constructor
 * Creates a new Item
 * @param {HTMLElement} el
 * @xtype tbitem
 */
T.Item = Ext.extend(Ext.BoxComponent, {
    hideParent: true, //  Hiding a Toolbar.Item hides its containing TD
    enable:Ext.emptyFn,
    disable:Ext.emptyFn,
    focus:Ext.emptyFn
    /**
     * @cfg {String} overflowText Text to be used for the menu if the item is overflowed.
     */
});
Ext.reg('tbitem', T.Item);

/**
 * @class Ext.Toolbar.Separator
 * @extends Ext.Toolbar.Item
 * A simple class that adds a vertical separator bar between toolbar items
 * (css class:<tt>'xtb-sep'</tt>). Example usage:
 * <pre><code>
new Ext.Panel({
    tbar : [
        'Item 1',
        {xtype: 'tbseparator'}, // or '-'
        'Item 2'
    ]
});
</code></pre>
 * @constructor
 * Creates a new Separator
 * @xtype tbseparator
 */
T.Separator = Ext.extend(T.Item, {
    onRender : function(ct, position){
        this.el = ct.createChild({tag:'span', cls:'xtb-sep'}, position);
    }
});
Ext.reg('tbseparator', T.Separator);

/**
 * @class Ext.Toolbar.Spacer
 * @extends Ext.Toolbar.Item
 * A simple element that adds extra horizontal space between items in a toolbar.
 * By default a 2px wide space is added via css specification:<pre><code>
.x-toolbar .xtb-spacer {
    width:2px;
}
 * </code></pre>
 * <p>Example usage:</p>
 * <pre><code>
new Ext.Panel({
    tbar : [
        'Item 1',
        {xtype: 'tbspacer'}, // or ' '
        'Item 2',
        // space width is also configurable via javascript
        {xtype: 'tbspacer', width: 50}, // add a 50px space
        'Item 3'
    ]
});
</code></pre>
 * @constructor
 * Creates a new Spacer
 * @xtype tbspacer
 */
T.Spacer = Ext.extend(T.Item, {
    /**
     * @cfg {Number} width
     * The width of the spacer in pixels (defaults to 2px via css style <tt>.x-toolbar .xtb-spacer</tt>).
     */

    onRender : function(ct, position){
        this.el = ct.createChild({tag:'div', cls:'xtb-spacer', style: this.width?'width:'+this.width+'px':''}, position);
    }
});
Ext.reg('tbspacer', T.Spacer);

/**
 * @class Ext.Toolbar.Fill
 * @extends Ext.Toolbar.Spacer
 * A non-rendering placeholder item which instructs the Toolbar's Layout to begin using
 * the right-justified button container.
 * <pre><code>
new Ext.Panel({
    tbar : [
        'Item 1',
        {xtype: 'tbfill'}, // or '->'
        'Item 2'
    ]
});
</code></pre>
 * @constructor
 * Creates a new Fill
 * @xtype tbfill
 */
T.Fill = Ext.extend(T.Item, {
    // private
    render : Ext.emptyFn,
    isFill : true
});
Ext.reg('tbfill', T.Fill);

/**
 * @class Ext.Toolbar.TextItem
 * @extends Ext.Toolbar.Item
 * A simple class that renders text directly into a toolbar
 * (with css class:<tt>'xtb-text'</tt>). Example usage:
 * <pre><code>
new Ext.Panel({
    tbar : [
        {xtype: 'tbtext', text: 'Item 1'} // or simply 'Item 1'
    ]
});
</code></pre>
 * @constructor
 * Creates a new TextItem
 * @param {String/Object} text A text string, or a config object containing a <tt>text</tt> property
 * @xtype tbtext
 */
T.TextItem = Ext.extend(T.Item, {
    /**
     * @cfg {String} text The text to be used as innerHTML (html tags are accepted)
     */

    constructor: function(config){
        T.TextItem.superclass.constructor.call(this, Ext.isString(config) ? {text: config} : config);
    },

    // private
    onRender : function(ct, position) {
        this.autoEl = {cls: 'xtb-text', html: this.text || ''};
        T.TextItem.superclass.onRender.call(this, ct, position);
    },

    /**
     * Updates this item's text, setting the text to be used as innerHTML.
     * @param {String} t The text to display (html accepted).
     */
    setText : function(t) {
        if(this.rendered){
            this.el.update(t);
        }else{
            this.text = t;
        }
    }
});
Ext.reg('tbtext', T.TextItem);

// backwards compat
T.Button = Ext.extend(Ext.Button, {});
T.SplitButton = Ext.extend(Ext.SplitButton, {});
Ext.reg('tbbutton', T.Button);
Ext.reg('tbsplit', T.SplitButton);

})();
