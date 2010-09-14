<script src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js"></script>
<form onsubmit="trigger_me();return false;">
    Partner Id: <input type="text" value="" name="partnerId" id="partnerId" /> 
    <input type="button" id="loadit" name="action" value="Load Partner" onclick="load_partner(this);" />
</form>
<div id="partnerDetails"></div>
<script>
function load_partner(obj)
{
    $.ajax({
        url: '/index.php/salestools/loadpartner',
        type: 'get',
        data: {
            partnerId: $("#partnerId").val(),
            action: 'loadPartner'
        },
        success: function(msg)
        {
            $("#partnerDetails").html(msg);
        },
        error: function(msg)
        {
            alert('could not load partner '+$("#partnerId").val());
        }
    });
    return false;
}
function trigger_me()
{
    obj = document.getElementById("loadit");
    load_partner(obj);
}
function update_partner()
{
    ppackage = $("#partnerPackage").val();
    monitor = $("#monitorUsage").val();
    jwlicense = $("#licenseJW").val();
    pid = $("#pid").html();
    $.ajax({
        url: '/index.php/salestools/updatepartner',
        type: 'get',
        async: false,
        data: {
            partnerId: pid,
            partnerPackage: ppackage,
            monitorUsage: monitor,
            licenseJW: jwlicense
        },
        success: function(msg){
            if(msg == 'ok')
            {
                $("#partnerId").val(pid);
                alert('changes saved !');
                trigger_me();
            }
            else
            {
                alert('something went wrong...');
            }
        }
    });
}
</script>
