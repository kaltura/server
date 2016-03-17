
function LogView(name, container, field, id, options) {
    this.name = name;
    this.container = container;
    this.field = field;
    this.id = id;
    this.options = options;
    this.nest = {};

    console.log('LogView[' + this.name + '] - construct: container [' + container.attr('id') + ']');
    
    if(options && options.table){
        this.table = options.table;
        this.table.empty();
        this.load();
    }
    else{
        this.init();
    }
}

LogView.prototype = {

    init: function(){
        var button = jQuery('<button>View Logs</button>');
        this.container.append(button);
        var This = this;
        button.click(function(){
            This.load();
        });
        
        this.table = jQuery('<table class="log-view" id="tbl-' + this.name + '"/>');
        this.container.append(this.table);

        console.log('LogView[' + this.name + '] - init: table [' + this.table.attr('id') + ']');
    },

    getUrl: function(){
        var url = logViewUrl;
        url += '/field/' + this.field;
        url += '/id/' + this.id;

        if(this.options && this.options.type){
        	url += '/type/' + this.options.type;
        }

        if(this.options && this.options.conditions){
        	for(var field in this.options.conditions)
        		url += '/' + field + '/' + this.options.conditions[field];
        }
        
        return url;
    },

    load: function(){
        var This = this;
        var pageIndex = 1;
        
        jQuery.ajax({
            url: This.getUrl(pageIndex),
            dataType: 'json',
            success: function(data) {
                console.log('LogView[' + This.name + '] - load.success: table [' + This.table.attr('id') + '], data [' + data.length + ']');
                for(var i = 0; i < data.length; i++){
                    This.add(data[i]);
                }
                toggleView();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Unable to find logs");
            }
        });
        
        console.log('LogView[' + this.name + '] - load: table [' + this.table.attr('id') + ']');
    },

    add: function(hit){
        var This = this;
        
        var message = hit._source.message.replace(/(\r\n|\n|\r)/g, '<br/>');
        message = message.replace(/(\t|\s{4})/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
        
        var sessionId = this.name + '-' + hit._type + '-' + hit._source.sessionId;
        var sessionTableId = 'tblLogView_' + sessionId;
        var sessionTitleId = 'ttlLogView_' + sessionId;
        var sessionRowId = 'trLogView_' + sessionId;
        
        var sessionTitle = jQuery('#' + sessionTitleId);
        var sessionTable = jQuery('#' + sessionTableId);
        
        if(!sessionTable.size()){
            console.log('LogView[' + this.name + '] - add.create: table [' + this.table.attr('id') + '] sessionId [' + sessionId + ']');            
            this.table.append('<tr><td class="title clickable" id="' + sessionTitleId + '">' + hit._type + '(' + sessionId + ')</td></tr>');
            this.table.append('<tr id="' + sessionRowId + '" style="display:none"><td><table class="session" id="' + sessionTableId + '"/></td></tr>');
            sessionTable = jQuery('#' + sessionTableId);
            sessionTitle = jQuery('#' + sessionTitleId);
            sessionTitle.click(function(){
                var sessionRow = jQuery('#' + sessionRowId);
                sessionRow.animate({opacity: 'toggle'}, 'slow'); 
                toggleView();
            });
        }
        else if(this.nest[sessionId] && this.nest[sessionId].length){
            console.log('LogView[' + this.name + '] - add.exists: table [' + this.table.attr('id') + '] sessionId [' + sessionId + ']');
            sessionTable = this.nest[sessionId][this.nest[sessionId].length - 1];
        }
        
        var classes = [];
        if(hit._source.level){
            classes.push(hit._source.level);
        }
        
        if(hit._source.title){
            sessionTitle.text(hit._source.title);
        }
        
        var related = false;
        if(hit._source.apiSession){
            related = hit._source.apiSession;
            message = message.replace(related, '<span class="related clickable" id="spn' + related + '">' + related + '</span>');
        }
        
        var indent = false;
        if(hit._source.tags && hit._source.tags.indexOf('consumerStart') >= 0){
            classes.push('event');
            classes.push('clickable');
            indent = '+';
        }
        else if(hit._source.tags && hit._source.tags.indexOf('consumerEnd') >= 0){
            indent = '-';
        }
        
        sessionTable.append('<tr><td id="td' + hit._id + '" class="' + classes.join(' ') + '">' + message + '</td></tr>');
        
        if(related){
            sessionTable.append('<tr id="trRelated' + related + '" style="display:none"><td class="nested"><table id="tblRelated' + related + '"/></td></tr>');
            var relatedLink = jQuery('#spn' + related);
            var relatedLoaded = false;
            relatedLink.click(function(){
                if(!relatedLoaded){
                    var relatedTable = jQuery('#tblRelated' + related);
                    new LogView('api-session-' + related, relatedTable, 'session', related, {
                        table: relatedTable,
                        type: 'api_v3'
                    });
                }
                relatedLoaded = true;
                var relatedRow = jQuery('#trRelated' + related);
                relatedRow.animate({opacity: 'toggle'}, 'slow'); 
                toggleView();
            });
        }
        
        if(indent == '+'){
            sessionTable.append('<tr id="tr' + hit._id + '" style="display:none"><td class="nested"><table id="tbl' + hit._id + '"/></td></tr>');
            if(!this.nest[sessionId]){
                this.nest[sessionId] = [];
            }
            nestedTable = jQuery('#tbl' + hit._id);
            this.nest[sessionId].push(nestedTable);
            
            var eventTitle = jQuery('#td' + hit._id);
            eventTitle.click(function(){
                var nestedRow = jQuery('#tr' + hit._id);
                nestedRow.animate({opacity: 'toggle'}, 'slow'); 
                toggleView();
            });
        }
        else if(indent == '-'){
            if(this.nest[sessionId]){
                this.nest[sessionId].pop();
            }
        }
    }
};

