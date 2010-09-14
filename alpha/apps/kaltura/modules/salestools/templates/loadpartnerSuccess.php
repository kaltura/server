<div>
    <table style="width:600px;">
        <tr>
            <td><b>Partner ID: </b></td>
            <td><span id="pid"><?php echo  $partner->getId(); ?></span></td>
        </tr>
        <tr>
            <td><b>Status: </b></td>
            <td>
                <?php
                if($partner->getStatus() == 1)
                    echo 'Normal';
                elseif($partner->getStatus() == 2)
                    echo 'Blocked';
                else
                    echo 'Deleted';
                ?>
            </td>
        </tr>
        <tr>
            <td><b>Name: </b></td>
            <td><?php echo  $partner->getPartnerName(); ?></td>
        </tr>
        <tr>
            <td><b>Admin Name: </b></td>
            <td><?php echo  $partner->getAdminName(); ?></td>
        </tr>        
        <tr>
            <td><b>Email: </b></td>
            <td><?php echo  $partner->getAdminEmail(); ?></td>
        </tr>
        <tr>
            <td><b>Description: </b></td>
            <td><?php echo  $partner->getDescription(); ?></td>
        </tr>
        <tr>
            <td><b>Phone: </b></td>
            <td><?php echo  $partner->getPhone(); ?></td>
        </tr>
        <tr>
            <td><b>Package: </b></td>
            <td>
                <select name="partnerPackage" id="partnerPackage">
                    <?php echo $packages_list; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><b>Monitor Usage: </b></td>
            <td>
                <select name="monitorUsage" id="monitorUsage">
                    <option value="0" <?php echo (!$partner->getMonitorUsage())? 'selected="selected"': ''; ?>>Skip monitoring</option>
                    <option value="1" <?php echo ($partner->getMonitorUsage())? 'selected="selected"': ''; ?>>Monitor partner</option>
                </select> (monitoring only applied on free partners)
            </td>
        </tr>
        <tr>
            <td><b>JW Player License Status: </b></td>
            <td>
                <select name="licenseJW" id="licenseJW">
                    <option value="0" <?php echo (!$partner->getLicensedJWPlayer())? 'selected="selected"': ''; ?>>None</option>
                    <option value="1" <?php echo ($partner->getLicensedJWPlayer())? 'selected="selected"': ''; ?>>Licensed</option>
                </select>
            </td>
        </tr>
    </table>
    <input type="submit" id="updatePartner" onclick="update_partner();return false;" value="save changes" />
</div>