/**
 * Helper Class takes different outputs as text and returns them with the right indentation
 */

var IDENT_TAB_SIZE = 4;
var HTML_SPACE_CHAR = '&nbsp;';

function indentXML(xml)
{
    var idented = '';
    var reg = /(>)(<)(\/*)/g;
    xml = xml.replace(reg, '$1\r\n$2$3');
    var pad = 0;
    var paddingAddision = spacesGen(IDENT_TAB_SIZE, " ");
    jQuery.each(xml.split('\r\n'), function(index, node)
    {
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

function indentJSON(json, newline, space) {
    var jsonCopy = "" ;
    var spaces = 0;
    var inquotes = false;
    for (var i=0; i< json.length ;i++)
    {
        var char = json.charAt(i);
        switch (char)
        {
            case "{":
                jsonCopy = jsonCopy.concat("{").concat(newline);
                spaces += IDENT_TAB_SIZE;
                jsonCopy = jsonCopy.concat(spacesGen(spaces,space));
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
                jsonCopy = jsonCopy.concat(newline).concat(spacesGen(spaces,space));
                break;
            case ",":
                if (inquotes)
                {//in case we are inside an array we do not jump a line
                    jsonCopy = jsonCopy.concat(char);
                } else {
                    jsonCopy = jsonCopy.concat(",").concat(newline).concat(spacesGen(spaces,space));
                }
                break;
            case "}":
                jsonCopy = jsonCopy.concat(newline);
                spaces -= IDENT_TAB_SIZE;
                if (i < json.length -1 && json.charAt(i+1) == ",")
                {
                    jsonCopy = jsonCopy.concat(spacesGen(spaces,space)).concat("},").concat(newline);
                    i++;
                } else {
                    jsonCopy = jsonCopy.concat(spacesGen(spaces,space)).concat("}").concat(newline);
                }
                jsonCopy = jsonCopy.concat(spacesGen(spaces,space));
                break;
            case "]":
                jsonCopy = jsonCopy.concat(newline);
                spaces -= IDENT_TAB_SIZE;
                if (i < json.length -1 && json.charAt(i+1) == ",")
                {
                    jsonCopy = jsonCopy.concat(spacesGen(spaces,space)).concat("],").concat(newline);
                    i++;
                } else {
                    jsonCopy = jsonCopy.concat(spacesGen(spaces,space)).concat("]").concat(newline);
                }
                jsonCopy = jsonCopy.concat(spacesGen(spaces,space));
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

function spacesGen(num, spaceChar)
{
    var spaces ="";
    for (var i=0; i<num ; i++)
    {
        spaces = spaces.concat(spaceChar);
    }
    return spaces;
}
