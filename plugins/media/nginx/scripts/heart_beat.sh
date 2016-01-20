#!/bin/bash

. /etc/kaltura.d/system.ini

function checkAccess()
{
	#check if script is already running
	LOCKFILE=/var/lock/`basename $0`.lock

	if [ -f $LOCKFILE ] ; then
	  errLog "$LOCKFILE is locked by somebody else. leaving"
	  exit 0
	fi

	# Upon exit, remove lockfile.

	onExit()
	{
	  rm -f $LOCKFILE ;
	  exit 255;
	}

	trap onExit EXIT

	touch $LOCKFILE
}


function validateInputParameters()
{
	HOSTNAME=`hostname`

	if [ -z "$HOSTNAME" ]; then
		echo "Mandatory HOSTNAME is not set."
		exit 1
	fi

	if [ -z "$SERVER_NODE_NAME" ]; then
		if [[ "$HOSTNAME" =~ ^[^.]* ]] ; then
			SERVER_NODE_NAME=${BASH_REMATCH[0]}
		else
			echo "Cannot set SERVER_NODE_NAME since HOSTNAME=$HOSTNAME is not in expected form."
			exit 1
		fi
	fi

	if [ -z $EXPIRY_SECONDS ] ;then
		EXPIRY_SECONDS=180
	fi
}

function checkIsAlive()
{
	local serviceName=nginx
	/sbin/service $serviceName status &> /dev/null
	let retVal=$?
	if [ $retVal -eq "0" ];then
		echo "Service $serviceName is up."
		return
	fi
	echo "Service $serviceName is down."
	exit 0
}

function fillReportData()
{
	OBJDESC="\"serverNode\" : { \"objectType\" : \"KalturaNginxLiveMediaServerNode\" , \
				\"name\" : \"$SERVER_NODE_NAME\"  ,\
				\"hostName\" : \"$HOSTNAME\"  "

	if [ -n "$SERVER_NODE_LOAD_BALANCER_HOSTNAME" ] ;then
		OBJDESC=$OBJDESC", \"playbackDomain\" : \"$SERVER_NODE_LOAD_BALANCER_HOSTNAME\" "
	fi

	OBJDESC="$OBJDESC }"
}

exitIfFail()
{
    errCode="$?"
    if [  $errCode -ne 0 ]; then
        errLog  $errCode "$@"
        exit $errCode
    fi
}

function sendReport()
{
	local reportData="{\
	\"service\" : \"serverNode\", \
	\"action\" : \"reportStatus\", \
	\"hostName\" : \"$HOSTNAME\" , \
	$OBJDESC }"

	RES=`curl -H "Content-Type: application/json" -sv -X POST -d "$reportData" "$SERVICE_URL/api_v3/index.php" --connect-timeout $EXPIRY_SECONDS`
	
	exitIfFail $RES
    echo "Status reported."
}

# main

checkAccess

checkIsAlive

validateInputParameters

fillReportData

sendReport

exit 0
