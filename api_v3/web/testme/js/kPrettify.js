
/**
 * Helper Class takes different outputs as text and insert them with the right ident
 * @class kField
 */
var ERROR_ARGUMENT_TYPE = "Got invalid type as argument, expected: ";
var IDENT_TAB_SIZE = 4;
var XML_NODE_NAME = 'xml';
var HTML_SPACE_CHAR = '&nbsp;';


function errorMessage(expected, got)
{
    return ERROR_ARGUMENT_TYPE.concat(expected).concat(' ,Got: ').concat(got);
}


function prettify(format, represantation){
    var repType = typeof represantation;
    switch (format)
    {
        case 'JSON' :
            if ( repType  == 'string' || represantation instanceof String)
            {
                return prettifyJSON(represantation);
            } else {
                return errorMessage('String', repType)
            }
        case 'XML' :
        default :
            if ( represantation instanceof Document &&
                represantation.firstChild &&
                represantation.firstChild.nodeName == XML_NODE_NAME
                )
            {
                return prettifyXML(represantation);
            } else {
                return errorMessage('Object', repType)
            }
    }
}

function prettifyXML(domXML){

    var originalResult = domXML.firstChild.innerHTML;
    var header =  '<head><meta charset="UTF-8"><meta content="application/xhtml+xml"></head><?xml version="1.0" encoding="utf-8"?>';
    var restoredResult = '<xml>' + originalResult + '</xml>';
    var identedXML = identXML(restoredResult);
    var encodedXML = header + encodeXML(identedXML);
    return encodedXML;

}

function encodeXML(xml)
{
    var ans = xml.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/ /g, '&nbsp;').replace(/\n/g,'<br/>');
    ans = ans.replace(/&lt;/g,'<xmltag>&lt;').replace(/&gt;/g,'&gt;</xmltag>');
    ans = ans.replace(/&lt;/g,'<xmlsyn>&lt;</xmlsyn>').replace(/&gt;/g,'<xmlsyn>&gt;</xmlsyn>');
    return ans;
}

function identXML(xml) {
    var idented = '';
    var reg = /(>)(<)(\/*)/g;
    xml = xml.replace(reg, '$1\r\n$2$3');
    var pad = 0;
    var paddingAddision = spacesGen(IDENT_TAB_SIZE, " ");
    jQuery.each(xml.split('\r\n'), function(index, node) {
        var indent = 0;
        if (node.match( /.+<\/\w[^>]*>$/ )) {
            indent = 0;
        } else if (node.match( /^<\/\w/ )) {
            if (pad != 0) {
                pad -= 1;
            }
        } else if (node.match( /^<\w[^>]*[^\/]>.*$/ )) {
            indent = 1;
        } else {
            indent = 0;
        }

        var padding = '';
        for (var i = 0; i < pad; i++) {
            padding += paddingAddision;
        }

        idented += padding + node + '\r\n';
        pad += indent;
    });

    return idented;
}

function prettifyJSON(asText)
{
    var ans = identJSON(asText);
    ans = syntaxHighlightJSON(ans);
    ans = createFoldingTagsJson(ans);
    return ans;
}

function createToggleHeader(num)
{
    var ans = "<script>";
    for (var i=0; i<num; i++)
    {
        ans = ans.concat("$(document).ready(function(){$(\"bracket")
            .concat(i).concat("\").click(function(){$(\"fold")
            .concat(i).concat("\").toggle(\"slow\");$(\"replace")
            .concat(i).concat("\").toggle();});});");
    }
    return ans.concat("</script>");
}

function createFoldingTagsJson(jsonText)
{
    var ans = "";
    var index = [];
    var counter  = 0;
    for (var i=0; i< jsonText.length ;i++) {
        var char = jsonText.charAt(i);
        switch (char) {
            case "{":
            case "[":
                index.push(counter);
                ans = ans.concat('<bracket').concat(counter).concat(' style="cursor:pointer">')
                    .concat(char).concat('</bracket').concat(counter).concat('>')
                    .concat('<replace').concat(counter).concat(' style="display: none">...</replace').concat(counter).concat('>')
                    .concat('<fold').concat(counter).concat('>');
                counter++;
                break;
            case "}":
            case "]":
                var idx = index.pop();
                ans = ans.concat('</fold').concat(idx).concat('>')
                    .concat('<bracket').concat(idx).concat('>')
                    .concat(char).concat('</bracket').concat(idx).concat('>');
                break;
            default :
                ans = ans.concat(char);
        }
    }
    var head = createToggleHeader(counter);
    return head.concat(ans);
}

function syntaxHighlightJSON(json)
{

    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
        function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

function identJSON(json) {
    var jsonCopy = "" ;
    var spaces = 0;
    var inquotes = false;
    for (var i=0; i< json.length ;i++)
    {
        var char = json.charAt(i);
        switch (char)
        {
            case "{":
                jsonCopy = jsonCopy.concat("{").concat("<br>");
                spaces += IDENT_TAB_SIZE;
                jsonCopy = jsonCopy.concat(spaceGenHTML(spaces));
                break;
            case "[":
                if (i < json.length -1 && json.charAt(i+1) == "]")
                {
                    if (i < json.length -2 && json.charAt(i+2) == ",")
                    {
                        jsonCopy = jsonCopy.concat("[],");
                        i += 2;
                    } else {
                        jsonCopy = jsonCopy.concat("[]");
                        i += 1;
                    }
                } else {
                    jsonCopy = jsonCopy.concat("[")
                    spaces += IDENT_TAB_SIZE;
                }
                jsonCopy = jsonCopy.concat("<br>").concat(spaceGenHTML(spaces));
                break;
            case ",":
                if (inquotes)
                {//in case we are inside an array we do not jump a line
                    jsonCopy = jsonCopy.concat(char);
                } else {
                    jsonCopy = jsonCopy.concat(",").concat("<br>").concat(spaceGenHTML(spaces));
                }
                break;
            case "}":
                jsonCopy = jsonCopy.concat("<br>");
                spaces -= IDENT_TAB_SIZE;
                if (i < json.length -1 && json.charAt(i+1) == ",")
                {
                    jsonCopy = jsonCopy.concat(spaceGenHTML(spaces)).concat("},").concat("<br>");
                    i++;
                } else {
                    jsonCopy = jsonCopy.concat(spaceGenHTML(spaces)).concat("}").concat("<br>");
                }
                jsonCopy = jsonCopy.concat(spaceGenHTML(spaces));
                break;
            case "]":
                jsonCopy = jsonCopy.concat("<br>");
                spaces -= IDENT_TAB_SIZE;
                if (i < json.length -1 && json.charAt(i+1) == ",")
                {
                    jsonCopy = jsonCopy.concat(spaceGenHTML(spaces)).concat("],").concat("<br>");
                    i++;
                } else {
                    jsonCopy = jsonCopy.concat(spaceGenHTML(spaces)).concat("]").concat("<br>");
                }
                jsonCopy = jsonCopy.concat(spaceGenHTML(spaces));
                break;
            case "\\":
                break;
            case "\"":
                inquotes =  !inquotes;
                jsonCopy = jsonCopy.concat(char);
                break;
            default :
                jsonCopy = jsonCopy.concat(char);
        }
    }

    return jsonCopy;
}


function spaceGenHTML(num)
{
    return spacesGen(num, HTML_SPACE_CHAR);
}

function spacesGen(num, spaceChar)
{
    var spaces ="";
    for (var i=0; i<num ; i++)
    {
        spaces = spaces.concat(spaceChar);
    }
    return spaces;
}
